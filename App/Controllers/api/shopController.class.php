<?php
    namespace App\Controllers\api;
    use Core\Controller;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use App\Exception\InputException;
    use App\Models\ShopModel;
    
    class shopController extends Controller{
        public function open($name, $description){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!$this->isPOST()){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if(!$user->isMerchant()){
                    $shop = new ShopModel($database);
                    $shop->name = $name;
                    $shop->description = $description;
                    $shop->owner_id = $user->id;
                    $shop->checkName()->checkDescription();
                    $addressestotal = $user->getDeliveryAddressesTotal();
                    
                    if($addressestotal==0){
                        $shop->addErrorMessage('deliveryaddress', 'Bạn chưa thiết lập địa chỉ vận chuyển!');
                    }
                    
                    if($shop->isValid()){
                        $shop->open();
                        $result->header->code = 0;
                        $result->header->message = 'Chúc mừng bạn đã đăng ký cửa hàng thành công';
                    }else{
                        throw new InputException($shop->getErrorsMap(), -1);
                    }
                }else{
                    $result->header->code = 1;
                    $result->header->message = 'Bạn đã là merchant không thể thực hiện thao tác này';
                    $result->header->errors = ['invalid'];
                }
            }catch(DBException $e){
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                $result->header->errors = [$e->getMessage()];
            }catch(InputException $e){
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsMap();
                $result->header->message = 'Thông tin cung cấp không hợp lệ';
            }catch(AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['Invalid user!'];
            }
            
            return $this->View->RenderJson($result);
        }
        
        
        public function update(ShopModel $input){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!$this->isPOST()){
                $result->header->errors = ['invalid'];
                $result->header->code = 1;
                return $this->View->RenderJson($result);
            }
            
            try{
              $database = new Database();
              $user = (new Authenticate($database))->getUser();
              
              if($user->isMerchant()){
                  $user->loadShop();
                  
                  $input->setDatabase($database);
                  $input->checkName()->checkDescription();
                  
                  if($input->isValid()){
                      $user->shop->update($input);
                      $result->header->code = 0;
                      $result->header->message = 'Đã cập nhật thành công thông tin cửa hàng';
                  }else{
                      throw new InputException($input->getErrorsMap());
                  }
              }else{
                  $result->header->code = 1;
                  $result->header->errors = ['Bạn chưa có cửa hàng'];
              }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
                return $this->View->RenderJson($result);
            } catch(AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            } catch(InputException $e){
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsMap();
            }
            
            return $this->View->RenderJson($result);
        }
        
        public function addproduct(){
            
        }
    }