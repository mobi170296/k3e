<?php
    namespace App\Models;
    use Core\Model;
    
    class DistrictModel extends Model{
        public $id, $province_id, $name;
        
        public $province;
        public $products = [];
        public $users = [];
        public $wards = [];
        
        public function loadData(){
            //load from id
            $rows = $this->database->selectall()->from(DB_TABLE_DISTRICT)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $this->id = $rows[0]->id;
                $this->province_id = $rows[0]->province_id;
                $this->name = $rows[0]->name;
                return true;
            }else{
                return false;
            }
        }
        
        public function loadProvince(){
            $this->province = new ProvinceModel($this->database);
            $this->province->id = $this->province_id;
            $this->province->loadData();
        }
        
        public function loadWards(){
            $this->wards = [];
            $rows = $this->database->selectall()->from(DB_TABLE_WARD)->where('district_id=' . (int)$this->id)->execute();
            foreach($rows as $row){
                $ward = new WardModel($this->database);
                $ward->id = $row->id;
                $ward->loadData();
                $this->wards[] = $ward;
            }
        }
    }