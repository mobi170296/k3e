<?php
    namespace App\Controllers;
    
    class AdminController extends \Core\Controller{
        protected function __init(){
            $this->dbcon = new \Library\MySQLUtility($this->config['db']['host'], $this->config['db']['username'], $this->config['db']['password'], $this->config['db']['dbname']);
            if($this->dbcon->connect_errno()){
                echo 'Lỗi Database: <b style="color:red">'. $thí->dbcon->connect_error() .'</style>';
                exit;
            }
            $this->authenticate();
            if($this->user->haveRole(ADMIN_PRIV)){
                echo 'Bạn có quyền quản trị';
            }else{
                echo 'Bạn không có quyền quản trị';
            }
            $this->View->dbcon = $this->dbcon;
            $this->View->user = $this->user;
        }
        public function Index(){
            
        }
    }