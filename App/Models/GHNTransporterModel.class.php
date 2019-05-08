<?php
    namespace App\Models;
    use Core\Model;
    
    use Library\Database\DBNumber;
    use Library\Database\DBString;
    use Library\Database\DBDateTime;
    
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
        
        public function loadFromOrderId(){
            #order_id
            
            $rows = $this->database->selectall()->from(DB_TABLE_GHNTRANSPORTER)->where('order_id=' . (int)$this->id)->execute();
            
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
            $this->database->insert(DB_TABLE_GHNTRANSPORTER, [
                'order_id' => new DBNumber($this->order_id),
                'orderid' => new DBNumber($this->orderid),
                'ordercode' => new DBString($this->ordercode),
                'currentstatus' => new DBString($this->currentstatus),
                'extrafee' => new DBString($this->extrafee),
                'totalservicefee' => new DBString($this->totalservicefee),
                'expecteddeliverytime' => DBDateTime::parseGHNDateTime($this->expecteddeliverytime),
                'note' => new DBString($this->note),
                'serviceid' => new DBNumber($this->serviceid),
                'servicename' => new DBString($this->servicename),
                'insurancefee' => new DBNumber($this->insurancefee),
                'codamount' => new DBNumber($this->codamount),
                'fromdistrictid' => new DBNumber($this->fromdistrictid),
                'fromwardcode' => new DBString($this->fromwardcode),
                'todistrictid' => new DBNumber($this->todistrictid),
                'towardcode' => new DBString($this->towardcode)
            ]);
            return true;
        }
        
        
        //cap nhat trang thai don hang tu CALLBACK cua ghn
        public function updateCurrentStatus(){
            
        }
    }