<?php
    namespace App\Models;
    use Core\Model;
    use Library\Database\DBNumber;
    use Library\Database\DBString;
    use Library\Database\DBDateTime;
    
    class DeliveryAddressModel extends Model{
        public $id, $firstname, $lastname, $user_id, $def = 0, $address, $ward_id, $phone, $created;
        public $ward;
        
        public function checkValidForId(){
            return $this;
        }
        public function checkValidForUserId(){
            $rows = $this->database->select('*')->from(DB_TABLE_USER)->where('id='. (int)$this->user_id)->execute();
            if(!count($rows)){
                $this->addErrorMessage('user_id', 'Người dùng không tồn tại!');
            }
            return $this;
        }
        public function checkValidForDefault(){
            if(!is_numeric($this->def) || !in_array($this->def, [0,1])){
                $this->addErrorMessage('def', 'Thuộc tính mặc định không hợp lệ!');
            }
            return $this;
        }
        public function checkValidForAddress(){
            if(!is_string($this->address) || mb_strlen($this->address) > 100){
                $this->addErrorMessage('address', 'Địa chỉ không hợp lệ, độ dài địa chỉ tối đa là 100 ký tự!');
            }
            return $this;
        }
        public function checkValidForWardId(){
            $rows = $this->database->select('*')->from(DB_TABLE_WARD)->where('id=' . (int)$this->ward_id)->execute();
            if(!count($rows)){
                $this->addErrorMessage('ward_id', 'Phường xã không tồn tại!');
            }
            return $this;
        }
        public function checkValidForPhone(){
            if(!is_string($this->phone) || !preg_match('/^0\d{9,10}$/', $this->phone)){
                $this->addErrorMessage('phone', 'Số điện thoại không hợp lệ!');
            }
            return $this;
        }
        public function checkValidForFirstName(){
            if(!isset($this->firstname) || !is_string($this->firstname) || mb_strlen($this->firstname) > 20){
                $this->addErrorMessage('firstname', 'Tên không hợp lệ phải có chiều dài tối đa 20 ký tự');
            }else{
                if(empty($this->firstname)){
                    $this->addErrorMessage('firstname', 'Tên không được để trống');
                }
            }
            return $this;
        }
        
        public function checkValidForLastName(){
            if(!isset($this->lastname) || !is_string($this->lastname) || mb_strlen($this->lastname) > 32){
                $this->addErrorMessage('lastname', 'Họ không hợp lệ phải có chiều dài tối đa 32 ký tự');
            }else{
                if(empty($this->lastname)){
                    $this->addErrorMessage('lastname', 'Họ không được để trống');
                }
            }
            return $this;
        }
        public function loadData(){
            $rows = $this->database->select('*')->from(DB_TABLE_DELIVERYADDRESS)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                $this->user_id = $row->user_id;
                $this->address = $row->address;
                $this->created = DBDateTime::parse($row->created);
                $this->def = $row->def;
                $this->phone = $row->phone;
                $this->ward_id = $row->ward_id;
                $this->lastname = $row->lastname;
                $this->firstname = $row->firstname;
                return true;
            }else{
                return false;
            }
        }
        
        public function loadWard(){
            $this->ward = new WardModel($this->database);
            $this->ward->id = $this->ward_id;
            return $this->ward->loadData();
        }
        
        public function add(){
            $rows = $this->database->select('count(*) as count')->from(DB_TABLE_DELIVERYADDRESS)->where('user_id='. (int)$this->user_id)->execute();
            $def = 0;
            if($rows[0]->count){
                if($this->def){
                    $this->database->update(DB_TABLE_DELIVERYADDRESS, ['def' => new DBNumber(0)], 'user_id='. (int)$this->user_id);
                    $def = 1;
                }
            }else{
                $def = 1;
            }
            $this->database->insert(DB_TABLE_DELIVERYADDRESS, ['user_id' => new DBNumber((int)$this->user_id), 'lastname' => new DBString($this->database->escape($this->lastname)), 'firstname'=> new DBString($this->database->escape($this->firstname)), 'def' => new DBNumber($def), 'address' => new DBString($this->database->escape($this->address)), 'ward_id' => new DBNumber($this->ward_id), 'phone' => new DBString($this->phone)]);
            return true;
        }
        
        public function update(DeliveryAddressModel $deliveryaddress){
            #address, phone, ward_id, def
            if($deliveryaddress->def){
                $this->database->update(DB_TABLE_DELIVERYADDRESS, ['def' => new DBNumber(0)], 'user_id='. (int)$this->user_id);
            }
            $this->database->update(DB_TABLE_DELIVERYADDRESS, ['lastname' => new DBString($this->database->escape($deliveryaddress->lastname)), 'firstname' => new DBString($this->database->escape($deliveryaddress->firstname)), 'def' => new DBNumber($deliveryaddress->def), 'address' => new DBString($this->database->escape($deliveryaddress->address)), 'phone' => new DBString($deliveryaddress->phone), 'ward_id' => new DBNumber($deliveryaddress->ward_id)], 'id=' . (int)$this->id);
            $this->lastname = $deliveryaddress->lastname;
            $this->firstname = $deliveryaddress->firstname;
            $this->def = $deliveryaddress->def;
            $this->address = $deliveryaddress->address;
            $this->phone = $deliveryaddress->phone;
            $this->ward_id = $deliveryaddress->ward_id;
            return true;
        }
        
        public function delete(){
            if($this->def == 1){
                return false;
            }
            $this->database->delete(DB_TABLE_DELIVERYADDRESS, 'id='.(int)$this->id);
            return true;
        }
    }