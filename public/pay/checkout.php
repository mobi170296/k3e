<?php
    require '../utest.php';
    
    use Library\Database\Database;
    use Library\Common\Generator;
    use Library\Database\DBNumber;
    use Library\Database\DBString;
    use Library\ThanhToan\OnePay\OnePay;
    use Library\ThanhToan\OnePay\OnePayException;
    use Library\ThanhToan\OnePay\PaymentRequestParameter;
    
    $database = new Database;
    
    $paymethods = $database->selectall()->from('paymethod')->execute();
    
    
    ?>

<form action="" method="post">
    <div>
        <input type="text" name="clientname" placeholder="ten khach hang"/>
    </div>
    <div>
        <input type="text" name="clientphone" placeholder="so dien thoai khach hang"/>
    </div>
    <div>
        <input type="text" name="clientemail" placeholder="email khach hang"/>
    </div>
    <div>
        <input type="text" name="amount" placeholder="so tien thanh toan"/>
    </div>
    <div>
        <?php
            $default = true;
            foreach ($paymethods as $paymethod){
                echo '<input type="radio" name="paymethod_id" value="'.$paymethod->id.'" ' .($default ? 'checked' : ''). '/>' . $paymethod->name;
                $default = false;
            }
        ?>
    </div>
    <div>
        <textarea name="info" placeholder="chi tiet don hang">
            
        </textarea>
    </div>
    <button type="submit" name="checkout" value="">Checkout</button>
</form>


<?php
    if(isset($_POST['checkout'])){
        #xet 2 truong hop 
        #1: CoD -> chuyen ngay qua trang orderinfo
        #2: Online pay -> chuyen qua trang onepay -> checkoutresult -> orderinfo
        
        
        $try = 0;

        do{
            $code = \Library\Common\Generator::orderCode();
            $try++;
        }while(count($database->selectall()->from('payorder')->where('code=\''.$code .'\'')->execute()) > 0 && $try <= 3);

        
        if($_POST['paymethod_id'] == 1){
            #CoD
            
            
            $paymethod_id = $_POST['paymethod_id'];
            $clientname = $_POST['clientname'];
            $clientphone = $_POST['clientphone'];
            $clientemail = $_POST['clientname'];
            $clientip = $_SERVER['REMOTE_ADDR'];
            $amount = $_POST['amount'];
            $paycomplete = 1;
            $paid = 0;
            $info = $_POST['info'];
            
            $database->insert('payorder', [
                'code' => new DBString($code),
                'paymethod_id' => new DBNumber($paymethod_id),
                'clientname' => new DBString($clientname),
                'clientphone' => new DBString($clientphone),
                'clientemail' => new DBString($clientemail),
                'clientip' => new DBString($clientip),
                'amount' => new DBNumber($amount),
                'paid' => new DBNumber($paid),
                'info' => new DBString($info),
                'paycomplete' => new DBNumber($paycomplete)
            ]);
            
            
            
            header('location: /pay/checkoutresult.php?ordercode='. $code);
        }else{
            #OnlinePay
            ### QUY TRINH DAU TIEN
            # TAO DON HANG TREN HE THONG
            $paymethod_id = $_POST['paymethod_id'];
            $clientname = $_POST['clientname'];
            $clientphone = $_POST['clientphone'];
            $clientemail = $_POST['clientname'];
            $clientip = $_SERVER['REMOTE_ADDR'];
            $amount = $_POST['amount'];
            $paycomplete = 0;
            $paid = 0;
            $info = $_POST['info'];
            $title = 'Thanh Toan Cho Don Hang ' . $code ;
            $againlink = $_SERVER['HTTP_REFERER'];
            $returnurl = 'http://localhost/pay/ipnemulator.php';
            
            $database->insert('payorder', [
                'code' => new DBString($code),
                'paymethod_id' => new DBNumber($paymethod_id),
                'clientname' => new DBString($clientname),
                'clientphone' => new DBString($clientphone),
                'clientemail' => new DBString($clientemail),
                'clientip' => new DBString($clientip),
                'amount' => new DBNumber($amount),
                'paid' => new DBNumber($paid),
                'info' => new DBString($info),
                'paycomplete' => new DBNumber($paycomplete)
            ]);
            
            $orderid = $database->lastInsertId();
            $merchtxnref = Generator::transactionReference();
            
            $database->insert('onepayinfo', [
                'order_id' => new DBNumber($orderid),
                'version' => new DBNumber(OnePay::VERSION),
                'currency' => new DBString(OnePay::CURRENCYCODE),
                'command' => new DBString('pay'),
                'accesscode' => new DBString(OnePay::ACCESSCODE),
                'merchant' => new DBString(OnePay::MERCHANT),
                'locale' => new DBString(OnePay::LOCALE),
                'returnurl' => new DBString(''),
                'merchtxnref' => new DBString($merchtxnref),
                'orderinfo' => new DBString($code),
                'amount' => new DBString(round($amount*100)),
                'ticketno' => new DBString($clientip),
                'title' => new DBString($title),
                'againlink' => new DBString($againlink),
                'message' => new DBString('')
            ]);
            
            # TAO YEU CAU THANH TOAN BEN THU 3
            
            $onepay = new OnePay();
            
            $url = $onepay->getPaymentURL(new PaymentRequestParameter($code, $merchtxnref, round($amount*100), $returnurl, $clientip, $againlink, $title));
            header('location: ' . $url);
        }
    }