<?php
    namespace App\Controllers\Ajax;
    use App\Models\MainCategoryModel;
    
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
        public function Add(MainCategoryModel $maincategory){
            #Thêm danh mục sản phẩm chính
            header('content-type: application/json');
            try{
                $maincategory->dbcon = $this->dbcon;
                $maincategory->add();
            } catch (Exception $ex) {
                $result = new \stdClass();
                $result->error = 1;
                $result->code = 1;
                $result->message = $ex;
                return $this->View->RenderJson($result);
            }
            $result = new \stdClass();
            $result->error = 0;
            $result->code = 0;
            $result->message = 'Đã thêm thành công danh mục chính';
            return $this->View->RenderJson($result);
        }
        public function AddForm(){
            if(!$this->user->haveRole(ADMIN_PRIV)){
                return $this->View->RenderContent("Bạn không có quyền thực hiện điều này");
            }
            return $this->View->RenderTemplate();
        }
        public function EditForm($id){
            if(!$this->user->haveRole(ADMIN_PRIV)){
                return $this->View->RenderContent('Bạn không có quyền thực hiện điều này');
            }
            $mcate = new MainCategoryModel($this->dbcon);
            $mcate->id = $id;
            if($mcate->loadFromDB()){
                $this->View->ViewData['maincategory'] = $mcate;
                return $this->View->RenderTemplate();
            }else{
                return $this->View->RenderContent('Danh mục này không tồn tại');
            }
        }
        public function DelForm($id){
            if(!$this->user->haveRole(ADMIN_PRIV)){
                return $this->View->RenderContent('Bạn không có quyền thực hiện điều này');
            }
            $mcate = new MainCategoryModel($this->dbcon);
            $mcate->id = $id;
            if($mcate->loadFromDB()){
                $this->View->ViewData['maincategory'] = $mcate;
                return $this->View->RenderTemplate();
            }else{
                return $this->View->RenderContent('Danh mục này không tồn tại');
            }
        }
    }