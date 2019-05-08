<?php
    namespace App\Models;
    use Core\Model;
    
    class PaymentTypeModel extends Model{
        const COD = 1, ONEPAY = 2;
        public $id, $code, $name;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_PAYMENTTYPE)->where('id=' . (int)$this->id)->execute();
            
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
    }