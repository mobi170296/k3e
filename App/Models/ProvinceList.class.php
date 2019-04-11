<?php
    namespace App\Models;
    use Core\Model;
    
    class ProvinceList extends Model{
        public $list = [];
        public function getAll(){
            $this->list = [];
            $rows = $this->database->select('*')->from(DB_TABLE_PROVINCE)->execute();
            foreach($rows as $row){
                $province = new ProvinceModel($this->database);
                $province->id = $row->id;
                $province->loadData();
                $this->list[] = $province;
            }
            
            return $this->list;
        }
    }
    