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
        public $id, $username, $firstname, $lastname, $password, $email, $phone, $address, $district_id, $created_time, $locked, $birthday, $money, $role, $gender;
        
        public function setId($id) {
            $this->id = $id;
            return $this;
        }

        public function setUserName($username) {
            $this->username = $username;
            return $this;
        }

        public function setFirstName($firstname) {
            $this->firstname = $firstname;
            return $this;
        }

        public function setLastName($lastname) {
            $this->lastname = $lastname;
            return $this;
        }

        public function setPassword($password) {
            $this->password = $password;
            return $this;
        }

        public function setEmail($email) {
            $this->email = $email;
            return $this;
        }

        public function setPhone($phone) {
            $this->phone = $phone;
            return $this;
        }

        public function setAddress($address) {
            $this->address = $address;
            return $this;
        }

        public function setDistrictId($district_id) {
            $this->district_id = $district_id;
            return $this;
        }

        public function setCreatedDate($created_date) {
            $this->created_date = $created_date;
            return $this;
        }

        public function setLocked($locked) {
            $this->locked = $locked;
            return $this;
        }

        public function setBirthday($birthday) {
            $this->birthday = $birthday;
            return $this;
        }

        public function setDay($day) {
            $this->day = $day;
            return $this;
        }

        public function setMonth($month) {
            $this->month = $month;
            return $this;
        }

        public function setYear($year) {
            $this->year = $year;
            return $this;
        }

        public function setMoney($money) {
            $this->money = $money;
            return $this;
        }

        public function setRole($role) {
            $this->role = $role;
            return $this;
        }

        public function setGender($gender) {
            $this->gender = $gender;
            return $this;
        }
                
        public function getId() {
            return $this->id;
        }

        public function getUserName() {
            return $this->username;
        }

        public function getFirstName() {
            return $this->firstname;
        }

        public function getLastName() {
            return $this->lastname;
        }

        public function getPassword() {
            return $this->password;
        }

        public function getEmail() {
            return $this->email;
        }

        public function getPhone() {
            return $this->phone;
        }

        public function getAddress() {
            return $this->address;
        }

        public function getDistrictId() {
            return $this->district_id;
        }

        public function getCreatedDate() {
            return $this->created_date;
        }

        public function getLocked() {
            return $this->locked;
        }
        
        public function getBirthday(){
            return $this->birthday;
        }

        public function getDay() {
            return $this->day;
        }

        public function getMonth() {
            return $this->month;
        }

        public function getYear() {
            return $this->year;
        }

        public function getMoney() {
            return $this->money;
        }

        public function getRole() {
            return $this->role;
        }

        public function getGender() {
            return $this->gender;
        }

        ###
        # Check input data
        ###
        
        public function checkValidForUserName(){
            if(isset($this->username) && is_string($this->username)){
                if(!preg_match('/^[A-z0-9]{6,}$/', $this->username)){
                    $this->addErrorMessage('username', 'Tên đăng nhập không hợp lệ, phải là số hoặc ký tự, có chiều dài là 6');
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
            if(!isset($this->firstname) || !is_string($this->firstname)){
                $this->addErrorMessage('firstname', 'Tên không hợp lệ');
            }else{
                if(empty($this->firstname)){
                    $this->addErrorMessage('firstname', 'Tên không được để trống');
                }
            }
            return $this;
        }
        
        public function checkValidForLastName(){
            if(!isset($this->lastname) || !is_string($this->lastname)){
                $this->addErrorMessage('lastname', 'Họ không hợp lệ');
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
                $this->addErrorMessage('email', 'Địa chỉ email không hợp lệ');
            }
            return $this;
        }
        
        public function checkValidForPhone(){
            if(!isset($this->phone) || !is_string($this->phone) || !preg_match('/^0[0-9]{9,10}$/', $this->phone)){
                $this->addErrorMessage('phone', 'Số điện thoại không hợp lệ');
            }
            
            return $this;
        }
        
        public function checkValidForAddress(){
            if(!isset($this->address) || !is_string($this->address)){
                $this->addErrorMessage('address', 'Địa chỉ không hợp lệ!');
            }else{
                if(mb_strlen($this->address)>200){
                    $this->addErrorMessage('address', 'Địa chỉ không được dài quá 200 ký tự');
                }
            }
            return $this;
        }
        
        public function checkValidForDistrictId(){
            if(!isset($this->district_id) || !is_numeric($this->district_id)){
                $this->addErrorMessage('districtid', 'Địa chỉ Quận huyện không hợp lệ!');
                return $this;
            }
            $result = $this->database->select('*')->from('district')->where('id=' . new DBNumber($this->district_id))->execute();
            if(!count($result)){
                $this->addErrorMessage('districtid', 'Quận/huyện không hợp lệ');
            }
            return $this;
        }
        
        public function checkValidForBirthday(){
            if(!isset($this->birthday) || !checkdate($this->birthday->month, $this->birthday->day, $this->birthday->year)){
                $this->addErrorMessage('birthday', "{$this->birthday->day}/{$this->birthday->month}/{$this->birthday->year} không hợp lệ!");
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
            if($this->username){
                $this->username = $this->database->escape($this->username);
            }
            
            if($this->firstname){
                $this->firstname = $this->database->escape($this->firstname);
            }
            
            if($this->lastname){
                $this->lastname = $this->database->escape($this->lastname);
            }
            
            if($this->password){
                $this->password = $this->database->escape($this->password);
            }
            
            if($this->email){
                $this->email = $this->database->escape($this->email);
            }
            
            if($this->phone){
                $this->email = $this->database->escape($this->phone);
            }
            
            if($this->address){
                $this->email = $this->database->escape($this->address);
            }
            return $this;
        }
        
        public function isLogin(){
            return isset($this->id);
        }
        
        public function isLocked(){
            return $this->locked == 1;
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
                $this->address = $row->address;
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
            $this->database->insert(DB_TABLE_USER, ['username' => new DBString($this->username), 'firstname' => new DBString($this->firstname), 'lastname' => new DBString($this->lastname), 'password' => new DBRaw("md5('{$this->password}')"), 'email' => new DBString($this->email), 'phone' => new DBString($this->phone), 'address' => new DBString($this->address), 'district_id' => new DBRaw('null'), 'locked' => new DBNumber(0), 'birthday' => new DBDateTime($this->birthday->day, $this->birthday->month, $this->birthday->year), 'gender' => new DBNumber($this->gender), 'money' => new DBNumber(0), 'role' => new DBNumber(UserModel::NORMAL_ROLE)]);
            return true;
        }
        public function update($user){
            $this->database->update(DB_TABLE_USER, ['lastname' => new DBString($user->lastname),'firstname'=>new DBString($user->firstname),'birthday'=>new DBDateTime($user->birthday->day, $user->birthday->month, $user->birthday->year),'gender'=>new DBNumber($user->gender),'address'=>new DBString($user->address)], 'id='.$this->id);
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
                $this->address = $row->address;
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
        public function haveRole($privilege){
            return $this->role == $privilege;
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