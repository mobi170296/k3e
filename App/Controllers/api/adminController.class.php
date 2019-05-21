<?php
    namespace App\Controllers\api;
    
    use Core\Controller;
    use App\Models\ProductModel;
    
    use App\Models\Authenticate;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Exception\AuthenticateException;
    use App\Models\UserModel;
    
    use App\Models\ShopModel;
    
    use App\Models\AssessmentModel;
    
    class adminController extends Controller{
        public function lockproduct($id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!is_numeric($id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->haveRole(UserModel::ADMIN_ROLE)){
                    
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                    return $this->View->RenderJSON($result);
                }else{
                    $product = new ProductModel($database);
                    
                    $product->id = $id;
                    
                    if($product->loadData()){
                        $product->lock();
                        $result->header->code = 0;
                        $result->header->message = 'Đã khóa sản phẩm thành công';
                        return $this->View->RenderJSON($result);
                    }else{
                        
                        $result->header->code = 1;
                        $result->header->message = 'Sản phẩm không tồn tại';
                        return $this->View->RenderJSON($result);
                    }
                }
            } catch (DBException $ex) {

                $result->header->code = 1;
                $result->header->message = 'DBERR';
                return $this->View->RenderJSON($result);
            } catch (AuthenticateException $e){
                
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
        }
        
        
        public function unlockproduct($id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!is_numeric($id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->haveRole(UserModel::ADMIN_ROLE)){
                    
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                    return $this->View->RenderJSON($result);
                }else{
                    $product = new ProductModel($database);
                    
                    $product->id = $id;
                    
                    if($product->loadData()){
                        $product->unlock();
                        $result->header->code = 0;
                        $result->header->message = 'Đã mở khóa sản phẩm thành công';
                        return $this->View->RenderJSON($result);
                    }else{
                        
                        $result->header->code = 1;
                        $result->header->message = 'Sản phẩm không tồn tại';
                        return $this->View->RenderJSON($result);
                    }
                }
            } catch (DBException $ex) {

                $result->header->code = 1;
                $result->header->message = 'DBERR';
                return $this->View->RenderJSON($result);
            } catch (AuthenticateException $e){
                
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
        }
        
        
        public function lockshop($id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!is_numeric($id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->haveRole(UserModel::ADMIN_ROLE)){
                    
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                    return $this->View->RenderJSON($result);
                }else{
                    $shop = new ShopModel($database);
                    
                    $shop->id = $id;
                    
                    if($shop->loadData()){
                        $shop->lock();
                        $result->header->code = 0;
                        $result->header->message = 'Đã khóa cửa hàng thành công';
                        return $this->View->RenderJSON($result);
                    }else{
                        
                        $result->header->code = 1;
                        $result->header->message = 'Cửa hàng không tồn tại';
                        return $this->View->RenderJSON($result);
                    }
                }
            } catch (DBException $ex) {

                $result->header->code = 1;
                $result->header->message = 'DBERR' . $database->lastquery;
                return $this->View->RenderJSON($result);
            } catch (AuthenticateException $e){
                
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
        }
        
        public function unlockshop($id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!is_numeric($id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->haveRole(UserModel::ADMIN_ROLE)){
                    
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                    return $this->View->RenderJSON($result);
                }else{
                    $shop = new ShopModel($database);
                    
                    $shop->id = $id;
                    
                    if($shop->loadData()){
                        $shop->unlock();
                        $result->header->code = 0;
                        $result->header->message = 'Đã mở khóa cửa hàng thành công';
                        return $this->View->RenderJSON($result);
                    }else{
                        
                        $result->header->code = 1;
                        $result->header->message = 'Cửa hàng không tồn tại';
                        return $this->View->RenderJSON($result);
                    }
                }
            } catch (DBException $ex) {

                $result->header->code = 1;
                $result->header->message = 'DBERR' . $database->lastquery;
                return $this->View->RenderJSON($result);
            } catch (AuthenticateException $e){
                
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
        }
        
        
        public function lockuser($id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!is_numeric($id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->haveRole(UserModel::ADMIN_ROLE)){
                    
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                    return $this->View->RenderJSON($result);
                }else{
                    if($user->id === $id){
                        $result->header->code = 1;
                        $result->header->message = 'Bạn không thể khóa chính mình?';
                        return $this->View->RenderJSON($result);
                    }
                    
                    $luser = new UserModel($database);
                    
                    $luser->id = $id;
                    
                    if($luser->loadData()){
                        $luser->lock();
                        $result->header->code = 0;
                        $result->header->message = 'Đã khóa người dùng thành công';
                        return $this->View->RenderJSON($result);
                    }else{
                        
                        $result->header->code = 1;
                        $result->header->message = 'Người dùng không tồn tại';
                        return $this->View->RenderJSON($result);
                    }
                }
            } catch (DBException $ex) {

                $result->header->code = 1;
                $result->header->message = 'DBERR' . $database->lastquery;
                return $this->View->RenderJSON($result);
            } catch (AuthenticateException $e){
                
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
        }
        
        public function unlockuser($id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!is_numeric($id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->haveRole(UserModel::ADMIN_ROLE)){
                    
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                    return $this->View->RenderJSON($result);
                }else{
                    $user = new UserModel($database);
                    
                    $user->id = $id;
                    
                    if($user->loadData()){
                        $user->unlock();
                        $result->header->code = 0;
                        $result->header->message = 'Đã mở khóa người dùng thành công';
                        return $this->View->RenderJSON($result);
                    }else{
                        
                        $result->header->code = 1;
                        $result->header->message = 'Người dùng không tồn tại';
                        return $this->View->RenderJSON($result);
                    }
                }
            } catch (DBException $ex) {

                $result->header->code = 1;
                $result->header->message = 'DBERR' . $database->lastquery;
                return $this->View->RenderJSON($result);
            } catch (AuthenticateException $e){
                
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
        }
        
        
        public function deleteassessment($order_id, $product_id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!is_numeric($product_id) || !is_numeric($order_id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->haveRole(UserModel::ADMIN_ROLE)){
                    
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                    return $this->View->RenderJSON($result);
                }else{
                    $assessment = new AssessmentModel($database);
                    $assessment->order_id = $order_id;
                    $assessment->product_id = $product_id;
                    
                    $assessment->delete();
                    

                    $result->header->code = 0;
                    $result->header->message = 'Đã xóa đánh giá';
                    return $this->View->RenderJSON($result);
                }
            } catch (DBException $ex) {

                $result->header->code = 1;
                $result->header->message = 'DBERR' . $database->lastquery;
                return $this->View->RenderJSON($result);
            } catch (AuthenticateException $e){
                
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
        }
    }