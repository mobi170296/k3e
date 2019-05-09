<?php
    namespace App\Models;
    use Core\Model;
    
    use Library\Database\DBNumber;
    use Library\Database\DBString;
    use Library\Database\DBRaw;
    
    class OnePayOrderModel extends Model{
        public $id, $order_id, $orderinfo, $transactionref, $currencycode, $amount, $transactionno, $transactioncode, $transactionmessage, $additiondata, $merchant, $accesscode, $ticketno;
        
        
        #object
        public $order;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_ONEPAYORDER)->where('id=' . (int)$this->id)->execute();
            
            if(count($rows)){
                $row = $rows[0];
                
                foreach($row as $key => $value){
                    $this->$key = $value;
                }
                return true;
            }else{
                return false;
            }
        }
        
        public function loadFromTransactionRef(){
            $rows = $this->database->selectall()->from(DB_TABLE_ONEPAYORDER)->where('transactionref=' . (new DBString($this->transactionref))->SqlValue())->execute();
            if(count($rows)){
                $row = $rows[0];
                foreach($row as $k => $v){
                    $this->$k = $v;
                }
                return true;
            }else{
                return false;
            }
        }
        
        public function loadFromOrderId(){
            $rows = $this->database->selectall()->from(DB_TABLE_ONEPAYORDER)->where('order_id=' . (int)$this->order_id)->execute();
            
            if(count($rows)){
                $row = $rows[0];
                
                foreach($row as $key => $value){
                    $this->$key = $value;
                }
                return true;
            }else{
                return false;
            }
        }
        
        public function loadOrder(){
            $this->order = new OrderModel($this->database);
            $this->order->id = $this->order_id;
            
            return $this->order->loadData();
        }
        
        public function add(){
            $order_id = $this->order_id === null ? new DBRaw('null') : new DBNumber($this->order_id);
            $orderinfo = $this->orderinfo === null ? new DBRaw('null') : new DBString($this->orderinfo);
            $transactionref = $this->transactionref === null ? new DBRaw('null') : new DBString($this->transactionref);
            $currencycode = $this->currencycode === null ? new DBRaw('null') : new DBString($this->currencycode);
            $amount = $this->amount === null ? new DBRaw('null') : new DBString($this->amount);
            $transactionno = $this->transactionno === null ? new DBRaw('null') : new DBString($this->transactionno);
            $transactioncode = $this->transactioncode === null ? new DBRaw('null') : new DBString($this->transactioncode);
            $transactionmessage = $this->transactionmessage === null ? new DBRaw('null') : new DBString($this->transactionmessage);
            $additiondata = $this->additiondata === null ? new DBRaw('null') : new DBString($this->additiondata);
            $merchant = $this->merchant === null ? new DBRaw('null') : new DBString($this->merchant);
            $accesscode = $this->accesscode === null ? new DBRaw('null') : new DBString($this->accesscode);
            $ticketno = $this->ticketno === null ? new DBRaw('null') : new DBString($this->ticketno);
            
            $this->database->insert(DB_TABLE_ONEPAYORDER, [
                'order_id' => $order_id,
                'orderinfo' => $orderinfo,
                'transactionref' => $transactionref,
                'currencycode' => $currencycode,
                'amount' => $amount,
                'transactionno' => $transactionno,
                'transactioncode' => $transactioncode,
                'transactionmessage' => $transactionmessage,
                'additiondata' => $additiondata,
                'merchant' => $merchant,
                'accesscode' => $accesscode,
                'ticketno' => $ticketno
            ]);
            
            return true;
        }
        
        public function update($onepayorder){
            $order_id = $onepayorder->order_id === null ? new DBRaw('null') : new DBNumber($onepayorder->order_id);
            $orderinfo = $onepayorder->orderinfo === null ? new DBRaw('null') : new DBString($onepayorder->orderinfo);
            $transactionref = $onepayorder->transactionref === null ? new DBRaw('null') : new DBString($onepayorder->transactionref);
            $currencycode = $onepayorder->currencycode === null ? new DBRaw('null') : new DBString($onepayorder->currencycode);
            $amount = $onepayorder->amount === null ? new DBRaw('null') : new DBString($onepayorder->amount);
            $transactionno = $onepayorder->transactionno === null ? new DBRaw('null') : new DBString($onepayorder->transactionno);
            $transactioncode = $onepayorder->transactioncode === null ? new DBRaw('null') : new DBString($onepayorder->transactioncode);
            $transactionmessage = $onepayorder->transactionmessage === null ? new DBRaw('null') : new DBString($onepayorder->transactionmessage);
            $additiondata = $onepayorder->additiondata === null ? new DBRaw('null') : new DBString($onepayorder->additiondata);
            $merchant = $onepayorder->merchant === null ? new DBRaw('null') : new DBString($onepayorder->merchant);
            $accesscode = $onepayorder->accesscode === null ? new DBRaw('null') : new DBString($onepayorder->accesscode);
            $ticketno = $onepayorder->ticketno === null ? new DBRaw('null') : new DBString($onepayorder->ticketno);
            
            $this->database->update(DB_TABLE_ONEPAYORDER, [
                'order_id' => $order_id,
                'orderinfo' => $orderinfo,
                'transactionref' => $transactionref,
                'currencycode' => $currencycode,
                'amount' => $amount,
                'transactionno' => $transactionno,
                'transactioncode' => $transactioncode,
                'transactionmessage' => $transactionmessage,
                'additiondata' => $additiondata,
                'merchant' => $merchant,
                'accesscode' => $accesscode,
                'ticketno' => $ticketno
            ], 'id=' . (int)$this->id);
            
            return true;
        }
    }