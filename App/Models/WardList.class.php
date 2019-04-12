<?php
    namespace App\Models;
    use Core\Model;
    
    class WardList extends Model{
        public $list;
        public function getAllFromDistrict($district_id){
            $this->list = [];
            $rows = $this->database->selectall()->from(DB_TABLE_WARD)->where('district_id=' . (int)$district_id)->execute();
            foreach($rows as $row){
                $ward = new WardModel($this->database);
                $ward->id = $row->id;
                $ward->loadData();
                $this->list[] = $ward;
            }
            return $this->list;
        }
    }