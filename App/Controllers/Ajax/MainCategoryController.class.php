<?php
    namespace App\Controllers\Ajax;
    class MainCategoryController extends \Core\Controller{
        public function __init(){
            $this->dbcon = new \Library\MySQLUtility($this->config['db']['host'], $this->config['db']['username'], $this->config['db']['password'], $this->config['db']['dbname']);
            if($this->dbcon->connect_errno()){
                echo 'Lỗi Database: <b style="color:red">' . $this->dbcon->connect_error() .'</b>';
                exit;
            }
            $this->authenticate();
            $this->View->dbcon = $this->dbcon;
            $this->View->user = $this->user;
        }
        public function Add(){
            header('content-type: application/json');
            return $this->View->RenderJson();
        }
        public function AddForm(){
            return $this->View->RenderTemplate();
        }
    }