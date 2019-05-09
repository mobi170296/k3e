<?php
    namespace App\Models;
    use Core\Model;
    
    use Library\Database\DBNumber;
    use Library\Database\DBString;
    use Library\Database\DBDateTime;
    use Library\Database\DBRaw;
    
    class GHNTransporterModel extends Model{
        const SHOP_PAY = 1, BUYER_PAY = 2, GHNWALLET_PAY = 4, CREDIT_PAY = 5;
        
        public $id, $order_id, $orderid, $ordercode, $currentstatus, $extrafee, $totalservicefee, $expecteddeliverytime, $note, $serviceid, $servicename, $insurancefee, $codamount, $fromdistrictid, $fromwardcode, $todistrictid, $towardcode, $paymenttypeid, $clienthubid, $sortcode, $created_time;
        
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
            
            $rows = $this->database->selectall()->from(DB_TABLE_GHNTRANSPORTER)->where('order_id=' . (int)$this->order_id)->execute();
            
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
            $order_id = new DBNumber($this->order_id);
            $orderid = $this->orderid === null ? new DBRaw('null') : new DBNumber($this->orderid);
            $ordercode = $this->ordercode === null ? new DBRaw('null') : new DBString($this->ordercode);
            $currentstatus = $this->currentstatus === null ? new DBRaw('null') : new DBString($this->currentstatus);
            $extrafee = $this->extrafee === null ? new DBRaw('null') : new DBNumber($this->extrafee);
            $totalservicefee = $this->totalservicefee === null ? new DBRaw('null') : new DBNumber($this->totalservicefee);
            $expecteddeliverytime = $this->expecteddeliverytime === null ? new DBRaw('null') : $this->expecteddeliverytime;
            $note = $this->note === null ? new DBRaw('null') : new DBString($this->note);
            $serviceid = $this->serviceid === null ? new DBRaw('null') : new DBNumber($this->serviceid);
            $servicename = $this->servicename === null ? new DBRaw('null') : new DBString($this->servicename);
            $insurancefee = $this->insurancefee === null ? new DBRaw('null') : new DBNumber($this->insurancefee);
            $codamount = $this->codamount === null ? new DBRaw('null') : new DBNumber($this->codamount);
            $fromdistrictid = $this->fromdistrictid === null ? new DBRaw('null') : new DBNumber($this->fromdistrictid);
            $fromwardcode = $this->fromwardcode === null ? new DBRaw('null') : new DBString($this->fromwardcode);
            $todistrictid = $this->todistrictid === null ? new DBRaw('null') : new DBNumber($this->todistrictid);
            $towardcode = $this->towardcode === null ? new DBRaw('null') : new DBString($this->towardcode);
            $clienthubid = $this->clienthubid === null ? new DBRaw('null') : new DBNumber($this->clienthubid);
            $sortcode = $this->sortcode === null ? new DBRaw('null') : new DBString($this->sortcode);
            $paymenttypeid = $this->paymenttypeid === null ? new DBRaw('null') : new DBNumber($this->paymenttypeid);
            
            $this->database->insert(DB_TABLE_GHNTRANSPORTER, [
                'order_id' => $order_id,
                'orderid' => $orderid,
                'ordercode' => $ordercode,
                'currentstatus' => $currentstatus,
                'extrafee' => $extrafee,
                'totalservicefee' => $totalservicefee,
                'expecteddeliverytime' => $expecteddeliverytime,
                'note' => $note,
                'serviceid' => $serviceid,
                'servicename' => $servicename,
                'insurancefee' => $insurancefee,
                'codamount' => $codamount,
                'fromdistrictid' => $fromdistrictid,
                'fromwardcode' => $fromwardcode,
                'todistrictid' => $todistrictid,
                'towardcode' => $towardcode,
                'clienthubid' => $clienthubid,
                'sortcode' => $sortcode,
                'paymenttypeid' => $paymenttypeid
            ]);
            return true;
        }
        
        
        //cap nhat trang thai don hang tu CALLBACK cua ghn
        public function updateCurrentStatus(){
            
        }
        
        public function update($ghntransporter){
            $order_id = new DBNumber($ghntransporter->order_id);
            $orderid = $ghntransporter->orderid === null ? new DBRaw('null') : new DBNumber($ghntransporter->orderid);
            $ordercode = $ghntransporter->ordercode === null ? new DBRaw('null') : new DBString($ghntransporter->ordercode);
            $currentstatus = $ghntransporter->currentstatus === null ? new DBRaw('null') : new DBString($ghntransporter->currentstatus);
            $extrafee = $ghntransporter->extrafee === null ? new DBRaw('null') : new DBNumber($ghntransporter->extrafee);
            $totalservicefee = $ghntransporter->totalservicefee === null ? new DBRaw('null') : new DBNumber($ghntransporter->totalservicefee);
            $expecteddeliverytime = $ghntransporter->expecteddeliverytime === null ? new DBRaw('null') : $ghntransporter->expecteddeliverytime;
            $note = $ghntransporter->note === null ? new DBRaw('null') : new DBString($ghntransporter->note);
            $serviceid = $ghntransporter->serviceid === null ? new DBRaw('null') : new DBNumber($ghntransporter->serviceid);
            $servicename = $ghntransporter->servicename === null ? new DBRaw('null') : new DBString($ghntransporter->servicename);
            $insurancefee = $ghntransporter->insurancefee === null ? new DBRaw('null') : new DBNumber($ghntransporter->insurancefee);
            $codamount = $ghntransporter->codamount === null ? new DBRaw('null') : new DBNumber($ghntransporter->codamount);
            $fromdistrictid = $ghntransporter->fromdistrictid === null ? new DBRaw('null') : new DBNumber($ghntransporter->fromdistrictid);
            $fromwardcode = $ghntransporter->fromwardcode === null ? new DBRaw('null') : new DBString($ghntransporter->fromwardcode);
            $todistrictid = $ghntransporter->todistrictid === null ? new DBRaw('null') : new DBNumber($ghntransporter->todistrictid);
            $towardcode = $ghntransporter->towardcode === null ? new DBRaw('null') : new DBString($ghntransporter->towardcode);
            $clienthubid = $this->clienthubid === null ? new DBRaw('null') : new DBNumber($this->clienthubid);
            $sortcode = $this->sortcode === null ? new DBRaw('null') : new DBString($this->sortcode);
            $paymenttypeid = $this->paymenttypeid === null ? new DBRaw('null') : new DBNumber($this->paymenttypeid);
            
            $this->database->update(DB_TABLE_GHNTRANSPORTER, [
                'order_id' => $order_id,
                'orderid' => $orderid,
                'ordercode' => $ordercode,
                'currentstatus' => $currentstatus,
                'extrafee' => $extrafee,
                'totalservicefee' => $totalservicefee,
                'expecteddeliverytime' => $expecteddeliverytime,
                'note' => $note,
                'serviceid' => $serviceid,
                'servicename' => $servicename,
                'insurancefee' => $insurancefee,
                'codamount' => $codamount,
                'fromdistrictid' => $fromdistrictid,
                'fromwardcode' => $fromwardcode,
                'todistrictid' => $todistrictid,
                'towardcode' => $towardcode,
                'clienthubid' => $clienthubid,
                'sortcode' => $sortcode,
                'paymenttypeid' => $paymenttypeid
            ], 'id=' . (int)$this->id);
            
            return true;
        }
    }