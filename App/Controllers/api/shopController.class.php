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
    use App\Models\OrderModel;
    
    use Library\Common\Text;
    
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
                $result->header->message = 'DBException ' . $database->lastquery;
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
        
        public function editproduct($id, ProductModel $product, $product_image_chosen = [], $attribute_key = [], $attribute_value = []){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!$this->isPOST() || !is_numeric($id)){
                $result->header->code = 1;
                $result->errors = [$result->header->message = 'invalid'];
                return $this->View->RenderJSON($result);
            }
            try{
                $database = new Database;
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $oldproduct = new ProductModel($database);
                    $oldproduct->id = $id;
                    if($oldproduct->loadData()){
                        if($oldproduct->shop_id == $shop->id){
                            $oldproduct->loadMainImage();
                            $oldproduct->loadProductAttributes();
                            $oldproduct->loadProductImages();
                            $oldproductimagesset = new Set();
                            
                            foreach($oldproduct->productimages as $productimage){
                                $productimage->loadImageMap();
                                $oldproductimagesset->add($productimage->imagemap_id);
                            }
                            
                            if($oldproduct->hasSold()){
                                #chỉ được cập nhật một số thông tin có thể biến động do thị trường những thông tin thuộc về đặc tính sản phẩm thì không thể thay đổi
                                $product->checkOriginalPrice()->checkPrice()->checkQuantity()->checkWarrantyMonthsNumber();
                                if($product->isValid()){
                                    $oldproduct->update($product);
                                    $result->header->code = 0;
                                    $result->header->message = 'Sản phẩm ' . $oldproduct->name . ' đã được cập nhật';
                                }else{
                                    throw new InputException($product->getErrorsMap());
                                }
                            }else{
                                #được cập nhật tất cả các thông tin vì có thể do người dùng sai dữ liệu lúc tạo không muốn tốn thời gian để xóa và tạo lại
                                #bắt đầu kiểm tra thông tin người dùng nhập
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
                                            $productattribute->product_id = $id;
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
                                        #kiem tra quyen so huu
                                        #kiem tra anh co hop le khong, hop le la chua duoc lien ket, da duoc lien ket thi phai la cua san pham hien tai
                                        if($mainimage->user_id != $user->id || ($mainimage->linked == ImageMapModel::LINKED && $oldproduct->mainimage_id != $mainimage->id)){
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
                                                    if($imagemap->user_id != $user->id || ($imagemap->linked == ImageMapModel::LINKED && !$oldproductimagesset->contain($imagemap->id))){
                                                        $product->addErrorMessage('productimages', 'Ảnh mô tả sản phẩm không hợp lệ');
                                                        break;
                                                    }
                                                    $productimage = new ProductImageModel($database);
                                                    $productimage->product_id = $oldproduct->id;
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
                                
                                if($product->isValid()){
                                    $database->startTransaction();
                                    
                                    #<PHAN XU LY THUOC TINH SAN PHAM>
                                    #xoa di cac thuong tinh cu
                                    foreach($oldproduct->productattributes as $productattribute){
                                        $productattribute->delete();
                                    }
                                    #insert lai thuoc tinh moi
                                    foreach($productattributes as $attribute){
                                        $attribute->add();
                                    }
                                    
                                    #<PHAN XU LY ANH MO TA>
                                    $dbimagesset = new Set();
                                    foreach($oldproduct->productimages as $productimage){
                                        $productimage->loadImageMap();
                                        $dbimagesset->add($productimage->imagemap_id);
                                    }
                                    
                                    $keepimagesset = $dbimagesset->intersect($productimagesset);
                                    
                                    $delimagesset = $dbimagesset->minus($keepimagesset);
                                    
                                    #Set unlink cho những ảnh đã được xóa trong tập cũ
                                    foreach($oldproduct->productimages as $productimage){
                                        #unlink di nhung image can xoa
                                        if($delimagesset->contain($productimage->imagemap_id)){
                                            $productimage->imagemap->unLink();
                                        }
                                        #xoa het tat ca product image, van giu imagemap, anh nao khong con ton tai da duoc unlink o delimagesset
                                        $productimage->delete();
                                    }
                                    
                                    #them lai vao productimage tu input nguoi dung
                                    foreach($productimages as $productimage){
                                        $productimage->add();
                                        $productimage->loadImageMap();
                                        $productimage->imagemap->setLinked();
                                    }
                                    
                                    #<PHAN XU LY ANH DAI DIEN CHO SAN PHAM>
                                    if($mainimage->linked == ImageMapModel::UNLINKED){
                                        #go lien ket anh cu
                                        $oldproduct->mainimage->unLink();
                                        #dat lien ket anh moi
                                        $mainimage->setLinked();
                                        $product->mainimage_id = $mainimage->id;
                                    }
                                    
                                    $oldproduct->update($product);
                                    
                                    $database->commit();
                                    $result->header->code = 0;
                                    $result->header->message = 'Sản phẩm ' . $product->name . ' đã được cập nhật!';
                                }else{
                                    throw new InputException($product->getErrorsMap());
                                }
                            }
                        }else{
                            $result->header->code = 1;
                            $result->header->message = 'invalid';
                            $result->header->errors = ['invalid'];
                        }
                    } else {
                        $result->header->code = 1;
                        $result->errors = [$result->header->message = 'Sản phẩm không tồn tại'];
                    }
                }else{
                    $result->header->code = 1;
                    $result->errors = [$result->header->message = 'Chưa có cửa hàng'];
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = $ex->getMessage();
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                $result->header->errors = ['invalid'];
            } catch (InputException $e){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                $result->header->errors = $e->getErrorsMap();
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function soldout($id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!$this->isPOST() || !is_numeric($id)){
                $result->header->code = 1;
                $result->errors = [$result->header->message = 'invalid'];
                return $this->View->RenderJSON($result);
            }
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $product = new ProductModel($database);
                    $product->id = $id;
                    if($product->loadData()){
                        if($product->shop_id == $shop->id){
                            $product->setSoldOut();
                            $result->header->code = 0;
                            $result->header->message = 'Đã cập nhật tình trang hết hàng thành công cho sản phẩm ' . $product->name . '!';
                        }else{
                            $result->header->code = 1;
                            $result->errors = [$result->header->message = 'invalid'];
                        }
                    } else {
                        $result->header->code = 1;
                        $result->errors = [$result->header->message = 'Sản phẩm không tồn tại'];
                    }
                }else{
                    $result->header->code = 1;
                    $result->errors = [$result->header->message = 'Chưa có cửa hàng'];
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = $ex->getMessage();
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function deleteproduct($id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!$this->isPOST() || !is_numeric($id)){
                $result->header->code = 1;
                $result->errors = [$result->header->message = 'invalid'];
                return $this->View->RenderJSON($result);
            }
            try{
                $database = new Database;
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $product = new ProductModel($database);
                    $product->id = $id;
                    if($product->loadData()){
                        if($product->shop_id == $shop->id){
                            if($product->hasSold()){
                                $result->header->code = 1;
                                $result->header->message = 'Sản phẩm này đã có người mua bạn không thể xóa nó (gợi ý: bạn có thể đặt tình trạng hết hàng cho sản phẩm này)';
                                $result->header->errors = ['Sản phẩm này không thể xóa vì đã có người mua nó'];
                            }else{
                                $database->startTransaction();
                                $product->loadMainImage();
                                $product->loadProductImages();
                                foreach($product->productimages as $productimage){
                                    $productimage->loadImageMap();
                                    $productimage->delete();
                                    $productimage->imagemap->delete();
                                }
                                $product->delete();
                                $product->mainimage->delete();
                                $database->commit();
                                foreach($product->productimages as $productimage){
                                    unlink($productimage->imagemap->diskpath);
                                }
                                unlink($product->mainimage->diskpath);
                                $result->header->code = 0;
                                $result->header->message = 'Đã xóa thành công sản phẩm ' . $product->name;
                            }
                        }else{
                            $result->header->code = 1;
                            $result->errors = [$result->header->message = 'invalid'];
                        }
                    } else {
                        $result->header->code = 1;
                        $result->errors = [$result->header->message = 'Sản phẩm không tồn tại'];
                    }
                }else{
                    $result->header->code = 1;
                    $result->errors = [$result->header->message = 'Chưa có cửa hàng'];
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = $ex->getMessage();
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        public function productlist($name = '', $page = 1, $itemsperpage = 10){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!is_string($name) || !is_numeric($page) || !is_numeric($itemsperpage)){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
                $result->header->message = 'invalid';
                return $this->View->RenderJson($result);
            }
            
            try{
                $database = new Database;
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    #build chuoi query theo tu
                    $names = Text::getWords($name);
                    for($i=0;$i<count($names);$i++){
                        $names[$i] = "name like '%" . $database->escape($names[$i]) . "%'"; 
                    }
                    $query = implode(' and ', $names);
                    
                    $result->header->code =0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->data = [];
                    
                    $rows = $database->select('count(*) as count')->from(DB_TABLE_PRODUCT)->where('shop_id=' . (int)$shop->id . ' and (' . $query . ')')->execute();
                    
                    $result->body->total = $rows[0]->count;
                    
                    $rows = $database->select('id')->from(DB_TABLE_PRODUCT)->where('shop_id=' . (int)$shop->id . ' and (' . $query . ')')->limit(($page-1) * $itemsperpage, $itemsperpage)->orderby('created_time')->desc()->execute();
                    
                    
                    
                    foreach($rows as $row){
                        $product = new ProductModel($database);
                        $product->id = $row->id;
                        $product->loadData();
                        $product->loadMainImage();
                        
                        $p = new \stdClass();
                        $p->name = $product->name;
                        $p->link = $product->getProductLink();
                        $p->id = (int)$product->id;
                        $p->quantity = (int)$product->quantity;
                        $p->price = (int)$product->price;
                        $p->original_price = (int)$product->original_price;
                        $p->soldquantity = (int)$product->getSoldQuantity();
                        $p->warranty_months_number = (int)$product->warranty_months_number;
                        $p->mainimage_url = $product->mainimage->urlpath;
                        $result->body->data[] = $p;
                    }
                }else{
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                    $result->header->errors = ['invalid'];
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBE';
                $result->header->errors = [$ex->getMessage()];
                
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function monthordersalesstatistics($month, $year){
            #thong ke theo ngay
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    #co cua hang
                    #filter
                    $status = [OrderModel::HUY_DON_HANG, OrderModel::HUY_DO_HE_THONG, OrderModel::HUY_DO_KHONG_LAY_DUOC_HANG, OrderModel::KHONG_CON_HANG, OrderModel::NGUOI_MUA_DANG_THANH_TOAN, OrderModel::NGUOI_MUA_THANH_TOAN_THAT_BAI, OrderModel::GIAO_THAT_BAI];
                    
                    $inwhere = '(' . implode(',', $status) . ')';
                    
                    $rows = $database->select('count(distinct order.id) as totalorder, sum(order.total_price) as totalprice, day(created_time) as month')->from(DB_TABLE_ORDER)->where("order.status not in $inwhere and order.shop_id={$shop->id} and order.created_time >= '{$year}-{$month}-01' and order.created_time <= last_day('{$year}-{$month}-1')")->groupby('day(order.created_time)')->execute();
                    
                    $sales = new \stdClass();
                    $sales->totalorder = [];
                    $sales->totalprice = [];
                    for($i = 0; $i<31; $i++){
                        $sales->totalorder[] = 0;
                        $sales->totalprice[] = 0;
                    }
                    
                    foreach($rows as $row){
                        $sales->totalorder[$row->month - 1] = (int)$row->totalorder;
                        $sales->totalprice[$row->month - 1] = (int)$row->totalprice;
                    }
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->data = new \stdClass();
                    $result->body->data->result = $sales;
                    $result->body->data->month = (int)$month;
                    $result->body->data->year = (int)$year;
                }else{
                    #khong co cua hang
                }
            } catch (DBException $ex) {
                
            } catch (AuthenticateException $e){
                
            }
            return $this->View->RenderJSON($result);
        }
        
        public function yearordersalesstatistics($year){
            #chuyen thanh cac thang xem danh so
            #don hang khong phai don bi huy thi la doanh so
            #order -> orderlog -> (.created_time + .order_status)
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    #co cua hang
                    #filter
                    $status = [OrderModel::HUY_DON_HANG, OrderModel::HUY_DO_HE_THONG, OrderModel::HUY_DO_KHONG_LAY_DUOC_HANG, OrderModel::KHONG_CON_HANG, OrderModel::NGUOI_MUA_DANG_THANH_TOAN, OrderModel::NGUOI_MUA_THANH_TOAN_THAT_BAI, OrderModel::GIAO_THAT_BAI];
                    
                    $inwhere = '(' . implode(',', $status) . ')';
                    
                    $rows = $database->select('count(distinct order.id) as totalorder, sum(order.total_price) as totalprice, month(created_time) as month')->from(DB_TABLE_ORDER)->where("order.status not in $inwhere and order.shop_id={$shop->id} and order.created_time >= '{$year}-01-01' and order.created_time <= '{$year}-12-31'")->groupby('month(order.created_time)')->execute();
                    
                    $sales = new \stdClass();
                    $sales->totalorder = [];
                    $sales->totalprice = [];
                    for($i = 0; $i<12; $i++){
                        $sales->totalorder[] = 0;
                        $sales->totalprice[] = 0;
                    }
                    
                    foreach($rows as $row){
                        $sales->totalorder[$row->month - 1] = (int)$row->totalorder;
                        $sales->totalprice[$row->month - 1] = (int)$row->totalprice;
                    }
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->data = new \stdClass();
                    $result->body->data->result = $sales;
                    $result->body->data->year = (int)$year;
                }else{
                    #khong co cua hang
                }
            } catch (DBException $ex) {
                
            } catch (AuthenticateException $e){
                
            }
            return $this->View->RenderJSON($result);
        }
        
        public function monthorderrevenuestatistics($month, $year){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    #co cua hang
                    #filter
                    $status = [OrderModel::HOAN_TAT];
                    
                    $inwhere = '(' . implode(',', $status) . ')';
                    
                    $rows = $database->select('count(distinct order.id) as totalorder, sum(order.total_price) as totalprice, day(created_time) as month')->from(DB_TABLE_ORDER)->where("order.status in $inwhere and order.shop_id={$shop->id} and order.created_time >= '{$year}-{$month}-01' and order.created_time <= last_day('{$year}-{$month}-1')")->groupby('day(order.created_time)')->execute();
                    
                    
                    $sales = new \stdClass();
                    $sales->totalorder = [];
                    $sales->totalprice = [];
                    for($i = 0; $i<31; $i++){
                        $sales->totalorder[] = 0;
                        $sales->totalprice[] = 0;
                    }
                    
                    foreach($rows as $row){
                        $sales->totalorder[$row->month - 1] = (int)$row->totalorder;
                        $sales->totalprice[$row->month - 1] = (int)$row->totalprice;
                    }
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->data = new \stdClass();
                    $result->body->data->result = $sales;
                    $result->body->data->month = (int)$month;
                    $result->body->data->year = (int)$year;
                }else{
                    #khong co cua hang
                }
            } catch (DBException $ex) {
                
            } catch (AuthenticateException $e){
                
            }
            return $this->View->RenderJSON($result);
        }
        
        public function yearorderrevenuestatistics($year){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    #co cua hang
                    #filter
                    $status = [OrderModel::HOAN_TAT];
                    
                    $inwhere = '(' . implode(',', $status) . ')';
                    
                    $rows = $database->select('count(distinct order.id) as totalorder, sum(order.total_price) as totalprice, month(created_time) as month')->from(DB_TABLE_ORDER)->where("order.status in $inwhere and order.shop_id={$shop->id} and order.created_time >= '{$year}-01-01' and order.created_time <= '{$year}-12-31'")->groupby('month(order.created_time)')->execute();
                    
                    $sales = new \stdClass();
                    $sales->totalorder = [];
                    $sales->totalprice = [];
                    for($i = 0; $i<12; $i++){
                        $sales->totalorder[] = 0;
                        $sales->totalprice[] = 0;
                    }
                    
                    foreach($rows as $row){
                        $sales->totalorder[$row->month - 1] = (int)$row->totalorder;
                        $sales->totalprice[$row->month - 1] = (int)$row->totalprice;
                    }
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->data = new \stdClass();
                    $result->body->data->result = $sales;
                    $result->body->data->year = (int)$year;
                }else{
                    #khong co cua hang
                }
            } catch (DBException $ex) {
                
            } catch (AuthenticateException $e){
                
            }
            return $this->View->RenderJSON($result);
        }
        
        
        
        
        public function yearsalesandrevenuestatistics($year){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    #thong ke doanh so theo nam
                    $status = [OrderModel::HUY_DON_HANG, OrderModel::HUY_DO_HE_THONG, OrderModel::HUY_DO_KHONG_LAY_DUOC_HANG, OrderModel::KHONG_CON_HANG, OrderModel::NGUOI_MUA_DANG_THANH_TOAN, OrderModel::NGUOI_MUA_THANH_TOAN_THAT_BAI, OrderModel::GIAO_THAT_BAI];
                    
                    $inwhere = '(' . implode(',', $status) . ')';
                    
                    $rows = $database->select('count(distinct order.id) as totalorder, sum(order.total_price) as totalprice, month(created_time) as month')->from(DB_TABLE_ORDER)->where("order.status not in $inwhere and order.shop_id={$shop->id} and order.created_time >= '{$year}-01-01' and order.created_time <= '{$year}-12-31'")->groupby('month(order.created_time)')->execute();
                    
                    $sales = new \stdClass();
                    $sales->salestotalprice = [];
                    for($i = 0; $i<12; $i++){
                        $sales->salestotalprice[] = 0;
                    }
                    
                    foreach($rows as $row){
                        $sales->salestotalprice[$row->month - 1] = (int)$row->totalprice;
                    }
                    
                    
                    
                    #thong ke doanh thu theo nam
                    $status = [OrderModel::HOAN_TAT];
                    $inwhere = '(' . implode(',', $status) . ')';
                    
                    $rows = $database->select('count(distinct order.id) as totalorder, sum(order.total_price) as totalprice, month(created_time) as month')->from(DB_TABLE_ORDER)->where("order.status in $inwhere and order.shop_id={$shop->id} and order.created_time >= '{$year}-01-01' and order.created_time <= '{$year}-12-31'")->groupby('month(order.created_time)')->execute();
                    
                    $sales->revenuetotalprice = [];
                    for($i = 0; $i<12; $i++){
                        $sales->revenuetotalprice[] = 0;
                    }
                    
                    foreach($rows as $row){
                        $sales->revenuetotalprice[$row->month - 1] = (int)$row->totalprice;
                    }
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->data = new \stdClass();
                    $result->body->data->result = $sales;
                    $result->body->data->year = (int)$year;
                }else{
                    #khong co cua hang
                }
            } catch (DBException $ex) {
                
            } catch (AuthenticateException $e){
                
            }
            return $this->View->RenderJSON($result);
        }
        
        
        public function monthsalesandrevenuestatistics($year, $month){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    #thong ke doanh so theo nam
                    $status = [OrderModel::HUY_DON_HANG, OrderModel::HUY_DO_HE_THONG, OrderModel::HUY_DO_KHONG_LAY_DUOC_HANG, OrderModel::KHONG_CON_HANG, OrderModel::NGUOI_MUA_DANG_THANH_TOAN, OrderModel::NGUOI_MUA_THANH_TOAN_THAT_BAI, OrderModel::GIAO_THAT_BAI];
                    
                    $inwhere = '(' . implode(',', $status) . ')';
                    
                    $rows = $database->select('count(distinct order.id) as totalorder, sum(order.total_price) as totalprice, day(created_time) as month')->from(DB_TABLE_ORDER)->where("order.status not in $inwhere and order.shop_id={$shop->id} and order.created_time >= '{$year}-{$month}-01' and order.created_time <= last_day('{$year}-{$month}-1')")->groupby('day(order.created_time)')->execute();
                    
                    $sales = new \stdClass();
                    $sales->salestotalprice = [];
                    for($i = 0; $i<31; $i++){
                        $sales->salestotalprice[] = 0;
                    }
                    
                    foreach($rows as $row){
                        $sales->salestotalprice[$row->month - 1] = (int)$row->totalprice;
                    }
                    
                    
                    
                    #thong ke doanh thu theo nam
                    $status = [OrderModel::HOAN_TAT];
                    $inwhere = '(' . implode(',', $status) . ')';
                    
                    $rows = $database->select('count(distinct order.id) as totalorder, sum(order.total_price) as totalprice, day(created_time) as month')->from(DB_TABLE_ORDER)->where("order.status in $inwhere and order.shop_id={$shop->id} and order.created_time >= '{$year}-{$month}-01' and order.created_time <= last_day('{$year}-{$month}-1')")->groupby('day(order.created_time)')->execute();
                    
                    $sales->revenuetotalprice = [];
                    for($i = 0; $i<31; $i++){
                        $sales->revenuetotalprice[] = 0;
                    }
                    
                    foreach($rows as $row){
                        $sales->revenuetotalprice[$row->month - 1] = (int)$row->totalprice;
                    }
                    
                    $result->header->code = 0;
                    $result->header->message = 'OK';
                    $result->body = new \stdClass();
                    $result->body->data = new \stdClass();
                    $result->body->data->result = $sales;
                    $result->body->data->year = (int)$year;
                }else{
                    #khong co cua hang
                }
            } catch (DBException $ex) {
                
            } catch (AuthenticateException $e){
                
            }
            return $this->View->RenderJSON($result);
        }
    }