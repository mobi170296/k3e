<?php
    namespace Core;
    use Library\Database\DatabaseUtility;
    use App\Models\UserModel;
    
    class Controller{
        public $View;
        public $controller, $action;
        public $config;
        public $dbcon;
        public $user;
        public $request;
        #
        # Authentication for application via UserModel
        #
        protected function authenticate(){
            $this->user = new UserModel($this->dbcon);
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
        protected function __init_db_authenticate(){
            $this->dbcon = new DatabaseUtility($this->config['db']['host'], $this->config['db']['username'], $this->config['db']['password'], $this->config['db']['dbname']);
            if($this->dbcon->connect_errno()){
                echo 'Lỗi Database: <b style="color:red">' . $this->dbcon->connect_error() .'</b>';
                exit;
            }
            $this->authenticate();
        }
        protected function redirectToAction($controller, $action, $params){
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