<?php
    namespace App\Models;
    use Core\Model;
    
    
    class TransporterModel extends Model{
        public $id, $name, $token;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_TRANSPORTER)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                $this->name = $row->name;
                $this->token = $row->token;
                return true;
            }else{
                return false;
            }
        }
    }