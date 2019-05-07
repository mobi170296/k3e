<?php
    namespace App\Controllers\api;
    
    use Core\Controller;
    
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use App\Models\CartItemModel;
    use App\Models\ProductModel;
    
    class cartitemController extends Controller{
        public function add($product_id, $quantity){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!is_numeric($product_id) || !is_numeric($quantity) || $quantity < 1){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                $result->header->errors = ['product_id' => 'invalid'];
                
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $product = new ProductModel($database);
                
                $product->id = $product_id;
                
                if(!$product->loadData()){
                    $result->header->code = 1;
                    $result->header->message = 'Sản phẩm không tồn tại';
                    $result->header->errors = ['product_id' => 'invalid'];
                    
                    return $this->View->RenderJSON($result);
                }
                
                $cartitem = new CartItemModel($database);
                $cartitem->client_id = $user->id;
                $cartitem->product_id = $product_id;
                
                if($cartitem->loadData()){
                    $quantity += $cartitem->quantity;
                    
                    if($quantity > $product->quantity){
                        $result->header->code = 1;
                        $result->header->message = 'Số lượng sản phẩm không đủ để thêm vào giỏ hàng';
                        $result->header->errors = ['quantity' => 'invalid'];
                        return $this->View->RenderJSON($result);
                    }
                    
                    $cartitem->delete();
                    $cartitem->quantity = $quantity;
                    $cartitem->add();
                }else{
                    $cartitem->quantity = $quantity;
                    
                    if($quantity > $product->quantity){
                        $result->header->code = 1;
                        $result->header->message = 'Số lượng sản phẩm không đủ để thêm vào giỏ hàng';
                        $result->header->errors = ['quantity' => 'invalid'];
                        return $this->View->RenderJSON($result);
                    }
                    
                    $cartitem->add();
                }
                
                $cartitem->loadProduct();
                
                $result->header->code = 0;
                $result->header->message = 'Đã thêm thành công sản phẩm ' . $cartitem->product->name . ' vào giỏ hàng';
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = ['database' => 'DBERR'];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid User';
                $result->header->errors = ['authenticate' => 'invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function gettotal(){
            $result = new \stdClass();
            $result->header = new \stdClass();
//            if(!$this->isPOST()){
//                $result->header->code = 1;
//                $result->header->message = 'invalid';
//                $result->header->errors = ['request' => 'invalid'];
//                return $this->View->RenderJSON($result);
//            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $total = $user->getCartItemTotal();
                
                $result->header->code = 0;
                $result->header->message = $total;
                $result->data = new \stdClass();
                $result->data = (int)$total;
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->errors = ['database' => 'ERROR'];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid User';
                $result->errors = ['authenticate' => 'invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function products(){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
//            if(!$this->isPOST()){
//                $result->header->code = 1;
//                $result->header->message = 'invalid';
//                $result->header->errors = ['request' => 'invalid'];
//                return $this->View->RenderJSON($result);
//            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadCartItems()){
                    $cartitems = $user->cartitems;

                    $data = [];
                    
                    foreach($cartitems as $cartitem){
                        $cartitem->loadProduct();
                        $cartitem->product->loadMainImage();
                        $dataitem = new \stdClass();
                        $dataitem->product = new \stdClass();
                        $dataitem->product->id = $cartitem->product->id;
                        $dataitem->product->name = $cartitem->product->name;
                        $dataitem->product->price = $cartitem->product->price;
                        $dataitem->product->mainimageurl = $cartitem->product->mainimage->urlpath;
                        $dataitem->quantity = (int)$cartitem->quantity;
                        
                        $data[] = $dataitem;
                    }

                    $result->header->code = 0;
                    $result->header->message = 'Có ' . count($cartitems) . ' loại sản phẩm';
                    $result->body = new \stdClass();
                    $result->body->data = new \stdClass();
                    $result->body->data = $data;
                }else{
                    $result->header->code = 0;
                    $result->header->message = 'Giỏ không có gì!';
                    $result->body = new \stdClass();
                    $result->body->data = new \stdClass();
                    $result->body->data = [];
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERROR';
                $result->header->errors = ['database' => 'error'];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid User';
                $result->header->errors = ['authenticate' => 'invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function delete($product_id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!is_numeric($product_id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                $result->header->errors = ['product_id' => 'invalid'];
                
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $cartitem = new CartItemModel($database);
                $cartitem->product_id = $product_id;
                $cartitem->client_id = $user->id;
                
                if($cartitem->loadData()){
                    $cartitem->loadProduct();
                    $cartitem->delete();
                    $result->header->code = 0;
                    $result->header->message = 'Đã bỏ ' . $cartitem->product->name . ' khỏi giỏ hàng!';
                }else{
                    $result->header->code = 1;
                    $result->header->message = 'Sản phẩm không tồn tại';
                    $result->header->errors = ['product_id' => 'Sản phẩm không tồn tại'];
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = ['database' => 'ERROR'];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'invalid user';
                $result->header->errors = ['authenticate' => 'invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function updateQuantity(){
            
        }
    }