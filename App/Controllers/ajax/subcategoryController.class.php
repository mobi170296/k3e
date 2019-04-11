<?php
    namespace App\Controllers\ajax;
    use Core\Controller;
    use App\Models\MainCategoryList;
    use App\Models\MainCategoryModel;
    use App\Models\SubCategoryList;
    use App\Models\SubCategoryModel;
    use App\Exception\AuthenticateException;
    use App\Models\Authenticate;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\UserModel;
    
    class subcategoryController extends Controller{
        public function addform($maincategory_id){
            if(!is_numeric($maincategory_id)){
                return $this->View->RenderContent('invalid');
            }
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                
                $user = $authenticate->getUser();
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $maincategory = new MainCategoryModel($database);
                    $maincategory->id = $maincategory_id;
                    if($maincategory->loadData()){
                        $this->View->Data->maincategories = (new MainCategoryList($database))->getAll();
                        $this->View->Data->maincategory_id = $maincategory_id;
                        return $this->View->RenderPartial();
                    }else{
                        return $this->View->RenderContent('invalid');
                    }
                }else{
                    throw new AuthenticateException('', -1);
                }
            } catch (DBException $ex) {
                return $this->View->RenderContent('' . $ex->getMessage());
            } catch (AuthenticateException $e){
                return $this->View->RenderContent('invalid');
            }
        }
        public function updateform($id){
            if(!is_numeric($id)){
                return $this->View->RenderContent('invalid');
            }
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $subcategory = new SubCategoryModel($database);
                    $subcategory->id = $id;
                    if($subcategory->loadData()){
                        $this->View->Data->maincategories = (new MainCategoryList($database ))->getAll();
                        $this->View->Data->subcategory = $subcategory;
                        return $this->View->RenderPartial();
                    }else{
                        return $this->View->RenderContent('Danh mục không tồn tại');
                    }
                }else{
                    throw new AuthenticateException('', -1);
                }
            } catch (DBException $ex) {
                return $this->View->RenderContent($ex->getMessage());
            }catch(AuthenticateException $e){
                return $this->View->RenderContent('invalid');
            }
        }
        public function subcategorytable($maincategory_id){
            if(!is_numeric($maincategory_id)){
                return $this->View->RenderContent("invalid");
            }
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $maincategory = new MainCategoryModel($database);
                    $maincategory->id = $maincategory_id;
                    if($maincategory->loadData()){
                        $this->View->Data->subcategories = (new SubCategoryList($database ))->getWhere('maincategory_id=' . $maincategory_id);
                        $this->View->Data->maincategory_id = $maincategory_id;
                        return $this->View->RenderPartial();
                    }else{
                        return $this->View->RenderContent('Danh mục không tồn tại!');
                    }
                }else{
                    throw new AuthenticateException('Lỗi xác thực', -1);
                }
            } catch (DBException $ex) {
                return $this->View->RenderContent($ex->getMessage());
            } catch( AuthenticateException $e){
                return $this->View->RenderContent('Lỗi xác thực');
            }
        }
        
    }