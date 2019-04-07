<?php
    namespace App\Models;
    use Library\Database\DBString;
    use Library\Database\DBDate;
    use Library\Database\DBNumber;
    use Library\Database\DBRaw;
    use Library\Database\DBDateTime;
    use App\Exception\DBException;
    use App\Exception\InputException;
    
    class UserModel extends \Core\Model{
        protected $id, $username, $firstname, $lastname, $password, $email, $phone, $address, $district_id, $created_date, $locked, $birthday, $day, $month, $year, $money, $role, $gender;
        public $dbcon;
        
        public function __construct($connection = null){
            $this->dbcon = $connection;
        }
        
        public function setConnection($connection){
            $this->dbcon = $connection;
            return $this;
        }

        #Old method
        public function setDBCon($con){
            $this->dbcon = $con;
            return $this;
        }
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

                
        public function isLogin(){
            return isset($this->id);
        }
        
        public function register(){
            $errors = [];
            if(!preg_match('/^[A-z0-9]{6,}$/', $this->username)){
                $errors['username'] = 'Tên đăng nhập không hợp lệ, phải là số hoặc ký tự, có chiều dài là 6';
            }
            if($this->password[0] !== $this->password[1]){
                $errors['password'] = 'Mật khẩu nhập lại không trùng khớp';
            }else if(!preg_match('/^.{6,}$/', $this->password[0])){
                $errors['password'] = 'Mật khẩu không hợp lệ phải từ 6 ký tự trở lên';
            }
            if(empty($this->lastname)){
                $errors['lastname'] = 'Họ không được để trống';
            }
            if(empty($this->firstname)){
                $errors['firstname'] = 'Tên không được để trống';
            }
            if(!preg_match('/^[A-z0-9.+%-]+@([A-z0-9-]+\.)[A-z]{2,}$/', $this->email)){
                $errors['email'] = 'Địa chỉ email không hợp lệ';
            }
            if(!checkdate($this->month, $this->day, $this->year)){
                $errors['birthday'] = 'Ngày tháng năm sinh không hợp lệ';
            }
            if(!preg_match('/^0[0-9]{9,10}$/', $this->phone)){
                $errors['phone'] = 'Số điện thoại không hợp lệ';
            }
            if($this->gender!=1&&$this->gender!=0){
                $errors['gender'] = 'Giới tính không hợp lệ';
            }
            if(count($errors)){
                throw new \App\Exception\InputException($errors);
            }
            $this->dbcon->insert(DB_TABLE_USER, ['username'=>new DBString($this->username), 'firstname'=>new DBString($this->firstname), 'lastname'=>new DBString($this->lastname),'password'=>new DBRaw("md5('{$this->password[0]}')"), 'email'=>new DBString($this->email), 'phone'=>new DBString($this->phone) ,'address'=>new DBString(''), 'district_id'=>new DBRaw('null'), 'birthday'=>new DBDate($this->day, $this->month, $this->year), 'gender'=>new DBNumber($this->gender)]);
            if($this->dbcon->errno()){
                throw new DBException($this->dbcon->error());
            }
        }
        public function update($user){
            $errors = [];
            if(empty($user->lastname)){
                $errors['lastname'] = 'Họ không được để trống';
            }
            if(empty($user->firstname)){
                $errors['firstname'] = 'Tên không được để trống';
            }
            if(!checkdate($user->month, $user->day, $user->year)){
                $errors['birthday'] = 'Ngày tháng năm sinh không hợp lệ';
            }
            if($user->gender!=1&&$user->gender!=0){
                $errors['gender'] = 'Giới tính không hợp lệ';
            }
            if(mb_strlen($user->address)>200){
                $errors['address'] = 'Địa chỉ không được vượt quá 200 ký tự';
            }
            if(count($errors)){
                throw new InputException($errors);
            }
            $this->dbcon->update(DB_TABLE_USER, ['lastname'=>new DBString($user->lastname),'firstname'=>new DBString($user->firstname),'birthday'=>new DBDate($user->day, $user->month, $user->year),'gender'=>new DBNumber($user->gender),'address'=>new DBString($user->address)], 'id='.$this->id);
            if($this->dbcon->errno()){
                throw new DBException($this->dbcon->error());
            }else{
                $this->lastname = $user->lastname;
                $this->firstname = $user->firstname;
                $this->birthday = new DBDate($user->day, $user->month, $user->year);
                $this->gender = $user->gender;
                $this->address = $user->address;
            }
        }
        public function login(){
            $result = $this->dbcon->select('*')->from(DB_TABLE_USER)->where("username='{$this->username}' and password=md5('{$this->password}')")->execute();
            if($result->num_rows){
                $row = $result->fetch_assoc();
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->firstname = $row['firstname'];
                $this->lastname = $row['lastname'];
                $this->password = $row['password'];
                $this->email = $row['email'];
                $this->phone = $row['phone'];
                $this->address = $row['address'];
                $this->district_id = $row['district_id'];
                $this->birthday = DBDateTime::parse($row['birthday']);
                $this->created_date = DBDateTime::parse($row['created_date']);
                $this->locked = $row['locked'];
                $this->birthday = DBDate::parse($row['birthday']);
                $this->gender = $row['gender'];
                $this->money = $row['money'];
                $this->role = $row['role'];
                return true;
            }else{
                return false;
            }
        }
        public function haveRole($privilege){
            return $this->role == $privilege;
        }
    }