<?php
    require '../utest.php';
    
    #9704250000000001
    
    #trang thong bao ket qua thanh toan
    
    use Library\Database\Database;
    
    try{
        
        $database=new Database;
        $ordercode = $_GET['ordercode'];
        
        $orders = $database->selectall()->from('payorder')->where('code=\'' . $ordercode. '\'')->execute();
        
        if(count($orders)){
            $order = $orders[0];
            
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
            throw new Library\Database\DBException('don hang khong ton tai');
        }
    } catch (Exception $ex) {
echo '<div style="color: red">' . $ex->getMessage() . '</div>';
    }