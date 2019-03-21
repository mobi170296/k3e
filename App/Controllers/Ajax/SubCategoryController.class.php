<?php
    namespace App\Controllers\Ajax;
    use Core\Controller;
    use App\Models\SubCategoryModel;
    use App\Models\MainCategoryModel;
    
    class SubCategoryController extends Controller{
        public function __init(){
            $this->__init_db_authenticate();
        }
        public function AddForm(){
            if(!$this->user->haveRole(ADMIN_PRIV)){
                return $this->View->RenderContent('Bạn không có quyền thực hiện thao tác này');
            }
            $result = $this->dbcon->select('id')->from(DB_TABLE_MAINCATEGORY)->execute();
            $maincategorylist = [];
            while($row = $result->fetch_assoc()){
                $maincategory = new MainCategoryModel($this->dbcon);
                $maincategory->id = $row['id'];
                $maincategory->loadFromDB();
                $maincategorylist[] = $maincategory;
            }
            if(count($maincategorylist)){
                $this->View->ViewData['maincategorylist'] = $maincategorylist;
                return $this->View->RenderTemplate();
            }else{
                return $this->View->RenderContent('Danh mục chính hiện tại rỗng không thể thêm!');
            }
        }
        public function EditForm($id){
           if(!$this->user->haveRole(ADMIN_PRIV)){
                return $this->View->RenderContent('Bạn không có quyền thực hiện thao tác này');
            }
            $subcate = new SubCategoryModel($this->dbcon);
            $subcate->id = $id;
            if($subcate->loadFromDB()){
                $maincategorylist = [];
                $result = $this->dbcon->select('id')->from(DB_TABLE_MAINCATEGORY)->execute();
                while($row = $result->fetch_assoc()){
                    $maincategory = new MainCategoryModel($this->dbcon);
                    $maincategory->id = $row['id'];
                    $maincategory->loadFromDB();
                    $maincategorylist[] = $maincategory;
                }
                $this->View->ViewData['maincategorylist'] = $maincategorylist;
                $this->View->ViewData['subcategory'] = $subcate;
                return $this->View->RenderTemplate();
            }else{
                return $this->View->RenderContent('Danh mục phụ này không tồn tại!');
            }
        }
        public function DelForm($id){
            if(!$this->user->haveRole(ADMIN_PRIV)){
                return $this->View->RenderContent('Bạn không có quyền thực hiện thao tác này');
            }
            $subcate = new SubCategoryModel($this->dbcon);
            $subcate->id = $id;
            if($subcate->loadFromDB()){
                $this->View->ViewData['subcategory'] = $subcate;
                return $this->View->RenderTemplate();
            }else{
                return $this->View->RenderContent('');
            }
        }
        public function Add(){
            
        }
        public function Edit(){
            
        }
        public function Del(){
            
        }
    }