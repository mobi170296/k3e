<?php
    namespace App\Models;
    use Core\Model;
    
    use Library\Database\DBNumber;
    use Library\Database\DBString;
    
    class OrderLogModel extends Model{
        public $id, $order_id, $order_status, $content, $created_time;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_ORDERLOG)->where('id=' . (int)$this->id)->execute();
            
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
        
        public function add(){
            $this->database->insert(DB_TABLE_ORDERLOG, [
                'order_id' => new DBNumber($this->order_id),
                'order_status' => new DBNumber($this->order_status),
                'content' => new DBString($this->content)
            ]);
            
            return true;
        }
    }