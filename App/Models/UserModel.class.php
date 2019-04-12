<?php
    namespace App\Models;
    use Library\Database\DBString;
    use Library\Database\DBNumber;
    use Library\Database\DBRaw;
    use Library\Database\DBDateTime;
    use Core\Model;
    
    class UserModel extends Model{
        const MALE = 1, FEMALE = 0;
        const ADMIN_ROLE = 0, NORMAL_ROLE = 1;
        public $id, $username, $firstname, $lastname, $password, $email, $phone, $district_id, $created_time, $locked, $birthday, $money, $role, $gender;
        public $shop;
        public $orders = [];
        public $assessments = [];
        public $cartitems = [];
        public $district;
        public $deliveryaddresses = [];
        public $imagemaps = [];
        
        public function checkValidForUserName(){
            if(isset($this->username) && is_string($this->username)){
                if(!preg_match('/^[A-z0-9]{6,32}$/', $this->username)){
                    $this->addErrorMessage('username', 'Tên tài khoản không hợp lệ, phải là số hoặc ký tự, có chiều dài từ 6 đến 32 ký tự');
                }
            }else{
                $this->addErrorMessage('username', 'Tên tài khoản không hợp lệ!');
            }
            
            return $this;
        }
        
        public function checkValidForUserNameExists(){
            $rows = $this->database->select('*')->from(DB_TABLE_USER)->where('username=' . new DBString($this->username))->execute();
            if(count($rows)){
                $this->addErrorMessage('usernameduplicate', 'Tên đăng nhập ' . $this->username .' đã tồn tại!');
            }
            return $this;
        }
        
        public function checkValidForFirstName(){
            if(!isset($this->firstname) || !is_string($this->firstname) || mb_strlen($this->firstname) > 20){
                $this->addErrorMessage('firstname', 'Tên không hợp lệ phải có chiều dài tối đa 20 ký tự');
            }else{
                if(empty($this->firstname)){
                    $this->addErrorMessage('firstname', 'Tên không được để trống');
                }
            }
            return $this;
        }
        
        public function checkValidForLastName(){
            if(!isset($this->lastname) || !is_string($this->lastname) || mb_strlen($this->lastname) > 32){
                $this->addErrorMessage('lastname', 'Họ không hợp lệ phải có chiều dài tối đa 32 ký tự');
            }else{
                if(empty($this->lastname)){
                    $this->addErrorMessage('lastname', 'Họ không được để trống');
                }
            }
            return $this;
        }
        
        public function checkValidForPassword($p1 = null, $p2 = null){
            if($p1 === null && $p2 === null){
                if(!isset($this->password) || !is_string($this->password) || !preg_match('/^.{6,}$/', $this->password)){
                    $this->addErrorMessage('password', 'Mật khẩu không hợp lệ phải từ 6 ký tự trở lên');
                }
            }else{
                if(is_string($p1) && is_string($p2)){
                    if($p2 !== $p1){
                        $this->addErrorMessage('password', 'Mật khẩu nhập lại không trùng khớp');
                    }else if(!preg_match('/^.{6,}$/', $p1)){
                        $this->addErrorMessage('password', 'Mật khẩu không hợp lệ phải từ 6 ký tự trở lên');
                    }
                }else{
                    $this->addErrorMessage('password', 'Mật khẩu không hợp lệ!');
                }
            }
            
            return $this;
        }
        
        public function checkValidForEmail(){
            if(!isset($this->email) || !is_string($this->email) || !preg_match('/^[A-z0-9.+%-]+@([A-z0-9-]+\.)[A-z]{2,}$/', $this->email)){
                $this->addErrorMessage('email', 'Địa chỉ email không đúng định dạng');
            }
            return $this;
        }
        
        public function checkValidForPhone(){
            if(!isset($this->phone) || !is_string($this->phone) || !preg_match('/^0[0-9]{9,10}$/', $this->phone)){
                $this->addErrorMessage('phone', 'Số điện thoại không hợp lệ');
            }
            
            return $this;
        }
        
        public function checkValidForDistrictId(){
            if(!isset($this->district_id) || !is_numeric($this->district_id)){
                $this->addErrorMessage('districtid', 'Địa chỉ Quận/Huyện không hợp lệ!');
                return $this;
            }
            $result = $this->database->select('*')->from(DB_TABLE_DISTRICT)->where('id=' . (int)$this->district_id)->execute();
            if(!count($result)){
                $this->addErrorMessage('districtid', 'Quận/huyện không hợp lệ');
            }
            return $this;
        }
        
        public function checkValidForBirthday(){
            if(!isset($this->birthday) || !isset($this->birthday->day) || !is_numeric($this->birthday->day) || !isset($this->birthday->month) || !is_numeric($this->birthday->month) || !isset($this->birthday->year) || !is_numeric($this->birthday->year) || !checkdate($this->birthday->month, $this->birthday->day, $this->birthday->year)){
                $this->addErrorMessage('birthday', "Ngày tháng năm sinh không hợp lệ!");
            }
            return $this;
        }
        
        public function checkValidForGender(){
            if(!isset($this->gender) || !is_numeric($this->gender) || ($this->gender != UserModel::FEMALE && $this->gender != UserModel::MALE)){
                $this->addErrorMessage('gender', 'Giới tính không hợp lệ');
            }
            return $this;
        }
        
        public function checkValidForRole(){
            if(!isset($this->role) || !is_numeric($this->role) || ($this->role != UserModel::ADMIN_ROLE && $this->role != UserModel::NORMAL_ROLE)){
                $this->addErrorMessage('role', 'Quyền không hợp lệ!');
            }
            return $this;
        }
        
        public function standardization(){
            if(!empty($this->username)){
                $this->username = $this->database->escape($this->username);
            }
            
            if(!empty($this->firstname)){
                $this->firstname = $this->database->escape($this->firstname);
            }
            
            if(!empty($this->lastname)){
                $this->lastname = $this->database->escape($this->lastname);
            }
            
            if(!empty($this->password)){
                $this->password = $this->database->escape($this->password);
            }
            
            if(!empty($this->email)){
                $this->email = $this->database->escape($this->email);
            }
            
            if(!empty($this->phone)){
                $this->phone = $this->database->escape($this->phone);
            }
            
            return $this;
        }
        
        public function isLogin(){
            return isset($this->id);
        }
        
        public function isLocked(){
            return $this->locked == 1;
        }
        
        public function loadShop(){
            
        }
        
        public function loadDistrict(){
            
        }
        
        public function loadCartItems(){
            
        }
        
        public function loadOrders(){
            
        }
        
        public function loadDeliveryAddresses(){
            $rows = $this->database->select('*')->from(DB_TABLE_DELIVERYADDRESS)->where('user_id=' . (int)$this->id)->orderby('def desc, id asc')->execute();
            foreach($rows as $row){
                $d = new DeliveryAddressModel($this->database);
                $d->id = $row->id;
                $d->loadData();
                $d->loadWard();
                $d->ward->loadDistrict();
                $d->ward->district->loadProvince();
                $this->deliveryaddresses[] = $d;
            }
        }
        
        public function getDeliveryAddressesTotal(){
            $rows = $this->database->select('count(*) total')->from(DB_TABLE_DELIVERYADDRESS)->where('user_id=' . (int)$this->id)->execute();
            return $rows[0]->total;
        }
        
        public function loadData(){
            $row = $this->database->select('*')->from(DB_TABLE_USER)->where('id='.new DBNumber($this->id))->execute();
            if(count($row)){
                $row = $row[0];
                $this->id = $row->id;
                $this->username = $row->username;
                $this->firstname = $row->firstname;
                $this->lastname = $row->lastname;
                $this->password = $row->password;
                $this->email = $row->email;
                $this->phone = $row->phone;
                $this->district_id = $row->district_id;
                $this->birthday = DBDateTime::parse($row->birthday);
                $this->created_time = DBDateTime::parse($row->created_time);
                $this->locked = $row->locked;
                $this->birthday = DBDateTime::parse($row->birthday);
                $this->gender = $row->gender;
                $this->money = $row->money;
                $this->role = $row->role;
                return true;
            }else{
                return false;
            }
        }
        public function register(){
            $this->database->insert(DB_TABLE_USER, ['username' => new DBString($this->username), 'firstname' => new DBString($this->firstname), 'lastname' => new DBString($this->lastname), 'password' => new DBRaw("md5('{$this->password}')"), 'email' => new DBString($this->email), 'phone' => new DBString($this->phone), 'district_id' => new DBRaw('null'), 'locked' => new DBNumber(0), 'birthday' => new DBDateTime($this->birthday->day, $this->birthday->month, $this->birthday->year), 'gender' => new DBNumber($this->gender), 'money' => new DBNumber(0), 'role' => new DBNumber(UserModel::NORMAL_ROLE)]);
            return true;
        }
        public function update($user){
            $this->database->update(DB_TABLE_USER, ['lastname' => new DBString($user->lastname),'firstname'=>new DBString($user->firstname),'birthday'=>new DBDateTime($user->birthday->day, $user->birthday->month, $user->birthday->year),'gender'=>new DBNumber($user->gender)], 'id='. (int)$this->id);
            $this->lastname = $this->database->unescape($user->lastname);
            $this->firstname = $this->database->unescape($user->firstname);
            $this->birthday = $user->birthday;
            return true;
        }
        public function login(){
            $row = $this->database->select('*')->from(DB_TABLE_USER)->where("username='{$this->username}' and password=md5('{$this->password}')")->execute();
            if(count($row)){
                $row = $row[0];
                $this->id = $row->id;
                $this->username = $row->username;
                $this->firstname = $row->firstname;
                $this->lastname = $row->lastname;
                $this->password = $row->password;
                $this->email = $row->email;
                $this->phone = $row->phone;
                $this->district_id = $row->district_id;
                $this->birthday = DBDateTime::parse($row->birthday);
                $this->created_time = DBDateTime::parse($row->created_time);
                $this->locked = $row->locked;
                $this->birthday = DBDateTime::parse($row->birthday);
                $this->gender = $row->gender;
                $this->money = $row->money;
                $this->role = $row->role;
                return true;
            }else{
                return false;
            }
        }
        public function changePassword($newpassword){
            $newpassword = $this->database->escape($newpassword);
            $this->database->update(DB_TABLE_USER, ['password' => new DBRaw("md5('$newpassword')")], 'id='.(int)$this->id);
        }
        
        public function haveRole($privilege){
            return $this->role == $privilege;
        }
        
        public function isShop(){
            $rows = $this->database->select('count(*) as total')->from(DB_TABLE_SHOP)->where('owner_id='.(int)$this->id)->execute();
            return $rows[0]->total != 0;
        }
        
        public function getRoleString($p){
            switch($p){
                case UserModel::ADMIN_ROLE:
                    return "Quản trị hệ thống";
                case UserModel::NORMAL_ROLE:
                    return "Khách hàng";
                default:
                    return "";
            }
        }
        public function getGenderString($g){
            switch($g){
                case UserModel::FEMALE:
                    return "Nữ";
                case UserModel::MALE:
                    return "Nam";
                default: return "Chưa xác định";
            }
        }
    }