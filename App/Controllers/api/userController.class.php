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
    
    use App\Models\AssessmentModel;
    
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
                return $this->View->RenderJSON($result);
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
                
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            }
            
            return $this->View->RenderJSON($result);
        }
        
        public function assessmentorder($order_id, $product_id, $starpoint, $comment){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            try{
                if(!is_numeric($order_id) || !is_array($product_id) || !is_array($starpoint) || !is_array($comment) || count($product_id) != count($starpoint) || count($starpoint) != count($comment)){
                    $result->header->code = 1;
                    $result->header->message = 'Yêu cầu không hợp lệ';
                    
                    return $this->View->RenderJSON($result);
                }
                $database = new Database();
                
                $user = (new Authenticate($database))->getUser();
                
                $order = new OrderModel($database);
                $order->id = $order_id;
                
                if($order->loadData() && $order->client_id == $user->id){
                    if($order->clientCanAssessment()){
                        $order->loadOrderItems();
                        
                        $order_product_id = [];
                        foreach($order->orderitems as $orderitem){
                            $order_product_id[] = $orderitem->product_id;
                        }
                        
                        if(count(array_intersect($product_id, $order_product_id)) == count($product_id)){
                            #khoi tao mang model danh gia + check du lieu truoc khi them vao db
                            $assessments = [];
                            
                            $length = count($product_id);
                            $errors = [];
                            for($i=0; $i<$length; $i++){
                                $assessment = new AssessmentModel($database);
                                $assessment->order_id = $order_id;
                                $assessment->client_id = $user->id;
                                $assessment->comment = $comment[$i];
                                $assessment->product_id = $product_id[$i];
                                $assessment->starpoint = $starpoint[$i];
                                $assessment->checkComment()->checkStarPoint();
                                
                                if(!$assessment->isValid()){
                                    $errors[] = 'Đánh giá số ' . ($i + 1) . ' không hợp lệ!';
                                }
                                $assessments[] = $assessment;
                            }
                            
                            if(count($errors)){
                                throw new InputException($errors, 'Thông tin đánh giá không hợp lệ');
                            }
                            
                            $database->startTransaction();
                            foreach($assessments as $assessment){
                                $assessment->add();
                            }
                            
                            $database->commit();
                            $result->header->code = 0;
                            $result->header->message = 'Đánh giá đơn hàng thành công cảm ơn bạn!';
                        }else{
                            $result->header->code = 1;
                            $result->header->message = 'Sản phẩm muốn đánh giá không tồn tại trong đơn hàng';
                        }
                    }else{
                        $result->header->code = 1;
                        $result->header->message = 'Đơn hàng này không có thể đánh giá được';
                    }
                }else{
                    $result->header->code = 1;
                    $result->header->message = 'Đơn hàng không tồn tại';
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
            } catch (AuthenticateException $e){
                $result->header->code = 1;
                $result->header->message = 'Invalid user';
            } catch (InputException $e){
                $result->header->code = 1;
                $result->header->message = $e->getMessage();
                $result->header->errors = $e->getErrorsMap();
            }
            
            return $this->View->RenderJSON($result);
        }
    }