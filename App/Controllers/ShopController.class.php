<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use Library\Database\DBException;
    use Library\Database\Database;
    use App\Models\MainCategoryList;
    use App\Models\SubCategoryList;
    
    use App\Models\ProductModel;
    
    
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
                $this->View->Data->ErrorMessage = $ex->getMessage();
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
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            } catch(AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        public function AddProduct(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $this->View->Data->maincategories = (new MainCategoryList($database))->getAll();
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        public function EditProduct($id){
            if(!is_numeric($id) || (int)$id != (float)$id){
                $this->View->Data->ErrorMessage = 'Không tìm thấy trang này';
                return $this->View->RenderTemplate('_error');
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $product = new ProductModel($database);
                    
                    $product->id = (int)$id;
                    
                    if($product->loadData()){
                        if($product->shop_id == $shop->id){
                            $this->View->Data->hasBought = $product->hasBought();
                            $product->loadSubcategory();
                            $product->loadMainImage();
                            $product->loadProductImages();
                            $product->loadProductAttributes();
                            foreach($product->productimages as $productimage){
                                $productimage->loadImageMap();
                            }
                            $product->subcategory->loadMainCategory();
                            $this->View->Data->maincategories = (new MainCategoryList($database))->getAll();
                            $this->View->Data->subcategories = (new SubCategoryList($database))->getWhere('maincategory_id='. $product->subcategory->maincategory_id);
                            $this->View->Data->product = $product;
                            return $this->View->RenderTemplate();
                        }else{
                            $this->View->Data->ErrorMessage = 'Không tìm thấy trang này';
                            return $this->View->RenderTemplate('_error');
                        }
                    }else{
                        $this->View->Data->ErrorMessage = 'Sản phẩm không tồn tại';
                        return $this->View->RenderTemplate('_error');
                    }
                }else{
                    $this->View->Data->ErrorMessage = 'Bạn không có quyền thực hiện thao tác này';
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        public function ProductList(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $user->shop->loadProducts();
                    $this->View->Data->products = $user->shop->products;
                    
                    foreach($user->shop->products as $product){
                        $product->loadMainImage();
                        $product->loadProductAttributes();
                        $product->loadProductImages();
                        foreach($product->productimages as $productimage){
                            $productimage->setDatabase($database);
                            $productimage->loadImageMap();
                        }
                    }
                    
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User', ['backurl' => '/Shop/ProductList']);
            }
        }
        
        public function View($id){
            return $this->View->RenderContent($id);
        }
    }