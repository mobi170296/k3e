<?php
    namespace App\Controllers;
    use App\Models\SubCategoryModel;
    use App\Models\MainCategoryModel;
    use App\Models\MainCategoryList;
    use Library\Database\Database;
    use App\Exception\AuthenticateException;
    use App\Models\Authenticate;
    use App\Models\UserModel;
    use Core\Controller;
    use Library\Database\DBException;
    
    class AdminController extends Controller{
        public function Index(){
            $this->redirectToAction('MainCategory', 'Admin');
        }
        public function MainCategory(){
            try{
                $database = new Database();
            } catch (\Exception $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate("_error");
            }
            
            try{
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $list = new MainCategoryList($database);
                    $this->View->Data->maincategorylist = $list->getAll();
                    return $this->View->RenderTemplate();
                }else{
                    $this->View->Data->ErrorMessage = 'Trang này không tồn tại!';
                    return $this->View->RenderTemplate('_error');
                }
            } catch (AuthenticateException $ex) {
                $this->View->Data->ErrorMessage = 'Chưa đăng nhập không thể truy cập trang';
                return $this->View->RenderTemplate("_error");
            } catch(DBException $e){
                $this->View->Data->ErrorMessage = $e->getMessage();
                return $this->View->RenderTemplate("_error");
            }
        }
        public function SubCategory(){
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $this->View->Data->maincategorylist = (new MainCategoryList($database))->getAll();
                    return $this->View->RenderTemplate();
                }else{
                    throw new AuthenticateException('', -1);
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                $this->View->Data->ErrorMessage = 'Lỗi xác thực';
                return $this->View->RenderTemplate('_error');
            }
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