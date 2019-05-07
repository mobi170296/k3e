<?php
    namespace App\Controllers;
    use Core\Controller;
    
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\MainCategoryModel;
    use App\Models\SubCategoryModel;
    use App\Models\ProductModel;
    
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    
    class ProductController extends Controller{
        public function Index(){
            
        }
        
        public function ListByMainCategory($id, $page = 1){
            try{
                if(!isset($id) || !is_numeric($id)){
                    throw new \Exception('Trang này không tìm thấy');
                }
                $database = new Database();
                
                $maincategory = new MainCategoryModel($database);
                $maincategory->id = (int)$id;
                if($maincategory->loadData()){
                    $maincategory->loadProducts(($page - 1) * 10, 10);
                    foreach($maincategory->products as $product){
                        $product->loadMainImage();
                        $product->loadWard();
                        $product->ward->loadDistrict();
                        $product->ward->district->loadProvince();
                    }
                    $this->View->Data->maincategory = $maincategory;
                    return $this->View->RenderTemplate();
                }else{
                    throw new \Exception('Trang này không tìm thấy');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR' . $database->lastquery;
                return $this->View->RenderTemplate('_error');
            } catch (\Exception $e){
                $this->View->Data->ErrorMessage = $e->getMessage();
                return $this->View->RenderTemplate("_error");
            }
        }
        
        public function ListBySubCategory($id, $page = 1){
            try{
                if(!isset($id) || !is_numeric($id)){
                    throw new \Exception('Trang này không tìm thấy');
                }
                $database = new Database();
                
                $subcategory = new SubCategoryModel($database);
                $subcategory->id = (int)$id;
                if($subcategory->loadData()){
                    $subcategory->loadMainCategory();
                    $this->View->Data->subcategory = $subcategory;
                    $subcategory->loadProducts(($page - 1) * 10, 10);
                    foreach($subcategory->products as $product){
                        $product->loadMainImage();
                        $product->loadWard();
                        $product->ward->loadDistrict();
                        $product->ward->district->loadProvince();
                    }
                    return $this->View->RenderTemplate();
                }else{
                    throw new \Exception('Trang này không tìm thấy');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (\Exception $e){
                $this->View->Data->ErrorMessage = $e->getMessage();
                return $this->View->RenderTemplate("_error");
            }
        }
        
        public function View($id){
            try{
                if($id == null || !is_numeric($id)){
                    throw new \Exception('Trang này không tồn tại');
                }
                
                $database = new Database();
                
                try{
                    $user = (new Authenticate($database))->getUser();
                } catch (AuthenticateException $e) {
                    $user = null;
                }
                
                $product = new ProductModel($database);
                $product->id = (int)$id;
                
                if($product->loadData()){
                    $product->loadMainImage();
                    $product->loadProductAttributes();
                    $product->loadProductImageMaps();
                    $product->loadShop();
                    $product->shop->loadBackground();
                    $product->shop->loadAvatar();
                    $product->loadWard();
                    $product->ward->loadDistrict();
                    $product->ward->district->loadProvince();
                    $this->View->Data->user = $user;
                    $this->View->Data->product = $product;
                    return $this->View->RenderTemplate();
                }else{
                    throw new \Exception('Trang này không tồn tại');
                }
            } catch(DBException $e){
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (\Exception $ex) {
                $this->View->Data->ErrorMessage = $ex->getMessage();
                return $this->View->RenderTemplate('_error');
            }
        }
    }