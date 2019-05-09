<?php
    require 'D:/k3e/public/utest.php';
    
    use Library\Database\Database;
    use App\Models\OrderModel;
    use Library\ThanhToan\OnePay\OnePay;
    use Library\ThanhToan\OnePay\QueryParameter;
    use App\Models\PaymentTypeModel;
    use App\Models\OrderLogModel;
    
    $database = new Database();


    $rows = $database->query('select id from `order` where order.paymenttype_id = ' . PaymentTypeModel::ONEPAY . ' and order.paycomplete=' . OrderModel::PAYINCOMPLETE . ' and timestampdiff(second, order.created_time, now()) > 180');
    
    
    $onepay = new OnePay();
    
    foreach($rows as $row){
        $order = new OrderModel($database);
        $order->id = $row->id;
        $order->loadData();
        $order->loadOnePayOrder();
        
        $result = $onepay->queryTransaction(new QueryParameter($order->onepayorder->transactionref));
        
        $database->startTransaction();
        
        if($result->TxnResponseCode == 0){
            $order->updatePayStatus(OrderModel::PAID, OrderModel::PAYCOMPLETE);
            $order->updateStatus(OrderModel::CHO_NGUOI_BAN_XAC_NHAN);
            $order->onepayorder->transactionno = $result->TransactionNo;
            $order->onepayorder->transactioncode = $result->TxnResponseCode;
            $order->onepayorder->transactionmessage = $result->Message;
            $order->onepayorder->additiondata = $result->AdditionData;
            $order->onepayorder->update($order->onepayorder);
            
            $orderlog = new OrderLogModel($database);
            $orderlog->order_id = $order->id;
            $orderlog->order_status = $order->status;
            $orderlog->content = $order->getStatusString();
            $orderlog->add();
            
            $database->commit();
        }else{
            $order->updatePayStatus(OrderModel::UNPAID, OrderModel::PAYCOMPLETE);
            $order->updateStatus(OrderModel::NGUOI_MUA_THANH_TOAN_THAT_BAI);
            $order->onepayorder->transactionno = $result->TransactionNo;
            $order->onepayorder->transactioncode = $result->TxnResponseCode;
            $order->onepayorder->transactionmessage = $result->Message;
            $order->onepayorder->additiondata = $result->AdditionData;
            $order->onepayorder->update($order->onepayorder);
            
            $orderlog = new OrderLogModel($database);
            $orderlog->order_id = $order->id;
            $orderlog->order_status = $order->status;
            $orderlog->content = $order->getStatusString();
            $orderlog->add();
            
            $database->commit();
        }
    }