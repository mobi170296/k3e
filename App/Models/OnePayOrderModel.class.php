<?php
    namespace App\Models;
    use Core\Model;
    
    use Library\Database\DBNumber;
    use Library\Database\DBString;
    
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
        
        public function loadOrder(){
            $this->order = new OrderModel($this->database);
            $this->order->id = $this->order_id;
            
            return $this->order->loadData();
        }
        
        public function add(){
            $this->database->insert(DB_TABLE_ONEPAYORDER, [
                'order_id' => new DBNumber($this->order_id),
                'orderinfo' => new DBString($this->orderinfo),
                'transactionref' => new DBString($this->transactionref),
                'currencycode' => new DBString($this->currencycode),
                'amount' => new DBString($this->amount),
                'transactionno' => new DBString($this->transactionno),
                'transactioncode' => new DBString($this->transactioncode),
                'transactionmessage' => new DBString($this->transactionmessage),
                'additiondata' => new DBString($this->additiondata),
                'merchant' => new DBString($this->merchant),
                'accesscode' => new DBString($this->accesscode),
                'ticketno' => new DBString($this->ticketno)
            ]);
            
            return true;
        }
    }