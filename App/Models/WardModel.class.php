<?php
    namespace App\Models;
    use Core\Model;
    
    class WardModel extends Model{
        public $id, $code, $district_id, $name, $ghn_ward_code;
        public $district;
        
        
        public function loadData(){
            $rows = $this->database->select('*')->from(DB_TABLE_WARD)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                $this->id = $row->id;
                $this->code = $row->code;
                $this->district_id = $row->district_id;
                $this->name = $row->name;
                $this->ghn_ward_code = $row->ghn_ward_code;
                return true;
            }else{
                return false;
            }
        }
        
        public function loadDistrict(){
            $rows = $this->database->selectall()->from(DB_TABLE_DISTRICT)->where('id=' . (int)$this->district_id)->execute();
            if(count($rows)){
                $this->district = new DistrictModel($this->database);
                $this->district->id = $rows[0]->id;
                return $this->district->loadData();
            }else{
                return false;
            }
        }
    }