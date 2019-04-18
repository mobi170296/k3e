<?php
    namespace App\Models;
    use Core\Model;
    use Library\Database\DBDateTime;
    use Library\Database\DBString;
    use Library\Database\DBNumber;
    
    class ShopModel extends Model{
        const LOCK = 1, UNLOCK = 0;
        
        public $id, $name, $owner_id, $description, $logo_id, $locked, $created_time;
        public $owner;
        public $logo;
        
        
        public function getLink(){
            return "/Shop/" . $this->id;
        }
        
        public function checkID(){
            return $this;
        }
        
        public function checkName(){
            if(!is_string($this->name) || mb_strlen($this->name) === 0){
                $this->addErrorMessage('name', 'Tên cửa hàng không được để trống');
            }else{
                if(mb_strlen($this->name) > 40){
                    $this->addErrorMessage('name', 'Tên cửa hàng không được vượt quá 40 ký tự');
                }
            }
            return $this;
        }
        
        public function checkOwnerId(){
            
            return $this;
        }
        
        public function checkDescription(){
            if(!is_string($this->description)){
                $this->addErrorMessage('description', 'Không chấp nhận mô tả dạng này!');
            }else{
                if(mb_strlen($this->description) > 512){
                    $this->addErrorMessage('description', 'Mô tả không được vượt quá 512 ký tự');
                }
            }
            return $this;
        }
        
        public function checkLocked(){
            return $this;
        }
        
        public function checkCreatedTime(){
            return $this;
        }
        
        public function LoadOwner(){
            $this->owner = new UserModel($this->database);
            $this->owner->id = $this->owner_id;
            return $this->owner->loadData();
        }
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_SHOP)->where('id=' . $this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                $this->id = $row->id;
                $this->name = $row->name;
                $this->owner_id = $row->owner_id;
                $this->description = $row->description;
                $this->locked = $row->locked;
                $this->logo_id = $row->logo_id;
                $this->created_time = DBDateTime::parse($row->created_time);
                return true;
            }else{
                return false;
            }
        }
        
        public function open(){
            $name = $this->database->escape($this->name);
            $owner_id = (int)$this->owner_id;
            $description = $this->database->escape($this->description);
            $this->database->insert(DB_TABLE_SHOP, ['name' => new DBString($name), 'owner_id' => new DBNumber($owner_id), 'description' => new DBString($description), 'locked' => new DBNumber(self::UNLOCK)]);
        }
        
        
        public function Update(ShopModel $shop){
            $id = (int)$this->id;
            $name = $this->database->escape($shop->name);
            $description = $this->database->escape($shop->description);
            $this->database->update(DB_TABLE_SHOP, ['name' => new DBString($name), 'description' => new DBString($description)], 'id=' . $id);
            $this->name = $shop->name;
            $this->description = $shop->description;
        }
        
        #@@@
        public function Delete(){
            #@@@
        }
        
        public function Lock(){
            $this->database->update(DB_TABLE_SHOP, ['lock' => new DBNumber(ShopModel::LOCK)], 'id='.(int)$this->id);
        }
        
        public function getLogoPath(){
            if($this->logo_id === null){
                #default logo
                return "";
            }else{
                if($this->logo!=null){
                    return $this->logo->urlpath;
                }else{
                    $rows = $this->database->selectall()->from(DB_TABLE_IMAGEMAP)->from('id=' . (int)$this->logo_id)->execute();
                    if(count($rows)){
                        $row = $rows[0];
                        return $row->urlpath;
                    }else{
                        return "";
                    }
                }
            }
        }
        
        public function updateLogoId($id){
            $id = (int)$id;
            $this->database->update(DB_TABLE_SHOP, ['logo_id' => new DBNumber($id)], '');
            $this->logo_id = $id;
        }
    }