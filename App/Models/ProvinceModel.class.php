<?php
    namespace App\Models;
    use Core\Model;
    
    class ProvinceModel extends Model{
        public $id, $name;
        
        public $districts = [];
        
        public function loadData(){
            $rows = $this->database->select('*')->from(DB_TABLE_PROVINCE)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $this->id = $rows[0]->id;
                $this->name = $rows[0]->name;
                return true;
            }else{
                return false;
            }
        }
        
        public function loadDistricts(){
            $rows = $this->database->select('*')->from(DB_TABLE_DISTRICT)->where('province_id='. (int)$this->id)->execute();
            foreach($rows as $row){
                $district = new DistrictModel();
                $district->id = $row->id;
                $district->name = $row->name;
                $district->province_id = $row->province_id;
                $this->districts[] = $district;
            }
        }
    }