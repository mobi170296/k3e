<?php
    namespace App\Controllers;
    use App\Models\SubCategoryModel;
    use App\Models\MainCategoryModel;
    
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
            $this->redirectToAction('Admin', 'MainCategory', null);
//            if(!$this->user->isLogin() || !$this->user->haveRole(ADMIN_PRIV)){
//                $this->View->ViewData['error'] = 'Bạn không có quyền để thực hiện hành động này';
//                return $this->View->RenderTemplate('error_page', 'error');
//            }
//            return $this->View->RenderTemplate();
        }
        public function MainCategory(){
            if(!$this->user->isLogin() || !$this->user->haveRole(ADMIN_PRIV)){
                $this->View->ViewData['error'] = 'Bạn không có quyền để thực hiện hành động này';
                return $this->View->RenderTemplate('error_page', 'error');
            }
            $this->View->ViewData['maincategorylist'] = [];
            $result = $this->dbcon->select('id')->from('maincategory')->execute();
            while($row = $result->fetch_assoc()){
                $mcate = new MainCategoryModel($this->dbcon);
                $mcate->id = $row['id'];
                $mcate->loadFromDB();
                $this->View->ViewData['maincategorylist'][] = $mcate;
            }
            return $this->View->RenderTemplate();
        }
        public function Subcategory(){
            if(!$this->user->isLogin() || !$this->user->haveRole(ADMIN_PRIV)){
                $this->View->ViewData['error'] = 'Bạn không có quyền để thực hiện hành động này';
                return $this->View->RenderTemplate('error_page', 'error');
            }
            $this->View->ViewData['subcategorylist'] = [];
            $result = $this->dbcon->select('id')->from('subcategory')->execute();
            while($row = $result->fetch_assoc()){
                $scate = new SubCategoryModel($this->dbcon);
                $scate->id = $row['id'];
                $scate->loadFromDB();
                $this->View->ViewData['subcategorylist'][] = $scate;
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