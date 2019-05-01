<?php
    require '../utest.php';
    
    use Library\Database\Database;
    use Library\ThanhToan\OnePay\OnePay;
    use Library\ThanhToan\OnePay\QueryParameter;
    
    $database = new Database;
    
    
    try{
        if(isset($_GET['ordercode']) && is_string($_GET['ordercode'])){
            $orders = $database->selectall()->from('payorder')->where("code='" . $_GET['ordercode']. "'")->execute();
            if(count($orders)){
                $order = $orders[0];
                if($order->paycomplete == 0){
                    #tinh trang don hang chua hoan thanh thanh toan can cap nhat bang api query
                    #lay ma tham chieu cua thanh toan de thao tac truy van
                    
                    $onepayinfos = $database->selectall()->from('onepayinfo')->where('orderinfo=\'' . $order->code .'\'')->execute();
                    
                    if(count($onepayinfos)){
                        $onepayinfo = $onepayinfos[0];
                        
                        $op = new OnePay;
                        $transaction = $op->queryTransaction(new QueryParameter($onepayinfo->merchtxnref));
                        
                        if($transaction->Exists){
                            if($transaction->TxnResponseCode != 0 && $transaction->TxnResponseCode != 100){
                                $database->update('payorder', [
                                    'paid' => new \Library\Database\DBNumber(0),
                                    'paycomplete' => new \Library\Database\DBNumber(1)
                                ], 'id=' . $order->id);
                                
                                $database->update('onepayinfo', [
                                    'message' => new Library\Database\DBString($transaction->Message)
                                ], 'order_id=' . $order->id);
                                
                                
                        
                                #lay tinh trang order moi
                                $order = $database->selectall()->from('payorder')->where("code='" . $_GET['ordercode']. "'")->execute()[0];
                            }elseif($transaction->TxnResponseCode == 0){
                                $database->update('payorder', [
                                    'paid' => new \Library\Database\DBNumber(1),
                                    'paycomplete' => new \Library\Database\DBNumber(1)
                                ], 'id=' . $order->id);
                                
                                $database->update('onepayinfo', [
                                    'message' => new Library\Database\DBString($transaction->Message)
                                ], 'order_id=' . $order->id);
                                
                                #lay tinh trang order moi
                                $order = $database->selectall()->from('payorder')->where("code='" . $_GET['ordercode']. "'")->execute()[0];
                            }
                            #can kiem tra them timeout cua giao dich va ma 100
                        }
                    }else{
                        echo 'khong tim thay onepay info => chua cap nhat duoc tinh trang that su';
                    }
                    
                }
                
                $paymethod = $database->selectall()->from('paymethod')->where('id=' . $order->paymethod_id)->execute()[0];
                
                $paidstring = $order->paid ? '<div style="color:#0f0">Đã thanh toán</div>' : '<div style="color:red">Chưa thanh toán</div>';
                
                $paycomplete = $order->paycomplete ? '<div style="color: #0f0">Đã hoàn tất quá trình thanh toán</div>' : '<div style="color: #f00">Chưa hoàn tất quá trình thanh toán</div>';
                
                $amount = number_format($order->amount, 2);
                
                echo <<<INFO
                <style>
                    table{
                        width: 100%;
                        border-collapse: collapse;
                    }
                    table td{
                        border: 2px solid #eee;
                    }
                </style>
                <h3>Chi tiết đơn hàng</h3>
                <table>
                    <tr>
                        <td>Mã đơn hàng</td><td>{$order->code}</td>
                    </tr>
                    <tr>
                        <td>Tên khách hàng</td><td>{$order->clientname}</td>
                    </tr>
                    <tr>
                        <td>Số điện thoại khách hàng</td><td>{$order->clientphone}</td>
                    </tr>
                    <tr>
                        <td>Địa chỉ email khách hàng</td><td>{$order->clientemail}</td>
                    </tr>
                    <tr>
                        <td>Chi tiết đơn hàng</td><td>{$order->info}</td>
                    </td>
                    <tr>
                        <td>Phương thức thanh toán</td><td>{$paymethod->name}</td>
                    </tr>
                    <tr>
                        <td>Tình trạng thanh toán</td><td>{$paycomplete}</td>
                    </tr>
                    <tr>
                        <td>Tình trạng thanh toán</td><td>{$paidstring}</td>
                    </tr>
                    <tr>
                        <td>Tổng số thanh toán</td><td>{$amount}đ</td>
                    </tr>
                </table>
INFO;
            }else{
                throw new Exception('Đơn hàng ' . $_GET['ordercode'] . ' không tồn tại!');
            }
        }else{
            throw new Exception('Đơn hàng này không tồn tại!');
        }
    } catch (Exception $ex) {
        echo '<span style="color: red; font-size: 2em; background-color: yellow">' . $ex->getMessage() . '</span>';
    }