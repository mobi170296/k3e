<?php
    namespace App\Models;
    use Core\Model;
    
    
    class TransporterModel extends Model{
        public $id, $code, $name;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_TRANSPORTER)->where('id=' . (int)$this->id)->execute();
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