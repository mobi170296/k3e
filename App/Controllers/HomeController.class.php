<?php
    namespace App\Controllers;
    use Core\Controller;
    
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\OrderModel;
    
    use App\Models\ProductModel;
    
    class HomeController extends Controller{
        public function Index($p = 1){
            try{
                $database = new Database();
                try{
                    $user = (new Authenticate($database))->getUser();
                } catch (AuthenticateException $ex) {
                    $user = null;
                }
                
                #thong ke san pham ngau nghien
                $rows = $database->select('id')->from(DB_TABLE_PRODUCT)->orderby('rand()')->limit(0, 10)->execute();
                
                $cancareproducts = [];
                
                foreach($rows as $row){
                    $product = new ProductModel($database);
                    $product->id = $row->id;
                    $product->loadData();
                    $product->loadMainImage();
                    $product->loadWard();
                    $product->ward->loadDistrict();
                    $product->ward->district->loadProvince();
                    $cancareproducts[] = $product;
                }
                
                #thong ke san pham ban chay nhat
                $rows = $database->select('product.id id')->from(DB_TABLE_PRODUCT)->join(DB_TABLE_ORDERITEM)->on('product.id=orderitem.product_id')->join(DB_TABLE_ORDER)->on('order.id=orderitem.order_id')->where('order.status='.OrderModel::HOAN_TAT)->groupby('product.id')->orderby('count(*)')->desc()->limit(0,10)->execute();
                
                $bestsellingproducts = [];
                
                foreach($rows as $row){
                    $product = new ProductModel($database);
                    $product->id = $row->id;
                    $product->loadData();
                    $product->loadMainImage();
                    $product->loadWard();
                    $product->ward->loadDistrict();
                    $product->ward->district->loadProvince();
                    
                    $bestsellingproducts[] = $product;
                }
                
                $this->View->Data->cancareproducts = $cancareproducts;
                $this->View->Data->bestsellingproducts = $bestsellingproducts;
                
                $viewedproducts = [];
                if($user){
                    #san pham ma nguoi mua da xem
                    $productviewslogs = $user->getProductViewsLogs(0, 10);
                    foreach($productviewslogs as $productviewslog){
                        $productviewslog->loadProduct();
                        $productviewslog->product->loadMainImage();
                        $productviewslog->product->loadWard();
                        $productviewslog->product->ward->loadDistrict();
                        $productviewslog->product->ward->district->loadProvince();
                        
                        $viewedproducts[] = $productviewslog->product;
                    }
                }
                
                if($user && count($viewedproducts)){
                    $this->View->Data->viewedproducts = $viewedproducts;
                    return $this->View->RenderTemplate('UserIndex');
                }else{
                    return $this->View->RenderTemplate();
                }
                
            } catch (DBException $ex) {
                $this->View->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            }
        }
        public function About(){
            $this->View->ViewData['title'] = 'About';
            return $this->View->RenderTemplate();
        }
        public function Info(){
            return $this->View->RenderContent("NOI DUNG OF INFO");
        }
    }