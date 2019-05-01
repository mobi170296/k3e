<?php
    require '../utest.php';

    use Library\ThanhToan\OnePay\OnePay;
    use Library\ThanhToan\OnePay\OnePayException;
    use Library\Database\Database;
    
    try{
        $op = new OnePay;
        $response = $op->getPaymentResponse($_GET);
        
        $ordercode = $response->OrderInfo;
        
        $database = new Database();
        
        $orders = $database->selectall()->from('payorder')->where('code =\'' . $ordercode . '\'' )->execute();
        
        if(count($orders)){
            $order = $orders[0];
            
            if($order->paycomplete == 0){
                if($response->TxnResponseCode == 0){
                    
                    $database->update('payorder', [
                        'paycomplete' => new \Library\Database\DBNumber(1),
                        'paid' => new \Library\Database\DBNumber(1)
                    ], 'code=\'' . $ordercode . '\'');
                    
                    $database->update('onepayinfo', [
                        'message' => new Library\Database\DBString($response->Message)
                    ], 'order_id=' . $order->id);
                }elseif($response->TxnResponseCode != 100){
                    
                    $database->update('payorder', [
                        'paycomplete' => new \Library\Database\DBNumber(1),
                        'paid' => new \Library\Database\DBNumber(0)
                    ], 'code=\'' . $ordercode . '\'');
                    
                    $database->update('onepayinfo', [
                        'message' => new Library\Database\DBString($response->Message)
                    ], 'order_id=' . $order->id);
                }
                
                header('location: /pay/checkoutresult.php?ordercode=' . $ordercode);
            }else{
                echo 'Don hang nay da duoc cap nhat thong tin roi!';
            }
        }else{
            echo 'DON HANG NAY KHONG TON TAI';
        }
    }catch(OnePayException $e){
        echo 'loi: ' . $e->getMessage();
    }