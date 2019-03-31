<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\UserModel;
    use App\Exception\DBException;
    use App\Exception\InputException;
    
    class UserController extends Controller{
        public function __init(){
            $this->__init_db_authenticate();
        }
        public function Index(){
            return $this->View->RenderTemplate();
        }
        public function Register($action, UserModel $user){
            if($this->user->isLogin()){
                $this->redirectToAction('Home', 'Index', null);
            }
            $user->dbcon = $this->dbcon;
            $this->View->ViewData['action'] = $action;
            if($action != null){
                try{
                    $user->register();
                    $_SESSION['username'] = $user->username;
                    $_SESSION['password'] = $user->password[0];
                    return $this->redirectToAction('Home', 'Index', null);
                } catch (InputException $ie) {
                    $this->View->ViewData['model'] = $user;
                    $this->View->ViewData['error'] = $ie;
                } catch(DBException $de){
                    $this->View->ViewData['model'] = $user;
                    $this->View->ViewData['error'] = $de;
                }
                return $this->View->RenderTemplate();
            }else{
                return $this->View->RenderTemplate();
            }
        }
        public function Login($action, $username, $password){
            if($this->user->isLogin()){
                return $this->redirectToAction('Home', 'Index', null);
            }
            if($action!=null){
                $user = new UserModel($this->dbcon);
                $user->username = $username;
                $user->password = $password;
                if($user->login()){
                    $_SESSION['username'] = $username;
                    $_SESSION['password'] = $password;
                    return $this->redirectToAction('Home', 'Index', null);
                }else{
                    $this->View->ViewData['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
                    return $this->View->RenderTemplate();
                }
            }else{
                return $this->View->RenderTemplate();
            }
        }
        public function Info($action, UserModel $user){
            if($this->user->isLogin()){
                $this->View->ViewData['action'] = $action;
                $this->View->ViewData['input'] = $user;
                if($action != null){
                    try{
                        $this->user->update($user);
                        $this->View->ViewData['success'] = 'Bạn đã cập nhật thông tin thành công';
                    } catch (DBException $de) {
                        $this->View->ViewData['error'] = $de;
                    } catch (InputException $ie){
                        $this->View->ViewData['error'] = $ie;
                    }
                }
                $this->View->ViewData['user'] = $this->user;
                return $this->View->RenderTemplate();
            }else{
                $this->redirectToAction('Home', 'Index', null);
            }
        }
        public function Logout(){
            unset($_SESSION['username']);
            unset($_SESSION['password']);
            $this->redirectToAction('Home', 'Index', null);
        }
        public function Shop(){
            
        }
    }