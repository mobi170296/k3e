<?php 
    namespace App\Controllers\api;
    use Core\Controller;
    
    use Library\Database\DBException;
    use Library\Database\Database;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    
    use App\Models\PaymentTypeModel;
    use App\Models\UserModel;
    use App\Models\OrderLogModel;
    
    use App\Models\OrderModel;
    
    class shopordersController extends Controller{
        public function waitorderslist($from = 1, $ipp = 10){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                if($user->loadShop()){
                    $shop = $user->shop;
                    
                    $start = ($from - 1) * $ipp;
                    
                    $rows = $database->select('id')->from(DB_TABLE_ORDER)->where('shop_id=' . (int)$shop->id . ' and order.status=' . (int) OrderModel::CHO_NGUOI_BAN_XAC_NHAN)->orderby('created_time')->limit($start, $ipp)->execute();
                    
                    $orders = [];
                    foreach($rows as $row){
                        $order = new OrderModel($database);
                        $order->id = $row->id;
                        $order->loadData();
                        $order->loadPaymentType();
                        $order->loadTransporter();
                        $order->loadOrderItems();
                        $order->statusstring = $order->getStatusString();
                        foreach($order->orderitems as $orderitem){
                            $orderitem->loadProduct();
                            $orderitem->product->loadMainImage();
                            $orderitem->product->mainimagethumbnail = $orderitem->product->getMainImageThumbnail();
                        }
                        
                        $orders[] = $order;
                    }
                    
                    $total = $shop->getWaitOrdersTotal();
                    
                    $result->body = new \stdClass();
                    $result->body->total = $total;
                    $result->body->data = $orders;
                }else{
                    
                }
                
                return $this->View->RenderJSON($result);
            } catch (DBException $ex) {
                
            } catch (AuthenticateException $e){
                
            }
        }
        
        public function cancelorder($order_id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!is_numeric($order_id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
                return $this->View->RenderJSON($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $order = new OrderModel($database);
                $order->id = $order_id;
                
                if($user->loadShop() && $order->loadData()){
                    $database->startTransaction();
                    
                    if($order->shopCanCancel()){
                        $order->updateStatus(OrderModel::KHONG_CON_HANG);
                        $orderlog = new OrderLogModel($database);
                        $orderlog->order_id = $order->id;
                        $orderlog->content = $order->getStatusString();
                        $orderlog->order_status = $order->status;

                        $orderlog->add();
                        
                        
                        if($order->paymenttype_id == PaymentTypeModel::ONEPAY && $order->paid == OrderModel::PAID){
                            $buyer = new UserModel($database);
                            $buyer->id = $order->client_id;

                            $buyer->loadData();
                            
                            $buyer->addMoney($order->total_price + $order->ship_fee);
                            $database->commit();
                        }else{
                            $database->commit();
                        }
                        
                        $result->header->code = 0;
                        $result->header->message = 'Đã hủy thành công đơn hàng ' . $order->ordercode;
                    }else{
                        $result->header->code = 1;
                        $result->header->message = 'Đơn hàng không thể hủy';
                    }
                }else{
                    $result->header->code = 1;
                    $result->header->message = 'invalid';
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
    }