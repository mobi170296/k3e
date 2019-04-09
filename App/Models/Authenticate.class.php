<?php
    namespace App\Models;
    use App\Exception\AuthenticateException;
    
    class Authenticate{
        public $connection;
        public $user;
        public function __construct($connection = null){
            $this->connection = $connection;
            $this->user = new UserModel($connection);
            
            if(isset($_SESSION['username']) && isset($_SESSION['password'])){
                $this->user->username = $_SESSION['username'];
                $this->user->password = $_SESSION['password'];
                $this->user->standardization();
                #Them vao chuc nang khoa nguoi dung
                #
                $this->user->login();
                if($this->user->isLogin()){
                    if($this->user->isLocked()){
                        unset($_SESSION['username']);
                        unset($_SESSION['password']);
                        throw new AuthenticateException('Tài khoản của bạn đã bị khóa', -1);
                    }
                }else{
                    unset($_SESSION['username']);
                    unset($_SESSION['password']);
                    throw new AuthenticateException('Tên đăng nhập và mật khẩu bạn đã bị thay đổi, vui lòng đăng nhập lại', -1);
                }
//                if(!$this->user->login()){
//                    throw new AuthenticateException('Tên đăng nhập và mật khẩu bạn đã thay đổi, vui lòng đăng nhập lại!', -1);
//                }
            }else{
                throw new AuthenticateException('Bạn chưa đăng nhập!', -1);
            }
        }
        
        public function getUser(){
            return $this->user;
        }
    }