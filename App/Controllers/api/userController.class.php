<?php
    namespace App\Controllers\api;
    use Core\Controller;
    use Library\Database\Database;
    use App\Models\Authenticate;
    use Library\Database\DBException;
    use App\Exception\AuthenticateException;
    use App\Exception\InputException;
    
    class userController extends Controller{
        public function changepassword($oldpassword, $password){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!$this->isPOST() || !is_array($password) || !is_string($password[0]) || !is_string($password[1]) || !is_string($oldpassword)){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
                return $this->View->RenderJson($result);
            }
            
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                if($oldpassword !== $_SESSION['password']){
                    throw new InputException(['Mật khẩu cũ không đúng']);
                }
                #thay doi mat khau, thay doi luon password
                $user->checkValidForPassword($password[0], $password[1]);
                if($user->isValid()){
                    $user->changePassword($password[0]);
                    $_SESSION['password'] = $password[0];
                    $result->header->code = 0;
                    $result->header->message = 'Bạn đã cập nhật thành công mật khẩu mới';
                }else{
                    throw new InputException($user->getErrorsMap());
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            } catch (InputException $e){
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsMap();
            }
            return $this->View->RenderJson($result);
        }
    }