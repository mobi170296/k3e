<?php
    namespace App\Controllers\api;
    use Core\Controller;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use App\Exception\InputException;
    use App\Models\ShopModel;
    
    use App\Models\ImageMapModel;
    use App\Models\ProductAttributeModel;
    use App\Models\ProductModel;
    use Library\Common\Set;
    use App\Models\ProductImageModel;
    
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
        
        public function addproduct($product_image_chosen, ProductModel $product, $attribute_key = [], $attribute_value = []){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!$this->isPOST()){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                $result->header->errors = ['invalid'];
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    #Kiem tra attribute key value
                    if(!is_array($attribute_key) || !is_array($attribute_value) || count($attribute_key) != count($attribute_value)){
                        $product->addErrorMessage('productattribute', 'Thuộc tính sản phẩm không hợp lệ');
                    }else{
                        $productattributes = [];
                        $length = count($attribute_key);

                        for($i = 0; $i<$length; $i++){
                            $productattribute = new ProductAttributeModel($database);
                            if(isset($attribute_key[$i]) && isset($attribute_value[$i])){
                                $productattribute->attributename = $attribute_key[$i];
                                $productattribute->attributevalue = $attribute_value[$i];
                                $productattribute->norder = $i+1;
                                $productattribute->checkKey()->checkValue();
                                if(!$productattribute->isValid()){
                                    $product->addErrorMessage("productattribute[$i]", "Cặp thuộc tính $i không hợp lệ");
                                }
                                $productattributes[] = $productattribute;
                            }else{
                                $product->addErrorMessage('productattribute', 'Thuộc tính sản phẩm không hợp lệ');
                                break;
                            }
                        }
                    }
                    
                    
                    #Kiem tra Anh dai dien cho san pham
                    if(is_numeric($product->mainimage_id)){
                        $mainimage = new ImageMapModel($database);
                        $mainimage->id = $product->mainimage_id;
                        if($mainimage->loadData()){
                            if($mainimage->user_id != $user->id || $mainimage->linked == ImageMapModel::LINKED){
                                $product->addErrorMessage('mainimage_id', 'Ảnh đại diện cho sản phẩm không hợp lệ');
                            }
                        }else{
                            $product->addErrorMessage('mainimage_id', 'Ảnh đại diện cho sản phẩm không tồn tại');
                        }
                    }else{
                        $product->addErrorMessage('mainimage_id', 'Ảnh đại diện cho sản phẩm không hợp lệ');
                    }
                    
                    #Kiem tra Anh mo ta san pham, ít nhất 5 ảnh nhiều nhất 30 ảnh
                    if(is_array($product_image_chosen)){
                        $productimagesset = new Set($product_image_chosen);
                        if($productimagesset->isInteger()){
                            if($productimagesset->count() >= 3 && $productimagesset->count() <= 30){
                                $productimages = [];
                                $productimageidsarray = $productimagesset->toArray();
                                $norder = 1;
                                foreach($productimageidsarray as $productimageid){
                                    $imagemap = new ImageMapModel($database);
                                    $imagemap->id = $productimageid;
                                    if($imagemap->loadData()){
                                        if($imagemap->user_id != $user->id || $imagemap->linked == ImageMapModel::LINKED){
                                            $product->addErrorMessage('productimages', 'Ảnh mô tả sản phẩm không hợp lệ');
                                            break;
                                        }
                                        $productimage = new ProductImageModel($database);
                                        $productimage->imagemap_id = $imagemap->id;
                                        $productimage->norder = $norder++;
                                        $productimages[] = $productimage;
                                    }else{
                                        $product->addErrorMessage('productimages', 'Ảnh mô tả sản phẩm không hợp lệ');
                                        break;
                                    }
                                }
                            }else{
                                $product->addErrorMessage('productimages', 'Số ảnh của mô tả sản phẩm phải từ 3 đến 30 ảnh');
                            }
                        }else{
                            $product->addErrorMessage('productimages', 'Ảnh mô tả sản phẩm không hợp lệ');
                        }
                    }else{
                        $product->addErrorMessage('productimages', 'Ảnh mô tả sản phẩm không hợp lệ');
                    }
                    
                    #Kiem tra thong tin co ban ve san pham
                    $product->database = $database;
                    $product->checkName()->checkDescription()->checkOriginalPrice()->checkPrice()->checkQuantity()->checkWeight()->checkLength()->checkWidth()->checkHeight()->checkWarrantyMonthsNumber()->checkSubcategoryId();
                    
                    #Kết luận của kiểm tra tính hợp lệ dữ liệu đầu vào
                    if($product->isValid()){
                        #Hop le du lieu dau vao ---> thuc hien transaction de thao tac voi CSDL integrity
                        $database->startTransaction();
                        $product->shop_id = $user->shop->id;
                        
                        #them bang product truoc
                        $product->add();
                        
                        #lay id cua product vua duoc them vao
                        $product_id = $database->lastInsertId();
                        
                        #set linked cho anh dai dien
                        $imagemap = new ImageMapModel($database);
                        $imagemap->id = $product->mainimage_id;
                        $imagemap->setLinked();
                        
                        
                        foreach($productattributes as $productattribute){
                            $productattribute->product_id = $product_id;
                            $productattribute->add();
                        }
                        
                        foreach($productimages as $productimage){
                            $productimage->product_id = $product_id;
                            $productimage->add();
                            $imagemap = new ImageMapModel($database);
                            $imagemap->id = $productimage->imagemap_id;
                            $imagemap->setLinked();
                        }
                        
                        $database->commit();
                        $result->header->code = 0;
                        $result->header->message = 'Đã thêm thành công sản phẩm "' . $product->name . '"!';
                    }else{
                        throw new InputException($product->getErrorsMap());
                    }
                }else{
                    $result->header->code = 1;
                    $result->header->message = 'Bạn chưa có cửa hàng';
                    $result->header->errors = ['Bạn chưa có cửa hàng'];
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBException';
                $result->header->errors = [$ex->getMessage()];
                $database->rollback();
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'invalid user';
                $result->header->errors = ['invalid user'];
                $database->rollback();
            } catch (InputException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid input';
                $result->header->errors = $e->getErrorsMap();
                $database->rollback();
            }
            
            return $this->View->RenderJSON($result);
        }
    }