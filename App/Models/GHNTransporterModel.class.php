<?php
    namespace App\Models;
    use Core\Model;
    
    class GHNTransporterModel extends Model{
        public $id, $order_id, $orderid, $ordercode, $currentstatus, $extrafee, $totalservicefee, $expecteddeliverytime, $note, $serviceid, $servicename, $insurancefee, $codamount, $fromdistrictid, $fromwardcode, $todistrictid, $towardcode, $created_time;
        
        #foreign key object
        public $order;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_GHNTRANSPORTER)->where('id=' . (int)$this->id)->execute();
            
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
    }