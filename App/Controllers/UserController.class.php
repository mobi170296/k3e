<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\UserModel;
    use Library\Database\DBException;
    use Library\Database\Database;
    use App\Exception\InputException;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use Library\Database\DBDateTime;
    use App\Models\ProvinceList;
    use App\Models\DeliveryAddressModel;
    use App\Models\DistrictList;
    use App\Models\WardList;
    
    use Library\VanChuyen\GHN\GHNRequest;
    use Library\VanChuyen\GHN\GHNServiceParameter;
    use Library\VanChuyen\GHN\GHNServiceResult;
    use Library\VanChuyen\GHN\GHNFeeParameter;
    use Library\VanChuyen\GHN\GHNFeeResult;
    use Library\VanChuyen\GHN\GHNException;
    
    use App\Models\Pagination;
    
    use App\Models\OrderModel;
    use Library\ThanhToan\OnePay\OnePay;
    use App\Models\OnePayOrderModel;
    use App\Models\OrderLogModel;
    use Library\ThanhToan\OnePay\OnePayException;
    use App\Models\PaymentTypeModel;
    
    class UserController extends Controller{
        public function Index(){
            return $this->redirectToAction('Info', 'User');
        }
        
        public function Register($register, $day, $month, $year, UserModel $input){
            try{
                $database = new Database();
                new Authenticate($database);
                return $this->redirectToAction('Index', 'Home');
            } catch (AuthenticateException $ex) {
                #XAC THUC THAT BAI CHO PHEP TRUY CAP DANG KY
            } catch (DBException $e){
                $this->View->Data->ErrorMessage = $e->getMessage();
                return $this->View->RenderTemplate('_error');
            }
            
            if($register != null && $this->isPOST()){
                #SEND ACTION
                try{
                    $input->setDatabase($database);
                    $input->birthday = new DBDateTime($day, $month, $year);
                    $input->checkValidForUserName()->checkValidForUserNameExists()->checkValidForPassword($input->password[0], $input->password[1])->checkValidForEmail()->checkValidForPhone()->checkValidForBirthday()->checkValidForGender()->checkValidForLastName()->checkValidForFirstName();
                    
                    if($input->getErrorsLength()){
                        $this->View->Data->Model = $input;
                        throw new InputException($input->getErrorsMap());
                    }
                    
                    $input->password = $input->password[0];
                    $input->register();
                    
                    $_SESSION['username'] = $input->username;
                    $_SESSION['password'] = $input->password;
                    
                    return $this->redirectToAction('Index', 'Home');
                } catch (DBException $ex) {
                    $this->View->Data->ErrorMessage = $ex->getMessage();
                    return $this->View->RenderTemplate("_error");
                } catch(InputException $ex){
                    $this->View->Data->ErrorsMap = $ex->getErrorsMap();
                    return $this->View->RenderTemplate();
                }
            }else{
                return $this->View->RenderTemplate();
            }
        }
        public function Login($login, $username, $password, $backurl){
            try{
                $database = new Database();
                new Authenticate($database);
                return $this->redirectToAction('Index', 'Home');
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate("_error");
            } catch (AuthenticateException $e){
                #normal access 
            }
            
            try{
                if($login && $this->isPOST()){
                    $input = new UserModel($database);
                    $input->username = $username;
                    $input->password = $password;
                    $input->checkValidForUserName()->checkValidForPassword();
                    
                    if($input->getErrorsLength()){
                        throw new InputException($input->getErrorsMap());
                    }
                    
                    $input->standardization();
                    if($input->login()){
                        if($input->isLocked()){
                            $this->View->Data->ErrorMessage = 'Tài khoản của bạn đã bị khóa, vui lòng liên hệ với quản trị viên để được trợ giúp';
                            return $this->View->RenderTemplate();
                        }else{
                            $_SESSION['username'] = $username;
                            $_SESSION['password'] = $password;
                            if(is_string($backurl)){
                                header('location: ' . $backurl);
                            }else{
                                
                                return $this->redirectToAction('Index', 'Home');
                            }
                        }
                    }else{
                        $this->View->Data->ErrorMessage = 'Tên đăng nhập hoặc tài khoản không đúng';
                        throw new InputException(null);
                    }
                }else{
                    return $this->View->RenderTemplate();
                }
            } catch (InputException $ex) {
                $this->View->Data->ErrorsMap = $ex->getErrorsMap();
                return $this->View->RenderTemplate();
            } catch (DBException $e){
                $this->View->Data->ErrorMessage = $e->getMessage();
                return $this->View->RenderTemplate("_error");
            }
        }
        public function Info($update, $day, $month, $year, UserModel $input){
            if($update!== null && !$this->isPOST()){
                return $this->redirectToAction('Index', 'Home');
            }
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                $this->View->Data->user = $user;
                if($update!==null){
                    $input->birthday = new DBDateTime($day, $month, $year);
                    $input->setDatabase($database);
                    $input->checkValidForBirthday()->checkValidForFirstName()->checkValidForLastName()->checkValidForGender();
                    if($input->isValid()){
                        $input->standardization();
                        $user->update($input);
                        $this->View->Data->SuccessMessage = 'Bạn đã cập nhật thông tin thành công';
                        $this->View->RenderTemplate();
                    }else{
                        throw new InputException($input->getErrorsMap());
                    }
                }
                return $this->View->RenderTemplate();
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            } catch(AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            } catch(InputException $e){
                $this->View->Data->ErrorsMap = $e->getErrorsMap();
                return $this->View->RenderTemplate();
            }
        }
        public function Logout(){
            unset($_SESSION['username']);
            unset($_SESSION['password']);
            $this->redirectToAction('Index', 'Home');
        }
        public function ChangePassword(){
            try{
                $database = new Database();
                new Authenticate($database);
                return $this->View->RenderTemplate();
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Index', 'Home');
            }
        }
        public function Order($ordercode){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $order = new OrderModel($database);
                $order->ordercode = $ordercode;
                
                if($order->loadFromOrderCode()){
                    $order->loadTransporter();
                    $order->loadTransporterUnit();
                    $order->loadOrderLogs();
                    $order->loadPaymentType();
                    $order->loadShop();
                    $order->loadAssessments();
                    foreach($order->assessments as $assessment){
                        $assessment->loadProduct();
                        $assessment->product->loadMainImage();
                    }
                    
                    $order->loadOrderItems();
                    foreach($order->orderitems as $orderitem){
                        $orderitem->loadProduct();
                        $orderitem->product->loadMainImage();
                    }
                    
                    $this->View->Data->order = $order;
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Orders', 'User');
                }
            } catch (DBException $ex) {
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        public function Orders(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $user->loadOrders();
                $orders =  $user->orders;
                
                foreach($orders as $order){
                    $order->loadPaymentType();
                    $order->loadOrderItems();
                    $order->orderitems[0]->loadProduct();
                    $order->orderitems[0]->product->loadMainImage();
                }
                
                $this->View->Data->orders = $orders;
                
                return $this->View->RenderTemplate();
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        public function DeliveryAddresses(){
            try{
                $database = new Database;
                $user = (new Authenticate($database))->getUser();
                $user->loadDeliveryAddresses();
                $this->View->Data->deliveryaddresses = $user->deliveryaddresses;
                
                return $this->View->RenderTemplate();
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        public function AddDeliveryAddress($add, $province_id, $district_id, DeliveryAddressModel $input){
            try{
                $database = new Database;
                $user = (new Authenticate($database))->getUser();
                #Tổng số địa chỉ trong sổ địa chỉ
                $this->View->Data->total = $user->getDeliveryAddressesTotal();
                #Danh sách tỉnh cho người dùng chọn đầu tiên
                $this->View->Data->provincelist = (new ProvinceList($database))->getAll();
                
                if($add!=null){
                    $this->View->Data->province_id = $province_id;
                    $this->View->Data->districtlist = (new DistrictList($database))->getAllFromProvince((int)$province_id);
                    $this->View->Data->district_id = $district_id;
                    $this->View->Data->wardlist = (new WardList($database))->getAllFromDistrict($district_id);
                    $this->View->Data->ward_id = $input->ward_id;
                }else{
                    $this->View->Data->districtlist = (new DistrictList($database))->getAllFromProvince($this->View->Data->provincelist[0]->id);
                    $this->View->Data->wardlist = (new WardList($database))->getAllFromDistrict($this->View->Data->districtlist[0]->id);
                }
                
                if($add!=null){
                    #request update
                    $this->View->Data->input = $input;
                    $input->def = $input->def == 0 ? 0 : 1;
                    $input->setDatabase($database);
                    $input->checkValidForAddress()->checkValidForFirstName()->checkValidForLastName()->checkValidForPhone()->checkValidForWardId();
                    
                    if($input->isValid()){
                        $input->user_id = $user->id;
                        $input->add();
                        return $this->redirectToAction('DeliveryAddresses', 'User');
                    }else{
                        $this->View->Data->input = $input;
                        throw new InputException($input->getErrorsMap());
                    }
                }else{
                    #request form
                    return $this->View->RenderTemplate();
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            } catch(InputException $e){
                $this->View->Data->ErrorsMap = $e->getErrorsMap();
                return $this->View->RenderTemplate();
            }
        }
        public function UpdateDeliveryAddress($update, $district_id, $province_id, DeliveryAddressModel $input){
            if(!is_numeric($input->id)){
                $this->View->Data->ErrorMessage = 'invalid';
                return $this->View->RenderTemplate('_error');
            }
            try{
                $database = new Database;
                $user = (new Authenticate($database))->getUser();
                
                $input->def = $input->def == 0 ? 0 : 1;
                
                $deliveryaddress = new DeliveryAddressModel($database);
                $deliveryaddress->id = $input->id;
                
                if($deliveryaddress->loadData() && $deliveryaddress->user_id == $user->id){
                    $this->View->Data->deliveryaddress = $deliveryaddress;
                    $deliveryaddress->loadWard();
                    $deliveryaddress->ward->loadDistrict();
                    $deliveryaddress->ward->district->loadProvince();
                    
                    $this->View->Data->provincelist = (new ProvinceList($database))->getAll();
                    $this->View->Data->districtlist = (new DistrictList($database))->getAllFromProvince($deliveryaddress->ward->district->province_id);
                    $this->View->Data->wardlist = (new WardList($database))->getAllFromDistrict($deliveryaddress->ward->district->id);
                    
                    if($update!==null){
                        #request update
                        if($deliveryaddress->def == 1){
                            $input->def = 1;
                        }
                        
                        $deliveryaddress->lastname = $input->lastname;
                        $deliveryaddress->firstname = $input->firstname;
                        $deliveryaddress->address = $input->address;
                        $deliveryaddress->phone  = $input->phone;
                        
                        $input->checkValidForAddress()->checkValidForFirstName()->checkValidForLastName()->checkValidForPhone();
                        if($input->isValid()){
                            $deliveryaddress->update($input);
                            $this->View->Data->SuccessMessage = 'Đã cập nhật địa chỉ thành công';
                            $user->loadDeliveryAddresses();
                            $this->View->Data->deliveryaddresses = $user->deliveryaddresses;
                            return $this->View->RenderTemplate('DeliveryAddresses', 'User');
                        }else{
                            $this->View->Data->province_id = $province_id;
                            $this->View->Data->district_id = $district_id;
                            $this->View->Data->ward_id = $input->ward_id;
                            throw new InputException($input->getErrorsMap());
                        }
                    } else {
                        #get form
                        $this->View->Data->province_id = $deliveryaddress->ward->district->province_id;
                        $this->View->Data->district_id = $deliveryaddress->ward->district_id;
                        $this->View->Data->ward_id = $deliveryaddress->ward_id;
                        return $this->View->RenderTemplate();
                    }
                }else{
                    return $this->redirectToAction('DeliveryAddresses', 'User');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            } catch (InputException $ex){
                $this->View->Data->ErrorsMap = $ex->getErrorsMap();
                return $this->View->RenderTemplate();
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        public function DeleteDeliveryAddress($delete, $id){
            if(!is_numeric($id)){
                $this->View->ErrorMessage = 'Trang này không tìm thấy';
                return $this->View->RenderTemplate('_error');
            }
            try{
                $database = new Database;
                $user = (new Authenticate($database))->getUser();
                $deliveryaddress = new DeliveryAddressModel($database);
                $deliveryaddress->id = $id;
                if($deliveryaddress->loadData() && $deliveryaddress->user_id == $user->id){
                    $this->View->Data->deliveryaddress = $deliveryaddress;
                    $deliveryaddress->loadWard();
                    $deliveryaddress->ward->loadDistrict();
                    $deliveryaddress->ward->district->loadProvince();
                    if($deliveryaddress->def==1){
                        return $this->redirectToAction('DeliveryAddresses', 'User');
                    }
                    if($delete!=null){
                        $deliveryaddress->delete();
                        $this->View->Data->SuccessMessage = 'Đã xóa địa chỉ giao hàng thành công';
                        $user->loadDeliveryAddresses();
                        $this->View->Data->deliveryaddresses = $user->deliveryaddresses;
                        return $this->View->RenderTemplate('DeliveryAddresses', 'User');
                    }else{
                        return $this->View->RenderTemplate();
                    }
                }else{
                    return $this->redirectToAction('DeliveryAddresses', 'User');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            } catch (InputException $e){
                $this->View->Data->ErrorsMap = $e->getErrorsMap();
                return $this->View->RenderTemplate();
            }
        }
        
        public function Cart(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $this->View->Data->cart = [];
                
                $cartgroup = [];
                
                if($user->loadCartItems()){
                    foreach($user->cartitems as $cartitem){
                        $cartitem->loadProduct();
                        $cartitem->product->loadMainImage();
                        
                        if(!isset($cartgroup[$cartitem->product->shop_id])){
                            $cartgroup[$cartitem->product->shop_id] = new \stdClass();
                            $cartitem->product->loadShop();
                            $cartgroup[$cartitem->product->shop_id]->shop = $cartitem->product->shop;
                            $cartitem->product->shop->loadWard();
                            $cartitem->product->shop->ward->loadDistrict();
                            $cartitem->product->shop->ward->district->loadProvince();
                            $cartgroup[$cartitem->product->shop_id]->items = [];
                        }
                        
                        $item = new \stdClass();
                        $item->product = $cartitem->product;
                        $item->quantity = $cartitem->quantity;
                        $item->subtotal = $item->product->getSalePrice() * $item->quantity;
                        
                        $cartgroup[$cartitem->product->shop_id]->items[] = $item;
                    }
                }
                
                foreach($cartgroup as $group){
                    $this->View->Data->cart[] = $group;
                }
                
                return $this->View->RenderTemplate();
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DB_ERROR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User', ['backurl' => '/User/Cart']);
            }
        }
        
        public function Checkout($shop_id){
            try{
                if(!is_numeric($shop_id)){
                    return $this->redirectToAction('Index', 'Home');
                }
                
                $database = new Database;
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->loadDefaultDeliveryAddress()){
                    return $this->View->RenderTemplate('RequireAddress');
                }
                
                $user->loadCartItems();
                
                $items = [];
                
                if(count($user->cartitems)){
                    //load product vao cartitem de check shop
                    foreach($user->cartitems as $cartitem){
                        $cartitem->loadProduct();
                        $cartitem->product->loadMainImage();
                        if($cartitem->product->shop_id == $shop_id){
                            $items[] = $cartitem;
                        }
                    }
                    
                    #ton tai items o cua hang duoc chi dinh thanh toan
                    if(count($items)){
                        //load thong tin van chuyen cua shop
                        $items[0]->product->loadShop();
                        $shop = $items[0]->product->shop;
                        $items[0]->product->shop->loadWard();
                        $items[0]->product->shop->ward->loadDistrict();
                        
                        //ton tai san pham thuoc ve cua hang bay gio kiem tra tinh hop le cua tung san pham
                        //load deliveryaddress cua user
                        //tinh phi van chuyen cho don hang
                        //hien thi thong tin thanh toan cho don hang
                        $user->loadDeliveryAddresses();
                        $user->loadDefaultDeliveryAddress();
                        
                        #tinh tong weight, width, length, height
                        #chi phi van chuyen
                        $totalweight = $totalvolume = 0;
                        foreach($items as $item){
                            $totalweight += $item->product->weight * $item->quantity;
                            $totalvolume += $item->product->width * $item->product->height * $item->product->length * $item->quantity;
                        }
                        
                        $avglength = (int)\pow($totalvolume, 1/3);
                        
                        $ghn = new GHNRequest();
                        
                        $ghnservices = $ghn->getServices(new GHNServiceParameter((int)$shop->ward->district->ghn_district_id, (int)$user->defaultdeliveryaddress->ward->district->ghn_district_id, $totalweight, $avglength, $avglength, $avglength));
                        
                        $ghnfee = null;
                        
                        if(count($ghnservices)){
                            $ghnfee = $ghn->calculateFee(new GHNFeeParameter((int)$shop->ward->district->ghn_district_id, (int)$user->defaultdeliveryaddress->ward->district->ghn_district_id, $ghnservices[0]->ServiceID, $totalweight, $avglength, $avglength, $avglength, 0));
                        }
                        
                        #gan du lieu render view
                        $this->View->Data->ghnservices = $ghnservices;
                        $this->View->Data->ghnfee = $ghnfee;
                        $this->View->Data->shop = $shop;
                        $this->View->Data->deliveryaddresses = $user->deliveryaddresses;
                        $this->View->Data->orderitems = $items;
                        
                        return $this->View->RenderTemplate();
                    }else{
                        $this->View->Data->ErrorMessage = 'Trong giỏ hàng không có mặt hàng nào của cửa hàng này';
                        return $this->View->RenderTemplate('_error');
                    }
                }else{
                    //khong co bat ky san pham nao trong gio hang
                    $this->View->Data->ErrorMessage = 'Trang này không tồn tại';
                    return $this->View->RenderTemplate('_error');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DB_ERROR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User', ['backurl' => '/User/Checkout']);
            } catch (GHNException $e){
                $this->View->Data->ErrorMessage = 'Đã có sự cố kết nối với bên thứ ba vui lòng thử Thanh toán lại!';
                return $this->View->RenderTemplate('_error');
            }
        }
        
        public function CheckoutResult($ordercode){
            try{
                $database = new Database();
                
                $user = (new Authenticate($database))->getUser();
                
                $order = new OrderModel($database);
                $order->ordercode = $ordercode;
                
                if($order->loadFromOrderCode()){
                    $order->loadPaymentType();
                    $order->loadTransporter();
                    $this->View->Data->order = $order;
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Index', 'Home');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        public function PayResult($ordercode){
            try{
                $onepay = new OnePay();
                $payresponse = $onepay->getPaymentResponse($_GET);
                $database = new Database();
                $order = new OrderModel($database);
                
                $order->ordercode = $payresponse->OrderInfo;
                if($order->loadFromOrderCode() && $order->paymenttype_id == PaymentTypeModel::ONEPAY){
                    #ton tai
                    $onepayorder = new OnePayOrderModel($database);
                    $onepayorder->order_id = $order->id;
                    $onepayorder->loadFromOrderId();
                    
                    if($order->status == OrderModel::NGUOI_MUA_DANG_THANH_TOAN){
                        if($payresponse->TxnResponseCode == 0){
                            $onepayorder->additiondata = $payresponse->AdditionData;
                            $onepayorder->transactioncode = $payresponse->TxnResponseCode;
                            $onepayorder->transactionmessage = $payresponse->Message;
                            $onepayorder->transactionno = $payresponse->TransactionNo;
                            $onepayorder->update($onepayorder);
                            
                            $order->updatePayStatus(OrderModel::PAID, OrderModel::PAYCOMPLETE);
                            
                            $order->updateStatus(OrderModel::CHO_NGUOI_BAN_XAC_NHAN);
                            
                            $orderlog = new OrderLogModel($database);
                            $orderlog->order_id = $order->id;
                            $orderlog->order_status = $order->status;
                            $orderlog->content = $order->getStatusString();
                            $orderlog->add();
                        }else if($payresponse->TxnResponseCode != 100){
                            $onepayorder->additiondata = $payresponse->AdditionData;
                            $onepayorder->transactioncode = $payresponse->TxnResponseCode;
                            $onepayorder->transactionmessage = $payresponse->Message;
                            $onepayorder->transactionno = $payresponse->TransactionNo;
                            $onepayorder->update($onepayorder);
                            
                            $order->updatePayStatus(OrderModel::UNPAID, OrderModel::PAYCOMPLETE);
                            $order->updateStatus(OrderModel::NGUOI_MUA_THANH_TOAN_THAT_BAI);
                            
                            $orderlog = new OrderLogModel($database);
                            $orderlog->order_id = $order->id;
                            $orderlog->order_status = $order->status;
                            $orderlog->content = $order->getStatusString();
                            $orderlog->add();
                        }
                        return $this->redirectToAction('CheckoutResult', 'User', ['ordercode' => $order->ordercode]);
                    }else{
                        return $this->redirectToAction('CheckoutResult', 'User', ['ordercode' => $order->ordercode]);
                    }
                }else{
                    #khong ton tai
                    return $this->View->RenderContent('DON HANG KHONG TON TAI');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (OnePayException $e){
                return $this->View->RenderContent('???');
            }
        }
        
        public function AssessmentOrder($ordercode){
            try{
                if(!is_string($ordercode)){
                    return $this->redirectToAction('Orders', 'User');
                }
                
                $database = new Database();
                
                $user = (new Authenticate($database))->getUser();
                
                $order = new OrderModel($database);
                
                $order->ordercode = $ordercode;
                
                if($order->loadFromOrderCode() && $order->client_id == $user->id){
                    if($order->clientCanAssessment()){
                        $order->loadOrderItems();
                        
                        foreach($order->orderitems as $orderitem){
                            $orderitem->loadProduct();
                            $orderitem->product->loadMainImage();
                        }
                        
                        $this->View->Data->order = $order;
                        return $this->View->RenderTemplate();
                    }else{
                        return $this->redirectToAction('Order', 'User', ['ordercode' => $ordercode]);
                    }
                }else{
                    return $this->redirectToAction('Orders', 'User');
                }
            } catch (DBException $ex) {
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User', ['backurl' => '/User/AssessmentOrder?ordercode=' . $ordercode]);
            }
        }
        
        
        public function ViewHistory($page = 1, $ipp = 10){
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $productviewslogs = $user->getProductViewsLogs(($page - 1) * $ipp, $ipp);
                
                foreach($productviewslogs as $productviewslog){
                    $productviewslog->loadProduct();
                    $productviewslog->product->loadMainImage();
                }
                
                $this->View->Data->productviewslogs = $productviewslogs;
                $this->View->TemplateData->pagination = new Pagination($page, $user->getProductViewsLogsTotal(), [], $ipp);
                
                return $this->View->RenderTemplate();
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
    }