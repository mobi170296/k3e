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
    use App\Models\ShopModel;
    use Library\Database\DBException;
    
    use App\Models\Pagination;
    
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
        
        public function ShopManage($name = '', $page = 1){
            try{
                $database = new Database();
                
                $user = (new Authenticate($database))->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $totalrows = $database->select('count(*) total')->from(DB_TABLE_SHOP)->where("name like '%". $database->escape($name) ."%'")->execute();
                    
                    if(count($totalrows)){
                        $total = $totalrows[0]->total;
                    }else{
                        $total = 0;
                    }
                    
                    $rows = $database->select('id')->from(DB_TABLE_SHOP)->where("name like '%". $database->escape($name) ."%'")->limit(($page - 1) * 2, 2)->execute();
                    
                    $shops = [];
                    foreach($rows as $row){
                        $shop = new ShopModel($database);
                        $shop->id = $row->id;
                        if($shop->loadData()){
                            $shop->loadAvatar();
                            $shops[] = $shop;
                        }
                    }
                    
                    
                    $this->View->TemplateData->pagination = new Pagination($page, $total, ['name' => $name], 2);
                    $this->View->Data->shops = $shops;
                    
                    return $this->View->RenderTemplate();
                }else{
                    $this->View->Data->ErrorMessage = 'Khong co quyen thuc hien';
                    return $this->View->RenderTemplate('_error');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        public function UserManage($phone = '', $page = 1){
            try{
                $database = new Database();
                
                $user = (new Authenticate($database))->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $totalrows = $database->select('count(*) total')->from(DB_TABLE_USER)->where("phone like '%". $database->escape($phone) ."%'")->execute();
                    
                    if(count($totalrows)){
                        $total = $totalrows[0]->total;
                    }else{
                        $total = 0;
                    }
                    
                    $rows = $database->select('id')->from(DB_TABLE_USER)->where("phone like '%". $database->escape($phone) ."%'")->limit(($page - 1) * 2, 2)->execute();
                    
                    $users = [];
                    foreach($rows as $row){
                        $user = new UserModel($database);
                        $user->id = $row->id;
                        if($user->loadData()){
                            $user->loadAvatar();
                            $users[] = $user;
                        }
                    }
                    
                    
                    $this->View->TemplateData->pagination = new Pagination($page, $total, ['phone' => $phone], 2);
                    $this->View->Data->users = $users;
                    
                    return $this->View->RenderTemplate();
                }else{
                    $this->View->Data->ErrorMessage = 'Khong co quyen thuc hien';
                    return $this->View->RenderTemplate('_error');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR ' . $database->lastquery;
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
    }