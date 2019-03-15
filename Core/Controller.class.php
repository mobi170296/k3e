<?php
    namespace Core;
    
    class Controller{
        public $View;
        public $controller, $action;
        public $config;
        public $dbcon;
        public $user;
        #
        # Authentication for application via UserModel
        #
        public function authenticate(){
            $this->user = new \App\Models\UserModel($this->dbcon);
            if(isset($_SESSION['username']) && isset($_SESSION['password'])){
                $this->user->username = $_SESSION['username'];
                $this->user->password = $_SESSION['password'];
                if($this->user->login()){
                    return true;
                }else{
                    unset($_SESSION['username']);
                    unset($_SESSION['password']);
                    return false;
                }
            }else{
                unset($_SESSION['username']);
                unset($_SESSION['password']);
                return false;
            }
        }
        
        public function __construct($controller, $action){
            global $k3_config;
            $this->config = $k3_config;
            $this->View = new View($controller, $action);
            $this->controller = $controller;
            $this->action = $action;
            $this->__init();
        }
        protected function __init(){
            
        }
        public function redirectToAction($controller, $action, $params){
            $querystring = '';
            if($params!=null){
                foreach($params as $k => $v){
                    $p[] = $k . '=' . urlencode($v);
                }
                $querystring = implode($p, '&');
            }
            header('location: /' . $controller . '/' . $action . (!empty($querystring)?'?' . $querystring : ''));
            exit;
        }
    }