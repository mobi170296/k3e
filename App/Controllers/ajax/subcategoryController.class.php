<?php
    namespace App\Controllers\ajax;
    use Core\Controller;
    use App\Models\MainCategoryList;
    use App\Models\SubCategoryList;
    use App\Models\SubCategoryModel;
    use App\Exception\AuthenticateException;
    use App\Models\Authenticate;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\UserModel;
    
    class subcategoryController extends Controller{
        public function addform(){
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                
                $user = $authenticate->getUser();
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $this->View->Data->maincategorylist = (new MainCategoryList($database))->getAll();
                    return $this->View->RenderPartial();
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
            
        }
        public function subcategorytable($maincategory_id){
            
        }
    }