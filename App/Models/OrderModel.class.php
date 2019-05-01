<?php
    namespace App\Models;
    use Core\Model;
    
    class OrderModel extends Model{
        public $id, $shop_id, $client_id, $transporter_id, $parcel_id, $status, $note, $created_time, $verified_time, $ship_fee, $payment_method, $paid, $clientname, $clientphone, $clientaddress, $clientward_id, $shopname, $shopphone, $shopaddress, $shopward_id, $total_price;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_ORDER)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                #lazy 
                foreach($row as $col => $value){
                    $this->$col = $value;
                }
                return true;
            }else{
                return false;
            }
        }
        
        
    }