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
                $this->user->setUserName($_SESSION['username'])->setPassword($_SESSION['password']);
                if(!$this->user->login()){
                    throw new AuthenticateException('Tên đăng nhập và mật khẩu bạn đã thay đổi, vui lòng đăng nhập lại!', -1);
                }
            }else{
                throw new AuthenticateException('Bạn chưa đăng nhập!', -1);
            }
        }
        
        public function getUser(){
            return $this->user;
        }
    }