<?php
    namespace App\Controllers\Ajax;
    use App\Models\MainCategoryModel;
    use App\Exception\AuthenticationException;
    use Exception;
    
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
                if(!$this->user->isLogin()||!$this->user->haveRole(ADMIN_PRIV)){
                    throw new AuthenticationException('Bạn không có quyền thực hiện thao tác này');
                }
                $maincategory->dbcon = $this->dbcon;
                $maincategory->add();
            } catch (\Exception $ex) {
                $result = new \stdClass();
                $result->error = 1;
                $result->code = 1;
                $result->message = $ex . '';
                return $this->View->RenderJson($result);
            }
            $result = new \stdClass();
            $result->error = 0;
            $result->code = 0;
            $result->message = 'Đã thêm thành công danh mục chính';
            return $this->View->RenderJson($result);
        }
        public function Edit($id, MainCategoryModel $mcate){
            header('content-type: application/json');
            $result = new \stdClass();
            try{
                if(!$this->user->isLogin()||!$this->user->haveRole(ADMIN_PRIV)){
                    throw new AuthenticationException('Bạn không có quyền thực hiện thao tác này');
                }
                $maincategory = new MainCategoryModel($this->dbcon);
                $maincategory->id = $id;
                $maincategory->update($mcate);
                $result->code = 0;
                $result->error = 0;
                $result->message = 'Đã cập nhật thành công danh mục chính';
                return $this->View->RenderJson($result);
            } catch (\Exception $ex) {
                $result->error = 1;
                $result->code = 1;
                $result->message = $ex . '';
                return $this->View->RenderJson($result);
            }
        }
        public function Del($id){
            header('content-type: application/json');
            $result = new \stdClass();
            try{
                if(!$this->user->isLogin()||!$this->user->haveRole(ADMIN_PRIV)){
                    throw new AuthenticationException('Bạn không có quyền thực hiện thao tác này');
                }
                $maincategory = new MainCategoryModel($this->dbcon);
                $maincategory->id = $id;
                $maincategory->delete();
                $result->code = 0;
                $result->error = 0;
                $result->message = 'Xóa thành công danh mục chính';
                return $this->View->RenderJson($result);
            } catch (\Exception $ex) {
                $result->error = 1;
                $result->code = 1;
                $result->message = $ex . '';
                return $this->View->RenderJson($result);
            }
        }
        public function AddForm(){
            if(!$this->user->isLogin()||!$this->user->haveRole(ADMIN_PRIV)){
                return $this->View->RenderContent("Bạn không có quyền thực hiện điều này");
            }
            return $this->View->RenderPartial();
        }
        public function EditForm($id){
            if(!$this->user->isLogin()||!$this->user->haveRole(ADMIN_PRIV)){
                return $this->View->RenderContent('Bạn không có quyền thực hiện điều này');
            }
            $mcate = new MainCategoryModel($this->dbcon);
            $mcate->id = $id;
            if($mcate->loadFromDB()){
                $this->View->ViewData['maincategory'] = $mcate;
                return $this->View->RenderPartial();
            }else{
                return $this->View->RenderContent('Danh mục này không tồn tại');
            }
        }
        public function DelForm($id){
            if(!$this->user->isLogin()||!$this->user->haveRole(ADMIN_PRIV)){
                return $this->View->RenderContent('Bạn không có quyền thực hiện điều này');
            }
            $mcate = new MainCategoryModel($this->dbcon);
            $mcate->id = $id;
            if($mcate->loadFromDB()){
                $this->View->ViewData['maincategory'] = $mcate;
                return $this->View->RenderPartial();
            }else{
                return $this->View->RenderContent('Danh mục này không tồn tại');
            }
        }
    }