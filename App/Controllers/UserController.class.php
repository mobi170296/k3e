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
        public function Login($login, $username, $password){
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
                            return $this->redirectToAction('Index', 'Home');
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
        public function Orders(){
            
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
                    $this->View->Data->district_id = $input->ward_id;
                    $this->View->Data->province_id = $province_id;
                    $this->View->Data->districtlist = (new DistrictList($database))->getAllFromProvince((int)$province_id);
                    $this->View->Data->district_id = $district_id;
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
    }