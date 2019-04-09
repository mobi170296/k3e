<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\UserModel;
    use Library\Database\DBException;
    use Library\Database\Database;
    use App\Exception\InputException;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use Library\Database\DBDateTime;
    
    class UserController extends Controller{
        public function Index(){
            return $this->View->RenderTemplate();
        }
        
        public function Register($register, $day, $month, $year, UserModel $input){
            try{
                $database = new Database();
                new Authenticate($database);
                return $this->redirectToAction('Index', 'Home');
            } catch (AuthenticateException $ex) {
                #XAC THUC THAT BAI CHO PHEP TRUY CAP DANG KY
            } catch (DBException $e){
                $this->View->Data->ErrorMessage = $e->getMessage();
                return $this->View->RenderTemplate('_error');
            }
            
            if($register != null && $this->isPOST()){
                #SEND ACTION
                try{
                    $input->setDatabase($database);
                    $input->birthday = new DBDateTime($day, $month, $year);
                    $input->checkValidForUserName()->checkValidForUserNameExists()->checkValidForPassword($input->password[0], $input->password[1])->checkValidForEmail()->checkValidForPhone()->checkValidForBirthday()->checkValidForGender()->checkValidForLastName()->checkValidForFirstName();
                    
                    if($input->getErrorsLength()){
                        $this->View->Data->Model = $input;
                        throw new InputException($input->getErrorsMap());
                    }
                    
                    $input->password = $input->password[0];
                    $input->register();
                    
                    $_SESSION['username'] = $input->username;
                    $_SESSION['password'] = $input->password;
                    
                    return $this->redirectToAction('Index', 'Home');
                } catch (DBException $ex) {
                    $this->View->Data->ErrorMessage = $ex->getMessage();
                    return $this->View->RenderTemplate("_error");
                } catch(InputException $ex){
                    $this->View->Data->ErrorsMap = $ex->getErrorsMap();
                    return $this->View->RenderTemplate();
                }
            }else{
                return $this->View->RenderTemplate();
            }
        }
        public function Login($login, $username, $password){
            try{
                $database = new Database();
                new Authenticate($database);
                return $this->redirectToAction('Index', 'Home');
            } catch (DBException $ex) {
                return $this->View->RenderTemplate("_error");
            } catch (AuthenticateException $e){
                #normal access 
            }
            
            try{
                if($login && $this->isPOST()){
                    $input = new UserModel($database);
                    $input->username = $username;
                    $input->password = $password;
                    $input->checkValidForUserName()->checkValidForPassword();
                    
                    if($input->getErrorsLength()){
                        throw new InputException($input->getErrorsMap());
                    }
                    
                    $input->standardization();
                    if($input->login()){
                        if($input->isLocked()){
                            $this->View->Data->ErrorMessage = 'Tài khoản của bạn đã bị khóa, vui lòng liên hệ với quản trị viên để được trợ giúp';
                            return $this->View->RenderTemplate();
                        }else{
                            $_SESSION['username'] = $username;
                            $_SESSION['password'] = $password;
                            return $this->redirectToAction('Index', 'Home');
                        }
                    }else{
                        $this->View->Data->ErrorMessage = 'Tên đăng nhập hoặc tài khoản không đúng';
                        throw new InputException(null);
                    }
                }else{
                    return $this->View->RenderTemplate();
                }
            } catch (InputException $ex) {
                $this->View->Data->ErrorsMap = $ex->getErrorsMap();
                return $this->View->RenderTemplate();
            } catch (DBException $e){
                $this->View->Data->ErrorMessage = $e->getMessage();
                return $this->View->RenderTemplate("_error");
            }
        }
        public function Info($action, UserModel $user){
            return $this->View->RenderTemplate();
        }
        public function Logout(){
            unset($_SESSION['username']);
            unset($_SESSION['password']);
            $this->redirectToAction('Index', 'Home');
        }
        public function Shop(){
            
        }
        public function ChangePassword(){
            
        }
        public function Orders(){
            
        }
        public function DeliveryAddresses(){
            
        }
    }