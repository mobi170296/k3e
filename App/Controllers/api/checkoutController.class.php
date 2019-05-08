<?php
    namespace App\Controllers\api;
    use Core\Controller;
    
    use Library\Database\DBException;
    use Library\Database\Database;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use Library\VanChuyen\GHN\GHNRequest;
    use Library\VanChuyen\GHN\GHNFeeParameter;
    use Library\VanChuyen\GHN\GHNServiceParameter;
    use Library\VanChuyen\GHN\GHNException;
    use App\Models\ProductModel;
    
    use App\Models\OrderModel;
    use App\Models\OrderItemModel;
    use App\Models\OnePayOrderModel;
    use App\Models\TransporterModel;
    
    use App\Exception\InputException;
    
    use App\Models\PaymentTypeModel;
    
    use Library\ThanhToan\OnePay\OnePay;
    use Library\ThanhToan\OnePay\PaymentRequestParameter;
    
    use App\Models\GHNTransporterModel;
    
    use Library\Database\DBDateTime;
    
    use App\Models\DeliveryAddressModel;
    use App\Models\ShopModel;
    use App\Models\OrderLogModel;
    
    use Library\Common\Set;
    
    use Library\Common\Generator;
    
    use App\Models\CartItemModel;
    
    
    class checkoutController extends Controller{
        public function shipfee($shop_id, $product_id, $quantity, $product_price, $deliveryaddress_id, $ghnservice_id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!is_numeric($shop_id) || !is_array($product_id) || !is_array($product_price) || !is_array($quantity) || count($product_id) != count($product_price) || count($product_price) != count($quantity)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            
            try{
                #code = 2 => reload 
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $shop = new ShopModel($database);
                $shop->id = $shop_id;
                
                if(!$shop->loadData()){
                    $result->header->code = 1;
                    $result->header->message = 'Cửa hàng không tồn tại';
                    $result->header->errors = ['product_id' => 'Cửa hàng không tồn tại'];
                    return $this->View->RenderJSON($result);
                }else{
                    //load dia chi cua hang de tinh phi van chuyen
                    $shop->loadWard();
                    $shop->ward->loadDistrict();
                    $shop->ward->district->loadProvince();
                }
                
                if($user->loadShopCartItems($shop_id)){
                    $productidsset = new Set();
                    foreach($user->shopcartitems as $cartitem){
                        $productidsset->add($cartitem->product_id);
                    }
                    
                    if($productidsset->intersect(new Set($product_id))->count() != $productidsset->count()){
                        $result->header->code = 2;
                        $result->header->message = 'Giỏ hàng đã bị thay đổi';
                        return $this->View->RenderJSON($result);
                    }
                }else{
                    #danh dau code thong bao cho phia client
                    $result->header->code = 2;
                    $result->header->message = 'Giỏ hàng hiện tại rỗng không thể tính phí vận chuyện';
                    return $this->View->RenderJSON($result);
                }
                
                #Kiem tra su dong nhat o gio hang
                
                $deliveryaddress = new DeliveryAddressModel($database);
                $deliveryaddress->id = $deliveryaddress_id;
                
                if(!$deliveryaddress->loadData() || $deliveryaddress->user_id != $user->id){
                    $result->header->code = 1;
                    $result->header->message = 'Địa chỉ vận chuyển không tồn tại';
                    $result->header->errors = ['deliveryaddress_id' => 'Địa chỉ vận chuyển không tồn tại'];
                    return $this->View->RenderJSON($result);
                }else{
                    $deliveryaddress->loadWard();
                    $deliveryaddress->ward->loadDistrict();
                }
                
                
                $diffprice = false;
                $diffquantity = false;
                
                $cartitems = [];
                $products = [];
                for($i = 0; $i < count($product_id); $i++){
                    $pid = $product_id[$i];
                    $qty = $quantity[$i];
                    $pprice = $product_price[$i];
                    
                    $product = new ProductModel($database);
                    $product->id = $pid;
                    if(!$product->loadData()){
                        $result->header->code = 2;
                        $result->header->message = 'Sản phẩm không tồn tại';
                        $result->header->errors = ['product_id' => 'Sản phẩm không tồn tại'];
                        return $this->View->RenderJSON($result);
                    }
                    
                    $cartitem = new CartItemModel($database);
                    $cartitem->product_id = $pid;
                    $cartitem->client_id = $user->id;
                    
                    if(!$cartitem->loadData()){
                        $result->header->code = 2;
                        $result->header->message = 'Sản phẩm không tồn tại';
                        $result->header->errors = ['product_id' => 'Sản phẩm không tồn tại'];
                        return $this->View->RenderJSON($result);
                    }
                    
                    if($cartitem->quantity != $qty){
                        $result->header->code = 2;
                        $result->header->message = 'Giỏ hàng không đồng nhất';
                        $result->header->errors = ['product_id' => 'Sản phẩm không tồn tại'];
                        return $this->View->RenderJSON($result);
                    }
                    
                    if($product->getAvailableQuantity() < $cartitem->quantity){
                        if($product->getAvailableQuantity() == 0){
                            $cartitem->delete();
                        }else{
                            $cartitem->updateQuantity($product->getAvailableQuantity());
                        }
                        $diffquantity = true;
                    }
                    
                    if($pprice != $product->getSalePrice()){
                        $diffprice = true;
                    }
                    
                    $products[] = $product;
                    $cartitems[] = $cartitem;
                }
                
                if($diffprice || $diffquantity){
                    #khong dong nhat gia va so luong so voi csdl
                    $result->header->code = 2;
                    $result->header->message = 'Giỏ hàng không đồng nhất giá';
                }else{
                    $totalweight = 0;
                    $totalvolume = 0;
                    for($i=0; $i<count($products); $i++){
                        $product = $products[$i];
                        $cartitem = $cartitems[$i];
                        $totalweight += $product->weight * $cartitem->quantity;
                        $totalvolume += ($product->width * $product->height * $product->length) * $cartitem->quantity;
                    }
                    $avgvolume = (int)\pow($totalvolume, 1/3);
                    
                    $ghn = new GHNRequest();
                    $exists = $ghn->hasServiceId(new GHNServiceParameter((int)$shop->ward->district->ghn_district_id, (int)$deliveryaddress->ward->district->ghn_district_id, $totalweight, $avgvolume, $avgvolume, $avgvolume), (int)$ghnservice_id);
                    
                    if($exists){
                        $fee = $ghn->calculateFee(new GHNFeeParameter((int)$shop->ward->district->ghn_district_id, (int)$deliveryaddress->ward->district->ghn_district_id, (int)$ghnservice_id, $totalweight, $avgvolume, $avgvolume, $avgvolume, 0));
                        $result->header->code = 0;
                        $result->header->message = 'Success';
                        $result->body = new \stdClass();
                        $result->body->data = new \stdClass();
                        $result->body->data->fee = $fee;
                    }else{
                        //serviceid khong ton tai
                        $result->header->code = 3;
                        $result->header->message = 'Không tồn tại service này';
                    }
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = ['DBERR'];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'user invalid';
                $result->header->errors = ['invalid'];
            } catch (GHNException $e){
                $result->header->code = 1;
                $result->header->message = 'Không thể tính phí vận chuyển vui lòng thử lại';
                $result->header->errors = ['ghn' => 'Không thể tính phí vận chuyển'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        
        public function servicelist($shop_id, $product_id, $quantity, $product_price, $deliveryaddress_id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!is_numeric($shop_id) || !is_array($product_id) || !is_array($product_price) || !is_array($quantity) || count($product_id) != count($product_price) || count($product_price) != count($quantity)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            
            try{
                #code = 2 => reload 
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $shop = new ShopModel($database);
                $shop->id = $shop_id;
                
                if(!$shop->loadData()){
                    $result->header->code = 1;
                    $result->header->message = 'Cửa hàng không tồn tại';
                    $result->header->errors = ['product_id' => 'Cửa hàng không tồn tại'];
                    return $this->View->RenderJSON($result);
                }else{
                    //load dia chi cua hang de tinh phi van chuyen
                    $shop->loadWard();
                    $shop->ward->loadDistrict();
                    $shop->ward->district->loadProvince();
                }
                
                if($user->loadShopCartItems($shop_id)){
                    $productidsset = new Set();
                    foreach($user->shopcartitems as $cartitem){
                        $productidsset->add($cartitem->product_id);
                    }
                    
                    if($productidsset->intersect(new Set($product_id))->count() != $productidsset->count()){
                        $result->header->code = 2;
                        $result->header->message = 'Giỏ hàng đã bị thay đổi';
                        return $this->View->RenderJSON($result);
                    }
                }else{
                    #danh dau code thong bao cho phia client
                    $result->header->code = 2;
                    $result->header->message = 'Giỏ hàng hiện tại rỗng không thể tính phí vận chuyện';
                    return $this->View->RenderJSON($result);
                }
                
                #Kiem tra su dong nhat o gio hang
                
                $deliveryaddress = new DeliveryAddressModel($database);
                $deliveryaddress->id = $deliveryaddress_id;
                
                if(!$deliveryaddress->loadData() || $deliveryaddress->user_id != $user->id){
                    $result->header->code = 1;
                    $result->header->message = 'Địa chỉ vận chuyển không tồn tại';
                    $result->header->errors = ['deliveryaddress_id' => 'Địa chỉ vận chuyển không tồn tại'];
                    return $this->View->RenderJSON($result);
                }else{
                    $deliveryaddress->loadWard();
                    $deliveryaddress->ward->loadDistrict();
                }
                
                
                $diffprice = false;
                $diffquantity = false;
                
                $cartitems = [];
                $products = [];
                for($i = 0; $i < count($product_id); $i++){
                    $pid = $product_id[$i];
                    $qty = $quantity[$i];
                    $pprice = $product_price[$i];
                    
                    $product = new ProductModel($database);
                    $product->id = $pid;
                    if(!$product->loadData()){
                        $result->header->code = 2;
                        $result->header->message = 'Sản phẩm không tồn tại';
                        $result->header->errors = ['product_id' => 'Sản phẩm không tồn tại'];
                        return $this->View->RenderJSON($result);
                    }
                    
                    $cartitem = new CartItemModel($database);
                    $cartitem->product_id = $pid;
                    $cartitem->client_id = $user->id;
                    
                    if(!$cartitem->loadData()){
                        $result->header->code = 2;
                        $result->header->message = 'Sản phẩm không tồn tại';
                        $result->header->errors = ['product_id' => 'Sản phẩm không tồn tại'];
                        return $this->View->RenderJSON($result);
                    }
                    
                    if($cartitem->quantity != $qty){
                        $result->header->code = 2;
                        $result->header->message = 'Giỏ hàng không đồng nhất';
                        $result->header->errors = ['product_id' => 'Sản phẩm không tồn tại'];
                        return $this->View->RenderJSON($result);
                    }
                    
                    if($product->getAvailableQuantity() < $cartitem->quantity){
                        if($product->getAvailableQuantity() == 0){
                            $cartitem->delete();
                        }else{
                            $cartitem->updateQuantity($product->getAvailableQuantity());
                        }
                        $diffquantity = true;
                    }
                    
                    if($pprice != $product->getSalePrice()){
                        $diffprice = true;
                    }
                    
                    $products[] = $product;
                    $cartitems[] = $cartitem;
                }
                
                if($diffprice || $diffquantity){
                    #khong dong nhat gia va so luong so voi csdl
                    $result->header->code = 2;
                    $result->header->message = 'Giỏ hàng không đồng nhất giá';
                }else{
                    $totalweight = 0;
                    $totalvolume = 0;
                    for($i=0; $i<count($products); $i++){
                        $product = $products[$i];
                        $cartitem = $cartitems[$i];
                        $totalweight += $product->weight * $cartitem->quantity;
                        $totalvolume += ($product->width * $product->height * $product->length) * $cartitem->quantity;
                    }
                    $avgvolume = (int)\pow($totalvolume, 1/3);
                    
                    $ghn = new GHNRequest();
                    $services = $ghn->getServices(new GHNServiceParameter((int)$shop->ward->district->ghn_district_id, (int)$deliveryaddress->ward->district->ghn_district_id, $totalweight, $avgvolume, $avgvolume, $avgvolume));
                    
                    foreach($services as $service){
                        $service->ExpectedDeliveryTime = DBDateTime::parseGHNDateTime($service->ExpectedDeliveryTime)->toLocalDateTime();
                    }
                    
                    $fee = $ghn->calculateFee(new GHNFeeParameter((int)$shop->ward->district->ghn_district_id, (int)$deliveryaddress->ward->district->ghn_district_id, $services[0]->ServiceID, $totalweight, $avgvolume, $avgvolume, $avgvolume, 0));
                    
                    
                    $result->header->code = 0;
                    $result->header->message = 'Success';
                    $result->body = new \stdClass();
                    $result->body->data = new \stdClass();
                    $result->body->data->services = $services;
                    $result->body->data->fee = $fee;
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = ['DBERR'];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'user invalid';
                $result->header->errors = ['invalid'];
            } catch (GHNException $e){
                $result->header->code = 1;
                $result->header->message = 'Không thể tính phí vận chuyển vui lòng thử lại';
                $result->header->errors = ['ghn' => 'Không thể tính phí vận chuyển'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function checkout($shop_id, $product_id, $quantity, $product_price, $deliveryaddress_id, $ghnservice_id, $paymenttype_id, $note){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!is_numeric($shop_id) || !is_array($product_id) || !is_array($product_price) || !is_numeric($deliveryaddress_id) || !is_numeric($ghnservice_id) || count($product_id) != count($product_price) || count($product_price) != count($quantity)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            #kiem tra su dong nhat voi phia user
            
            try{
                $database = new Database();
                $database->startTransaction();
                $user = (new Authenticate($database))->getUser();
                
                $shop = new ShopModel($database);
                $shop->id = $shop_id;
                if(!$shop->loadData()){
                    $result->header->code = 1;
                    $result->header->message = 'Đơn hàng không tồn tại!';
                    return $this->View->RenderJSON($result);
                }else{
                    $shop->loadOwner();
                    $shop->owner->loadDefaultDeliveryAddress();
                    $shopdeliveryaddress = $shop->owner->defaultdeliveryaddress;
                }
                
                $paymenttype = new PaymentTypeModel($database);
                $paymenttype->id = $paymenttype_id;
                if(!$paymenttype->loadData()){
                    $result->header->code = 1;
                    $result->header->message = 'Phương thức thanh toán không tồn tại';
                    return $this->View->RenderJSON($result);
                }
                
                $deliveryaddress = new DeliveryAddressModel($database);
                $deliveryaddress->id = $deliveryaddress_id;
                if(!$deliveryaddress->loadData() || $deliveryaddress->user_id != $user->id){
                    $result->header->code = 1;
                    $result->header->message = 'Địa chỉ vận chuyển đến không tồn tại';
                    return $this->View->RenderJSON($result);
                }else{
                    $deliveryaddress->loadWard();
                    $deliveryaddress->ward->loadDistrict();
                    $deliveryaddress->ward->district->loadProvince();
                }
                
                if($user->loadShopCartItems($shop_id)){
                    $shopcartitems = $user->shopcartitems;
                    #check trong gio hang truoc
                    if(count($shopcartitems) == count($product_id)){
                        $cartitems = [];
                        foreach($product_id as $pid){
                            $cartitem = new CartItemModel($database);
                            $cartitem->product_id = $pid;
                            $cartitem->client_id = $user->id;
                            if(!$cartitem->loadData()){
                                $result->header->code = 2;
                                $result->header->message = 'Giỏ hàng không đồng nhất';
                                return $this->View->RenderJSON($result);
                            }
                            $cartitem->loadProduct();
                            
                            $cartitems[] = $cartitem;
                        }
                        
                        #kiem tra su dong + tinh weight + volume
                        $totalweight = 0;
                        $totalvolume = 0;
                        $totalprice = 0;
                        for($i=0; $i<count($product_id); $i++){
                            $pid = $product_id[$i];
                            $qty = $quantity[$i];
                            $pprice = $product_price[$i];
                            $cartitem = $cartitems[$i];
                            
                            if($qty > $cartitem->product->getAvailableQuantity()){
                                $result->header->code = 2;
                                $result->header->message = 'Giỏ hàng có sản phẩm vượt quá số lượng nhà cung cấp';
                                return $this->View->RenderJSON($result);
                            }
                            
                            if($pprice != $cartitem->product->getSalePrice()){
                                $result->header->code = 2;
                                $result->header->message = 'Giỏ hàng có sản phẩm có giá đã thay đổi';
                                return $this->View->RenderJSON($result);
                            }
                            
                            $totalprice += $cartitem->product->getSalePrice() * $cartitem->quantity;
                            $totalweight += $cartitem->product->weight * $cartitem->quantity;
                            $totalvolume += ($cartitem->product->length * $cartitem->product->width * $cartitem->product->height) * $cartitem->quantity;
                        }
                        
                        $avglength = (int)\pow($totalvolume, 1/3);
                        
                        $ghn = new GHNRequest();
                        if($service = $ghn->getService(new GHNServiceParameter((int)$shop->owner->defaultdeliveryaddress->ward->district->ghn_district_id, (int)$deliveryaddress->ward->district->ghn_district_id, $totalweight, $totalweight, $avglength, $avglength), $ghnservice_id)){
                            #tinh truoc phi van chuyen
                            $fee = $ghn->calculateFee(new GHNFeeParameter((int)$shop->owner->defaultdeliveryaddress->ward->district->ghn_district_id, (int)$deliveryaddress->ward->district->ghn_district_id, (int)$ghnservice_id, $totalweight, $avglength, $avglength, $avglength, 0));
                            
                            #THOA HET YEU CAU CHECKOUT TAI DAY
                            
                            $order = new OrderModel($database);
                            
                            
                            $try = 0;
                            do{
                                $order->ordercode = Generator::orderCode();
                                $try++;
                            }while($try < 3 && $order->loadFromOrderCode());
                            
                            if($try >= 5){
                                #vuot qua so lan thu cho viec chon ma don hang
                                $result->header->code = 1;
                                $result->header->message = 'Hiện tại không thể tạo đơn hàng vui lòng thử lại sau';
                                return $this->View->RenderJSON($result);
                            }
                            
                            #cap phep xong ma don hang -> khoi tao thong tin don hang
                            $order->shop_id = $shop->id;
                            $order->client_id = $user->id;
                            $order->status = OrderModel::DON_HANG_DUOC_TAO;
                            $order->note = $note;
                            $order->total_price = $totalprice;
                            $order->ship_fee = $fee->CalculatedFee;
                            $order->clientname = $deliveryaddress->lastname. ' ' . $deliveryaddress->firstname;
                            $order->clientphone = $deliveryaddress->phone;
                            $order->clientaddress = $deliveryaddress->address;
                            $order->clientdistrictname = $deliveryaddress->ward->district->name;
                            $order->clientwardname = $deliveryaddress->ward->name;
                            $order->clientprovincename = $deliveryaddress->ward->district->province->name;
                            $order->shopname = $shopdeliveryaddress->lastname . ' ' . $shopdeliveryaddress->firstname;
                            $order->shopphone = $shopdeliveryaddress->phone;
                            $order->shopaddress = $shopdeliveryaddress->address;
                            $order->shopwardname = $shopdeliveryaddress->ward->name;
                            $order->shopdistrictname = $shopdeliveryaddress->ward->district->name;
                            $order->shopprovincename = $shopdeliveryaddress->ward->district->province->name;
                            $order->weight = $totalweight;
                            $order->length = $order->width = $order->height = $avglength;
                            
                            $order->paymenttype_id = $paymenttype_id;
                            
                            $order->transporter_id = TransporterModel::GHN;
                            
                            $order->checkNote();
                            
                            if(!$order->isValid()){
                                throw new InputException($order->getErrorsMap());
                            }
                            
                            if($paymenttype_id == PaymentTypeModel::COD){
                                #thanh toan cod
                                $order->paid = OrderModel::UNPAID;
                                $order->paycomplete = OrderModel::PAYCOMPLETE;
                            }else{
                                #thanh toan truc tuyen qua onepay
                                $order->paid = OrderModel::UNPAID;
                                $order->paycomplete = OrderModel::PAYINCOMPLETE;
                            }
                            
                            #them order vao db truoc, lay duoc order.id
                            $order->add();
                            
                            #them orderlog dau tien
                            $orderlog = new OrderLogModel($database);
                            $orderlog->content = $order->getStatusString();
                            $orderlog->order_id = $order->id;
                            $orderlog->order_status = $order->status;
                            $orderlog->add();
                            
                            #phuong thuc van chuyen
                            $ghntransporter = new GHNTransporterModel($database);
                            $ghntransporter->order_id = $order->id;
                            $ghntransporter->serviceid = $ghnservice_id;
                            $ghntransporter->servicename = $service->Name;
                            $ghntransporter->insurancefee = 0;
                            $ghntransporter->fromdistrictid = $shopdeliveryaddress->ward->district->ghn_district_id;
                            $ghntransporter->fromwardcode = $shopdeliveryaddress->ward->ghn_ward_code;
                            $ghntransporter->todistrictid = $deliveryaddress->ward->district->ghn_district_id;
                            $ghntransporter->towardcode = $deliveryaddress->ward->ghn_ward_code;
                            
                            if($paymenttype_id == PaymentTypeModel::COD){
                                $ghntransporter->codamount = $order->ship_fee + $order->total_price;
                            }else{
                                $ghntransporter->codamount = 0;
                            }
                            $ghntransporter->add();
                            
                            #phuong thuc thanh toan
                            if($paymenttype_id == PaymentTypeModel::COD){
                                #thanh toan khi nhan hang chuyen ngay sang trang thai cho nguoi ban xac nhan
                                $order->updateStatus(OrderModel::CHO_NGUOI_BAN_XAC_NHAN);
                                
                                $orderlog = new OrderLogModel($database);
                                $orderlog->order_id = $order->id;
                                $orderlog->content = $order->getStatusString();
                                $orderlog->order_status = $order->status;
                                $orderlog->add();
                            }else{
                                $order->updateStatus(OrderModel::NGUOI_MUA_DANG_THANH_TOAN);
                                
                                $orderlog = new OrderLogModel($database);
                                $orderlog->order_id = $order->id;
                                $orderlog->content = $order->getStatusString();
                                $orderlog->order_status = $order->status;
                                $orderlog->add();
                                
                                $onepayorder = new OnePayOrderModel($database);
                                $try = 0;
                                do{
                                    $onepayorder->transactionref = Generator::transactionReference();
                                    $try++;
                                }while($try < 5 && $onepayorder->loadFromTransactionRef());
                                
                                if($try >= 5){
                                    $result->header->code = 1;
                                    $result->header->message = 'Không thể thực hiện thanh toán vui lòng thử lại sau';
                                    return $this->View->RenderJSON($result);
                                }
                                
                                $onepayorder->order_id = $order->id;
                                $onepayorder->orderinfo = $order->ordercode;
                                $onepayorder->currencycode = OnePay::CURRENCYCODE;
                                $onepayorder->amount = (int)($totalprice * 100);
                                $onepayorder->merchant = OnePay::MERCHANT;
                                $onepayorder->accesscode = OnePay::ACCESSCODE;
                                $onepayorder->ticketno = $_SERVER['REMOTE_ADDR'];
                                
                                $onepayorder->add();
                            }
                            
                            $database->commit();
                            return $this->View->RenderCONTENT('ORDER ID ' . $order->id);
                        }else{
                            $result->header->code = 1;
                            $result->header->message = 'Dịch vụ vận chuyển không tồn tại';
                        }
                    }else{
                        $result->header->code = 2;
                        $result->header->message = 'Không thể thanh toán giỏ hàng không đồng nhất';
                    }
                }else{
                    $result->header->code = 1;
                    $result->header->message = 'Không có sản phẩm trong giỏ hàng không thể thanh toán';
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR' . $ex->getMessage();
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'invalid user';
            } catch (GHNException $e){
                $result->header->code = 1;
                $result->header->message = 'Đã có sự cố với máy chủ, vui lòng thử lại sau!';
            } catch (InputException $e){
                $result->header->code = 1;
                $result->header->message = $e->getMessage();
                $result->header->errors = $e->getErrorsMap();
            }
            
            return $this->View->RenderJSON($result);
        }
    }