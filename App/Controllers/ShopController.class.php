<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use Library\Database\DBException;
    use Library\Database\Database;
    
    
    class ShopController extends Controller{
        public function Index(){
            
        }
        
        public function Open(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->isMerchant()){
                    
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Info', 'Shop');
                }
            } catch (DBException $ex) {
                return $this->View->RenderTemplate('_error');
            } catch(AuthenticateException $e){
                return $this->redirectToAction('Index', 'Home');
            }
        }
        
        public function Info(){
            
        }
    }