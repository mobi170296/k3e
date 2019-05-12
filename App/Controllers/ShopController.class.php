<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use Library\Database\DBException;
    use Library\Database\Database;
    use App\Models\MainCategoryList;
    use App\Models\SubCategoryList;
    
    use App\Models\OrderModel;
    
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
                    $shop = $user->shop;
                    $shop->loadAvatar();
                    $shop->loadBackground();
                    $this->View->Data->shop = $shop;
                    $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                    $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                    $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                    $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                    $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
                    
                    
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
                    $shop = $user->shop;
                    $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                    $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                    $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                    $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                    $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
                    
                    
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
                            $this->View->Data->hasSold = $product->hasSold();
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
                            
                            
                            
                            $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                            $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                            $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                            $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                            $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
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
                    $shop = $user->shop;
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
                    
                    
                    
                    $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                    $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                    $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                    $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                    $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
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
        #danh sach don hang doi xac nhan
        public function WaitOrders(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    
                    $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                    $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                    $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                    $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                    $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        #danh dach don hang cho lay hang
        public function ToshipOrders(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    
                    $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                    $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                    $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                    $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                    $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        #danh sach don hang dang giao 
        public function ShippingOrders(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    
                    $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                    $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                    $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                    $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                    $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        #danh sach don hang hoan thanh
        public function CompletedOrders(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    
                    $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                    $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                    $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                    $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                    $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        #danh sach don hang huy
        public function CancelledOrders(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    
                    $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                    $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                    $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                    $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                    $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        #thong tin don hang tu phia cua hang
        public function OrderDetail($ordercode){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $order = new OrderModel($database);
                    $order->ordercode = $ordercode;
                    if($order->loadFromOrderCode()){
                        $order->loadTransporter();
                        $order->loadTransporterUnit();
                        $order->loadPaymentType();
                        $order->loadOrderLogs();
                        $order->loadOrderItems();
                        foreach($order->orderitems as $orderitem){
                            $orderitem->loadProduct();
                            $orderitem->product->loadMainImage();
                        }
                        
                        $this->View->Data->order = $order;

                        $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                        $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                        $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                        $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                        $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
                        return $this->View->RenderTemplate();
                    }else{
                        return $this->redirectToAction('Info', 'Shop');
                    }
                    
                }else{
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        public function PrintInvoice($ordercode){
            $status = [OrderModel::CHO_LAY_HANG, OrderModel::DANG_GIAO, OrderModel::DA_GIAO, OrderModel::HOAN_TAT, OrderModel::GIAO_THAT_BAI];
            try{
                if(!is_string($ordercode)){
                    throw new \Exception('Thao tác không thành công!');
                }
                
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    $order = new OrderModel($database);
                    
                    $order->ordercode = $ordercode;
                    
                    if($order->loadFromOrderCode() && $order->shop_id == $shop->id){
                        if(in_array($order->status, $status)){
                            #load du lieu can thiet cho viec in hoa don
                            $order->loadOrderItems();
                            foreach($order->orderitems as $orderitem){
                                $orderitem->loadProduct();
                            }
                            $order->loadPaymentType();
                            $order->loadTransporter();
                            $order->loadTransporterUnit();
                            
                            $this->View->Data->order = $order;
                            
                            return $this->View->RenderPartial();
                        }else{
                            throw new \Exception('Đơn hàng này không thể in hóa đơn');
                        }
                    }else{
                        throw new \Exception('Đơn hàng không tồn tại');
                    }
                }else{
                    throw new \Exception('Bạn không có quyền thực hiện thao tác này');
                }
            } catch (DBException $ex) {
                return $this->View->RenderContent('Không thể in đơn hàng vui lòng thử lại');
            } catch (AuthenticateException $e){
                return $this->View->RenderContent('Bạn không có quyền thực hiện thao tác này');
            } catch (\Exception $e){
                return $this->View->RenderContent($e->getMessage());
            }
        }
        
        
        public function MonthStatistics(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                    $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                    $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                    $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                    $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        
        public function YearStatistics(){
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    
                    
                    $this->View->Data->waitorderstotal = $shop->getWaitOrdersTotal();
                    $this->View->Data->toshiporderstotal = $shop->getToshipOrdersTotal();
                    $this->View->Data->shippingorderstotal = $shop->getShippingOrdersTotal();
                    $this->View->Data->completedorderstotal = $shop->getCompletedOrdersTotal();
                    $this->View->Data->cancelledorderstotal = $shop->getCancelledOrdersTotal();
                    return $this->View->RenderTemplate();
                }else{
                    return $this->redirectToAction('Open', 'Shop');
                }
            } catch (DBException $ex) {
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            } catch (AuthenticateException $e){
                return $this->redirectToAction('Login', 'User');
            }
        }
        
        #thong tin shop voi khach hang
        public function View($id){
            return $this->View->RenderContent($id);
        }
    }