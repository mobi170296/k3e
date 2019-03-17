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
            $this->View->dbcon = $this->dbcon;
            $this->View->user = $this->user;
        }
        public function Index(){
            return $this->View->RenderTemplate();
        }
        public function MainCategory(){
            return $this->View->RenderTemplate();
        }
        public function Subcategory(){
            return $this->View->RenderTemplate();
        }
        public function AccountInfo(){
            return $this->View->RenderTemplate();
        }
        public function Orders(){
            return $this->View->RenderTemplate();
        }
    }