<?php
    namespace App\Models;
    use Core\Model;
    
    class DistrictList extends Model{
        public $list = [];
        public function getAllFromProvince($id){
            $rows = $this->database->select('*')->from(DB_TABLE_DISTRICT)->where('province_id=' . (int)$id)->execute();
            foreach($rows as $row){
                $district = new DistrictModel($this->database);
                $district->id = $row->id;
                $district->loadData();
                $this->list[] = $district;
            }
            return $this->list;
        }
    }
