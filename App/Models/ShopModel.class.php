<?php
    namespace App\Models;
    use Core\Model;
    use Library\Database\DBDateTime;
    
    class ShopModel extends Model{
        public $id, $name, $owner_id, $description, $phone, $address, $district_id, $locked, $created_time;
        public function getLink(){
            return "/Shop/" . $this->id;
        }
        
        public function checkID(){
            return $this;
        }
        
        public function checkName(){
            
            return $this;
        }
        
        public function checkOwnerId(){
            return $this;
        }
        
        public function checkDescription(){
            return $this;
        }
        
        public function checkPhone(){
            return $this;
        }
        
        public function checkDistrictId(){
            #database check
            return $this;
        }
        
        public function checkLocked(){
            return $this;
        }
        
        public function checkCreatedTime(){
            return $this;
        }
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_SHOP)->where('id=' . $this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                $this->id = $row->id;
                $this->name = $row->name;
                $this->owner_id = $row->owner_id;
                $this->description = $row->description;
                $this->phone = $row->phone;
                $this->district_id = $row->district_id;
                $this->locked = $row->locked;
                $this->created_time = DBDateTime::parse($row->created_time);
                return true;
            }else{
                return false;
            }
        }
        
        public function Open(){
            
        }
    }