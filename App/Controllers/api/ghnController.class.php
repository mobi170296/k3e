<?php
    namespace App\Controllers\api;
    
    use Core\Controller;
    
    use Library\Database\Database;
    use App\Models\GHNTransporterModel;
    use App\Models\OrderModel;
    use App\Models\OrderLogModel;
    
    class ghnController extends Controller{
        public function ghnupdate($OrderCode, $CurrentStatus){
            #kiem tra chuoi xac thuc
            #moi truong kiem thu bo qua viec nay
            $statusarray = ['ReadyToPick', 'Picking', 'Storing', 'Delivering', 'Delivered', 'Return', 'Returned', 'WaitingToFinish', 'Finish', 'Cancel', 'LostOrder'];
            
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if($OrderCode !== null && $CurrentStatus !== null && is_string($CurrentStatus) && in_array($CurrentStatus, $statusarray)){        
                $database = new Database();
                $ghntransporter = new GHNTransporterModel($database);
                $ghntransporter->ordercode = $OrderCode;
                
                if($ghntransporter->loadFromOrderCode()){
                    #xet trang thai don hang
                    $ghntransporter->loadOrder();
                    $order = $ghntransporter->order;
                    
                    //bat dau transaction
                    $database->startTransaction();
                    
                    
                    if($CurrentStatus === 'Storing' && $order->status == OrderModel::CHO_LAY_HANG){
                        #nha van chuyen thong bao da lay hang -> cap nhat lai trang thai
                        
                        
                        $order->updateStatus(OrderModel::DANG_GIAO);
                        
                        $orderlog = new OrderLogModel($database);
                        
                        $orderlog->order_id = $order->id;
                        $orderlog->content = $order->getStatusString();
                        $orderlog->order_status = $order->status;
                        
                        $orderlog->add();
                        
                        $ghntransporter->currentstatus = $CurrentStatus;
                        $ghntransporter->update($ghntransporter);
                        
                        $database->commit();
                        
                        $result->header->code = 0;
                        $result->header->message = 'Đã cập nhật thành công đơn hàng sang trạng thái đang giao';
                        return $this->View->RenderJSON($result);
                    }
                    
                    
                    if($CurrentStatus === 'Cancel' && $order->status == OrderModel::CHO_LAY_HANG){
                        #nha van chuyen thong bao lay hang khong thanh cong -> hoan tien lai neu nguoi mua chon thanh toan truc tuyen
                        //cap nhat lai so san pham kho khong lay duoc hang
                        $order->loadOrderItems();
                        foreach($order->orderitems as $orderitem){
                            $orderitem->loadProduct();
                            $orderitem->product->increaseQuantity($orderitem->quantity);
                        }
                        
                        $order->updateStatus(OrderModel::HUY_DO_KHONG_LAY_DUOC_HANG);
                        
                        $orderlog = new OrderLogModel($database);
                        
                        $orderlog->order_id = $order->id;
                        $orderlog->content = $order->getStatusString();
                        $orderlog->order_status = $order->status;
                        $orderlog->add();
                        
                        $ghntransporter->currentstatus = $CurrentStatus;
                        $ghntransporter->update($ghntransporter);
                        
                        
                        
                        if($order->paid == OrderModel::PAID){
                            #hoan tien lai cho nguoi mua neu ho da thanh toan
                            $order->loadClient();

                            //lock buyer lai
                            $database->selectall()->from(DB_TABLE_USER)->where('id=' . $order->client_id)->lock();

                            $buyer = $order->client;

                            $buyer->addMoney($order->total_price + $order->ship_fee);
                        }
                        
                        $database->commit();
                        
                        $result->header->code = 0;
                        $result->header->message = 'Đã cập nhật thành công đơn hàng sang trạng thái đã hủy do không lấy được hàng';
                        return $this->View->RenderJSON($result);
                    }
                    
                    
                    if($CurrentStatus === 'Returned' && $order->status == OrderModel::DANG_GIAO){
                        #Giao that bai don hang da tra ve cho nguoi ban
                        
                        $order->updateStatus(OrderModel::GIAO_THAT_BAI);
                        
                        $orderlog = new OrderLogModel($database);
                        
                        $orderlog->order_id = $order->id;
                        $orderlog->content = $order->getStatusString();
                        $orderlog->order_status = $order->status;
                        $orderlog->add();
                        
                        $ghntransporter->currentstatus = $CurrentStatus;
                        $ghntransporter->update($ghntransporter);
                        
                        if($order->paid == OrderModel::PAID){
                            #hoan tien lai cho nguoi mua neu ho da thanh toan
                            $order->loadClient();

                            //lock buyer lai
                            $database->selectall()->from(DB_TABLE_USER)->where('id=' . $order->client_id)->lock();

                            $buyer = $order->client;

                            $buyer->addMoney($order->total_price + $order->ship_fee);
                        }
                        
                        $database->commit();
                        
                        $result->header->code = 0;
                        $result->header->message = 'Đã cập nhật thành công đơn hàng sang trạng thái đã hủy do người mua không nhận (giao không thành công)';
                        return $this->View->RenderJSON($result);
                    }
                    
                    if($CurrentStatus === 'Delivered' && $order->status == OrderModel::DANG_GIAO){
                        #Da giao thanh cong
                        #cap nhat sang DA_GIAO
                        
                        
                        $order->updateStatus(OrderModel::DA_GIAO);
                        
                        $order->updatePayStatus(OrderModel::PAID, OrderModel::PAYCOMPLETE);
                        
                        $orderlog = new OrderLogModel($database);
                        
                        $orderlog->order_id = $order->id;
                        $orderlog->content = $order->getStatusString();
                        $orderlog->order_status = $order->status;
                        $orderlog->add();
                        
                        $ghntransporter->currentstatus = $CurrentStatus;
                        $ghntransporter->update($ghntransporter);
                        
                        #cap nhat sang HOAN_TAT
                        $order->updateStatus(OrderModel::HOAN_TAT);
                        
                        $orderlog = new OrderLogModel($database);
                        
                        $orderlog->order_id = $order->id;
                        $orderlog->content = $order->getStatusString();
                        $orderlog->order_status = $order->status;
                        $orderlog->add();
                        
                        $ghntransporter->currentstatus = $CurrentStatus;
                        $ghntransporter->update($ghntransporter);
                        
                        
                        #thanh toan cho nguoi ban
                        $order->loadShop();
                        $order->shop->loadOwner();
                        
                        //lock shop lai
                        $database->selectall()->from(DB_TABLE_USER)->where('id=' . $order->shop->owner->id)->lock();
                        
                        $shopuser = $order->shop->owner;
                        
                        $shopuser->addMoney($order->total_price);
                        
                        $database->commit();
                        
                        $result->header->code = 0;
                        $result->header->message = 'Đã cập nhật đơn hàng sang trạng thái đã giao thành công';
                        return $this->View->RenderJSON($result);
                    }
                    $result->header->code = 0;
                    $result->header->message = 'Trạng thái cập nhật không hợp lệ';
                    return $this->View->RenderJSON($result);
                }else{
                    $result->header->code = 0;
                    $result->header->message = 'Không tồn tại order code';
                    return $this->View->RenderJSON($result);
                }
            }else{
                $result->header->code = 1;
                $result->header->message = 'Yêu cầu không hợp lệ!';
                
                
                return $this->View->RenderJSON($result);
            }

        }
    }
