<?php
    namespace App\Controllers\ajax;
    use Core\Controller;
    use App\Models\MainCategoryModel;
    use App\Exception\AuthenticateException;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\Authenticate;
    use App\Models\UserModel;
    use App\Models\MainCategoryList;
    
    class maincategoryController extends Controller{
        public function addform(){
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    return $this->View->RenderPartial();
                }else{
                    return $this->View->RenderContent('invalid');
                }
            } catch (DBException $ex) {
                return $this->View->RenderContent('' . $ex->getMessage());
            } catch (AuthenticateException $ex){
                return $this->View->RenderContent('invalid');
            }
        }
        public function updateform($id){
            try{
                if(!is_numeric($id)){
                    return $this->View->RenderContent("invalid");
                }
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $maincategory = new MainCategoryModel($database);
                    $maincategory->id = $id;
                    $list = new MainCategoryList($database);
                    $this->View->Data->maincategorylist = $list->getAll();
                    if($maincategory->loadData()){
                        $this->View->Data->maincategory = $maincategory;
                        return $this->View->RenderPartial();
                    }else{
                        return $this->View->RenderContent('Danh mục này không tồn tại!');
                    }
                }else{
                    return $this->View->RenderContent("invalid");
                }
            } catch (DBException $ex) {
                return $this->View->RenderContent('' . $ex->getMessage());
            } catch (AuthenticateException $ex){
                return $this->View->RenderContent('invalid');
            }
        }
        public function delform($id){
            try{
                if(!is_numeric($id)){
                    return $this->View->RenderContent("invalid");
                }
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $maincategory = new MainCategoryModel($database);
                    $maincategory->id = $id;
                    $list = new MainCategoryList($database);
                    $this->View->Data->maincategorylist = $list->getAll();
                    if($maincategory->loadData()){
                        $this->View->Data->maincategory = $maincategory;
                        return $this->View->RenderPartial();
                    }else{
                        return $this->View->RenderContent('Danh mục này không tồn tại!');
                    }
                }else{
                    return $this->View->RenderContent("invalid");
                }
            } catch (DBException $ex) {
                return $this->View->RenderContent('' . $ex->getMessage());
            } catch (AuthenticateException $ex){
                return $this->View->RenderContent('invalid');
            }
        }
        public function maincategorytable(){
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $maincategorylist = (new MainCategoryList($database))->getAll();
                    $this->View->Data->maincategorylist = $maincategorylist;
                    return $this->View->RenderPartial();
                }else{
                    return $this->View->RenderContent('invalid');
                }
            } catch (DBException $ex) {
                return $this->View->RenderContent($ex->getMessage());
            } catch (AuthenticateException $e){
                return $this->View->RenderContent('invalid');
            }
        }
    }