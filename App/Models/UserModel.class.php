<?php
    namespace App\Models;
    use Library\DBString;
    use Library\DBDate;
    use Library\DBNumber;
    use Library\DBRaw;
    
    class UserModel extends \Core\Model{
        public $id;
        public $username;
        public $firstname;
        public $lastname;
        public $password;
        public $email;
        public $phone;
        public $address;
        public $district_id;
        public $created_date;
        public $locked;
        public $day;
        public $month;
        public $year;
        public $money;
        public $role;
        public $gender;
        
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
            $this->dbcon->insert('user', ['username'=>new DBString($this->username), 'firstname'=>new DBString($this->firstname), 'lastname'=>new DBString($this->lastname),'password'=>new DBRaw("md5('{$this->password[0]}')"), 'email'=>new DBString($this->email), 'address'=>new DBString(''), 'district_id'=>new DBRaw('null'), 'birthday'=>new DBDate($this->day, $this->month, $this->year), 'gender'=>new DBNumber($this->gender)]);
            if($this->dbcon->errno()){
                throw new \App\Exception\DBException($this->dbcon->error());
            }
        }
        
        public function login(){
            $result = $this->dbcon->select('*')->from('user')->where("username='{$this->username}' and password=md5('{$this->password}')")->execute();
            if($result->num_rows){
                $row = $result->fetch_assoc();
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->firstname = $row['firstname'];
                $this->lastname = $row['lastname'];
                $this->password = $row['password'];
                $this->email = $row['email'];
                $this->address = $row['address'];
                $this->district_id = $row['district_id'];
                $this->created_date = \Library\DBDateTime::parse($row['created_date']);
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
    }