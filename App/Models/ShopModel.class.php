<?php
    namespace App\Models;
    use Core\Model;
    
    class ShopModel extends Model{
        public $id, $name, $owner_id, $description, $phone, $address, $district_id, $locked, $created_time, $verified, $verified_time;
        public function getLink(){
            return "/Shop/" . $this->id;
        }
    }