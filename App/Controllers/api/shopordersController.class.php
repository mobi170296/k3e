<?php 
    namespace App\Controllers\api;
    use Core\Controller;
    
    use Library\Database\DBException;
    use Library\Database\Database;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    
    use App\Models\PaymentTypeModel;
    use App\Models\UserModel;
    use App\Models\OrderLogModel;
    
    use App\Models\GHNTransporterModel;
    use Library\VanChuyen\GHN\GHNRequest;
    use Library\VanChuyen\GHN\GHNException;
    use Library\VanChuyen\GHN\GHNCreateOrderParameter;
    
    use Library\Database\DBDateTime;
    use App\Models\TransporterModel;
    
    use App\Models\OrderModel;
    
    class shopordersController extends Controller{
        public function waitorderslist($page = 1, $ipp = 10){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $start = ($page - 1) * $ipp;
                    
                    $rows = $database->select('id')->from(DB_TABLE_ORDER)->where('shop_id=' . (int)$shop->id . ' and order.status=' . (int) OrderModel::CHO_NGUOI_BAN_XAC_NHAN)->orderby('created_time')->limit($start, $ipp)->execute();
                    
                    $orders = [];
                    foreach($rows as $row){
                        $order = new OrderModel($database);
                        $order->id = $row->id;
                        $order->loadData();
                        $order->loadPaymentType();
                        $order->loadTransporter();
                        $order->loadOrderItems();
                        $order->statusstring = $order->getStatusString();
                        foreach($order->orderitems as $orderitem){
                            $orderitem->loadProduct();
                            $orderitem->product->loadMainImage();
                            $orderitem->product->mainimagethumbnail = $orderitem->product->getMainImageThumbnail();
                        }
                        
                        $orders[] = $order;
                    }
                    
                    $total = $shop->getWaitOrdersTotal();
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->total = $total;
                    $result->body->data = $orders;
                }else{
                    
                }
            }  catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid User';
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function toshiporderslist($page = 1, $ipp = 10){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $start = ($page - 1) * $ipp;
                    
                    $rows = $database->select('id')->from(DB_TABLE_ORDER)->where('shop_id=' . (int)$shop->id . ' and order.status=' . (int) OrderModel::CHO_LAY_HANG)->orderby('created_time')->limit($start, $ipp)->execute();
                    
                    $orders = [];
                    foreach($rows as $row){
                        $order = new OrderModel($database);
                        $order->id = $row->id;
                        $order->loadData();
                        $order->loadPaymentType();
                        $order->loadTransporter();
                        $order->loadTransporterUnit();
                        $order->loadOrderItems();
                        $order->transporterordercode = $order->getTransporterOrderCode();
                        $order->statusstring = $order->getStatusString();
                        foreach($order->orderitems as $orderitem){
                            $orderitem->loadProduct();
                            $orderitem->product->loadMainImage();
                            $orderitem->product->mainimagethumbnail = $orderitem->product->getMainImageThumbnail();
                        }
                        
                        $orders[] = $order;
                    }
                    
                    $total = $shop->getToShipOrdersTotal();
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->total = $total;
                    $result->body->data = $orders;
                }else{
                    
                }
            }  catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid User';
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function shippingorderslist($page = 1, $ipp = 10){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $start = ($page - 1) * $ipp;
                    
                    $rows = $database->select('id')->from(DB_TABLE_ORDER)->where('shop_id=' . (int)$shop->id . ' and order.status=' . (int) OrderModel::DANG_GIAO)->orderby('created_time')->limit($start, $ipp)->execute();
                    
                    $orders = [];
                    foreach($rows as $row){
                        $order = new OrderModel($database);
                        $order->id = $row->id;
                        $order->loadData();
                        $order->loadPaymentType();
                        $order->loadTransporter();
                        $order->loadTransporterUnit();
                        $order->loadOrderItems();
                        $order->transporterordercode = $order->getTransporterOrderCode();
                        $order->statusstring = $order->getStatusString();
                        foreach($order->orderitems as $orderitem){
                            $orderitem->loadProduct();
                            $orderitem->product->loadMainImage();
                            $orderitem->product->mainimagethumbnail = $orderitem->product->getMainImageThumbnail();
                        }
                        
                        $orders[] = $order;
                    }
                    
                    $total = $shop->getShippingOrdersTotal();
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->total = $total;
                    $result->body->data = $orders;
                }else{
                    
                }
            }  catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid User';
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function completedorderslist($page = 1, $ipp = 10){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $start = ($page - 1) * $ipp;
                    
                    $array = [OrderModel::HOAN_TAT, OrderModel::DA_GIAO];
                    $in = '(' . implode(',', $array) . ')';
                    
                    $rows = $database->select('id')->from(DB_TABLE_ORDER)->where('shop_id=' . (int)$shop->id . ' and order.status in ' . $in)->orderby('created_time')->limit($start, $ipp)->execute();
                    
                    $orders = [];
                    foreach($rows as $row){
                        $order = new OrderModel($database);
                        $order->id = $row->id;
                        $order->loadData();
                        $order->loadPaymentType();
                        $order->loadTransporter();
                        $order->loadTransporterUnit();
                        $order->loadOrderItems();
                        $order->transporterordercode = $order->getTransporterOrderCode();
                        $order->statusstring = $order->getStatusString();
                        foreach($order->orderitems as $orderitem){
                            $orderitem->loadProduct();
                            $orderitem->product->loadMainImage();
                            $orderitem->product->mainimagethumbnail = $orderitem->product->getMainImageThumbnail();
                        }
                        
                        $orders[] = $order;
                    }
                    
                    $total = $shop->getCompletedOrdersTotal();
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->total = $total;
                    $result->body->data = $orders;
                }else{
                    
                }
            }  catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid User';
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function cancelledorderslist($page = 1, $ipp = 10){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $start = ($page - 1) * $ipp;

                    $array = [OrderModel::HUY_DON_HANG, OrderModel::KHONG_CON_HANG, OrderModel::HUY_DO_KHONG_LAY_DUOC_HANG, OrderModel::HUY_DO_HE_THONG, OrderModel::GIAO_THAT_BAI];

                    $in = '(' . implode(',', $array) . ')';
                    $rows = $database->select('id')->from(DB_TABLE_ORDER)->where('shop_id=' . (int)$shop->id . ' and order.status in ' . $in)->orderby('created_time')->limit($start, $ipp)->execute();
                    
                    $orders = [];
                    foreach($rows as $row){
                        $order = new OrderModel($database);
                        $order->id = $row->id;
                        $order->loadData();
                        $order->loadPaymentType();
                        $order->loadTransporter();
                        $order->loadOrderItems();
                        $order->statusstring = $order->getStatusString();
                        foreach($order->orderitems as $orderitem){
                            $orderitem->loadProduct();
                            $orderitem->product->loadMainImage();
                            $orderitem->product->mainimagethumbnail = $orderitem->product->getMainImageThumbnail();
                        }
                        
                        $orders[] = $order;
                    }
                    
                    $total = $shop->getCancelledOrdersTotal();
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->total = $total;
                    $result->body->data = $orders;
                }else{
                    
                }
            }  catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid User';
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function cancelorder($order_id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!is_numeric($order_id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $order = new OrderModel($database);
                $order->id = $order_id;
                
                if($user->loadShop() && $order->loadData()){
                    $database->startTransaction();
                    
                    if($order->shopCanCancel()){
                        $order->updateStatus(OrderModel::KHONG_CON_HANG);
                        $orderlog = new OrderLogModel($database);
                        $orderlog->order_id = $order->id;
                        $orderlog->content = $order->getStatusString();
                        $orderlog->order_status = $order->status;

                        $orderlog->add();
                        
                        
                        if($order->paymenttype_id == PaymentTypeModel::ONEPAY && $order->paid == OrderModel::PAID){
                            $buyer = new UserModel($database);
                            $buyer->id = $order->client_id;

                            $buyer->loadData();
                            
                            $buyer->addMoney($order->total_price + $order->ship_fee);
                            $database->commit();
                        }else{
                            $database->commit();
                        }
                        
                        $result->header->code = 0;
                        $result->header->message = 'Đã hủy thành công đơn hàng ' . $order->ordercode;
                    }else{
                        $result->header->code = 1;
                        $result->header->message = 'Đơn hàng không thể hủy';
                    }
                }else{
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid User';
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function readytoship($order_id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!is_numeric($order_id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $order = new OrderModel($database);
                $order->id = $order_id;
                
                if($user->loadShop() && $order->loadData()){
                    $database->startTransaction();
                    
                    if($order->shopCanShip()){
                        //kiem tra so luong san pham o thoi diem hien tai
                        $order->loadOrderItems();
                        foreach($order->orderitems as $orderitem){
                            $orderitem->loadProduct();
                            if($orderitem->quantity > $orderitem->product->getAvailableQuantity()){
                                $database->rollback();
                                $result->header->code = 1;
                                $result->header->message = 'Sản phẩm "' . $orderitem->product->name . '" chỉ còn ' . $orderitem->product->getAvailableQuantity() . ' không đủ ' . $orderitem->quantity . ' đơn vị';
                                
                                return $this->View->RenderJSON($result);
                            }
                        }
                        
                        $order->loadPaymentType();
                        $order->loadTransporter();
                        $order->loadTransporterUnit();
                        $order->loadGHNTransporter();
                        
                        $order->updateStatus(OrderModel::CHO_LAY_HANG);
                        
                        //cap nhat so luong san pham voi so luong cua order item
                        foreach($order->orderitems as $orderitem){
                            $orderitem->product->decreaseQuantity($orderitem->quantity);
                        }
                        
                        $orderlog = new OrderLogModel($database);
                        $orderlog->order_id = $order->id;
                        $orderlog->content = $order->getStatusString();
                        $orderlog->order_status = $order->status;

                        $orderlog->add();
                        
                        if($order->transporter_id == TransporterModel::GHN){
                            $ghntransporter = $order->ghntransporter;
                            
                            $fromd = (int)$ghntransporter->fromdistrictid;
                            $tod = (int)$ghntransporter->todistrictid;
                            $clientName = $order->shopname;
                            $clientPhone = $order->shopphone;
                            $clientAddress = $order->shopaddress;
                            $customerName = $order->clientname;
                            $customerPhone = $order->clientphone;
                            $customerAddress = $order->clientaddress;
                            $serviceId = (int)$ghntransporter->serviceid;
                            $weight = (int)$order->weight;
                            $length = (int)$order->length;
                            $width = (int)$order->width;
                            $height = (int)$order->height;
                            $insuranceFee = 0;
                            
                            if($order->paymenttype_id == PaymentTypeModel::ONEPAY){
                                $codAmount = 0;
                            }else{
                                $codAmount = (int)($order->total_price + $order->ship_fee);
                            }
                            
                            $note = '';
                            
                            $content = '';
                            
                            $ghnrequest = new GHNRequest();
                            $ghnresult = $ghnrequest->createOrder(new GHNCreateOrderParameter($fromd, $tod, $clientName, $clientPhone, $clientAddress, $customerName, $customerPhone, $customerAddress, $serviceId, $weight, $length, $width, $height, $insuranceFee, $codAmount, $note, $content));
                            
                            $ghntransporter->orderid = $ghnresult->OrderID;
                            $ghntransporter->ordercode = $ghnresult->OrderCode;
                            $ghntransporter->currentstatus = $ghnresult->CurrentStatus;
                            $ghntransporter->extrafee = $ghnresult->ExtraFee;
                            $ghntransporter->totalservicefee = $ghnresult->TotalServiceFee;
                            $ghntransporter->expecteddeliverytime = DBDateTime::parseGHNDateTime($ghnresult->ExpectedDeliveryTime);
                            $ghntransporter->note = $note;
                            $ghntransporter->clienthubid = $ghnresult->ClientHubID;
                            $ghntransporter->sortcode = $ghnresult->SortCode;
                            $ghntransporter->paymenttypeid = $ghnresult->PaymentTypeID;
                            
                            $ghntransporter->update($ghntransporter);
                            
                            $database->commit();
                            $result->header->code = 0;
                            $result->header->message = 'Đơn hàng '.$order->ordercode.' đã chuẩn bị thành công, thông tin đơn hàng đã gửi cho ' . $order->transporter->name;
                        }else{
                            $database->rollback();
                            $result->header->code = 1;
                            $result->header->message = 'Đơn vị vận chuyển không tìm thấy';
                        }
                    }else{
                        
                        $database->rollback();
                        $result->header->code = 1;
                        $result->header->message = 'Đơn hàng không thể hủy';
                    }
                }else{
                    
                    $database->rollback();
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                }
            } catch (DBException $ex) {
                
                $database->rollback();
                return $this->View->RenderContent($ex->getMessage() . ' ' . $database->lastquery);
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                
                $database->rollback();
                $result->header->code = 1;
                $result->header->message = 'Invalid User';
                $result->header->errors = ['invalid'];
            } catch (GHNException $e){
                
                $database->rollback();
                $result->header->code = 1;
                $result->header->message = 'GHNException' . $e->getMessage();
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
    }