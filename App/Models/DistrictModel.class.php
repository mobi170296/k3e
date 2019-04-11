<?php
    namespace App\Models;
    use Core\Model;
    
    class DistrictModel extends Model{
        public $id, $province_id, $name;
        
        public $province;
        public $products;
        public $users;
        
        public function loadData(){
            //load from id
            $rows = $this->database->select('*')->from(DB_TABLE_DISTRICT)->where('id=' . (int)$this->id)->execute();
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
    }