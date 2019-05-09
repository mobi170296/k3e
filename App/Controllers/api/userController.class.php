<?php
    namespace App\Controllers\api;
    use Core\Controller;
    use Library\Database\Database;
    use App\Models\Authenticate;
    use Library\Database\DBException;
    use App\Exception\AuthenticateException;
    use App\Exception\InputException;
    
    use App\Models\PaymentTypeModel;
    use App\Models\OrderModel;
    use App\Models\OrderLogModel;
    
    class userController extends Controller{
        public function changepassword($oldpassword, $password){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!$this->isPOST() || !is_array($password) || !is_string($password[0]) || !is_string($password[1]) || !is_string($oldpassword)){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
                return $this->View->RenderJson($result);
            }
            
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                if($oldpassword !== $_SESSION['password']){
                    throw new InputException(['Mật khẩu cũ không đúng']);
                }
                #thay doi mat khau, thay doi luon password
                $user->checkValidForPassword($password[0], $password[1]);
                if($user->isValid()){
                    $user->changePassword($password[0]);
                    $_SESSION['password'] = $password[0];
                    $result->header->code = 0;
                    $result->header->message = 'Bạn đã cập nhật thành công mật khẩu mới';
                }else{
                    throw new InputException($user->getErrorsMap());
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            } catch (InputException $e){
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsMap();
            }
            return $this->View->RenderJson($result);
        }
        
        public function cancelorder($order_id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            if(!is_numeric($order_id)){
                $result->header->code = 1;
                $result->header->message = 'invalid';
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $order = new OrderModel($database);
                $order->id = $order_id;
                
                if($order->loadData() && $order->client_id == $user->id){
                    if($order->clientCanCancel()){
                        $order->loadPaymentType();
                        $database->startTransaction();
                        //khoa lai dong user can cap nhat tien te
                        $database->selectall()->from(DB_TABLE_USER)->where('id=' . (int)$user->id)->lock();
                        $order->updateStatus(OrderModel::HUY_DON_HANG);
                        
                        $orderlog = new OrderLogModel($database);
                        $orderlog->order_id = $order->id;
                        $orderlog->content = $order->getStatusString();
                        $orderlog->order_status = $order->status;
                        $orderlog->add();
                        
                        if($order->paymenttype_id == PaymentTypeModel::ONEPAY){
                            $total = $order->total_price + $order->ship_fee;
                            $user->addMoney($total);
                            $database->commit();
                            $result->header->code = 0;
                            $result->header->message = 'Đã hủy đơn hàng ' . $order->ordercode . ' thành công';
                        }else{
                            $database->commit();
                            $result->header->code = 0;
                            $result->header->message = 'Đã hủy đơn hàng ' . $order->ordercode . ' thành công';
                        }
                        
                    }else{
                        $result->header->code = 1;
                        $result->header->message = 'Đơn hàng này không hủy được';
                    }
                }else{
                    $result->header->code = 1;
                    $result->header->message = 'don hang hong ton tai';
                }
                
                return $this->View->RenderJSON($result);
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            }
        }
    }