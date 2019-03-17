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
            if(!$this->user->isLogin() || !$this->user->haveRole(ADMIN_PRIV)){
                $this->View->ViewData['error'] = 'Bạn không có quyền để thực hiện hành động này';
                return $this->View->RenderTemplate('error_page', 'error');
            }
            return $this->View->RenderTemplate();
        }
        public function MainCategory(){
            if(!$this->user->isLogin() || !$this->user->haveRole(ADMIN_PRIV)){
                $this->View->ViewData['error'] = 'Bạn không có quyền để thực hiện hành động này';
                return $this->View->RenderTemplate('error_page', 'error');
            }
            return $this->View->RenderTemplate();
        }
        public function Subcategory(){
            if(!$this->user->isLogin() || !$this->user->haveRole(ADMIN_PRIV)){
                $this->View->ViewData['error'] = 'Bạn không có quyền để thực hiện hành động này';
                return $this->View->RenderTemplate('error_page', 'error');
            }
            return $this->View->RenderTemplate();
        }
        public function AccountInfo(){
            if(!$this->user->isLogin() || !$this->user->haveRole(ADMIN_PRIV)){
                $this->View->ViewData['error'] = 'Bạn không có quyền để thực hiện hành động này';
                return $this->View->RenderTemplate('error_page', 'error');
            }
            return $this->View->RenderTemplate();
        }
        public function Orders(){
            if(!$this->user->isLogin() || !$this->user->haveRole(ADMIN_PRIV)){
                $this->View->ViewData['error'] = 'Bạn không có quyền để thực hiện hành động này';
                return $this->View->RenderTemplate('error_page', 'error');
            }
            return $this->View->RenderTemplate();
        }
    }