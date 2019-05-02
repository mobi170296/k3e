<?php
    namespace App\Controllers;
    use Core\Controller;
    
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\MainCategoryModel;
    use App\Models\SubCategoryModel;
    
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
                    }
                    $this->View->Data->maincategory = $maincategory;
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
        
        public function ListBySubCategory($id){
            try{
                if(!isset($id) || !is_numeric($id)){
                    throw new \Exception('Trang này không tìm thấy');
                }
                $database = new Database();
                
                $subcategory = new SubCategoryModel($database);
                $subcategory->id = (int)$id;
                if($subcategory->loadData()){
                    $this->View->Data->subcategory = $subcategory;
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
    }