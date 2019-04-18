<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use Library\Database\DBException;
    use Library\Database\Database;
    
    
    class ShopController extends Controller{
        public function Index(){
            return $this->redirectToAction('Info', 'Shop');
        }
        
        public function Open(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->isMerchant()){
                    if($user->getDeliveryAddressesTotal() == 0){
                        return $this->View->RenderTemplate('RequireAddress');
                    }else{
                        return $this->View->RenderTemplate();
                    }
                }else{
                    return $this->redirectToAction('Info', 'Shop');
                }
            } catch (DBException $ex) {
                return $this->View->RenderTemplate('_error');
            } catch(AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        public function Info(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $this->View->Data->shop = $user->shop;
                    return $this->View->RenderTemplate();
                } else {
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                return $this->View->RenderTemplate('_error');
            } catch(AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        public function ShopProfile($id){
            return $this->View->RenderContent('ID Shop: ' . $id);
        }
    }