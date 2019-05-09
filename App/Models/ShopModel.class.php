<?php
    namespace App\Models;
    use Core\Model;
    use Library\Database\DBDateTime;
    use Library\Database\DBString;
    use Library\Database\DBNumber;
    
    class ShopModel extends Model{
        const LOCK = 1, UNLOCK = 0;
        
        public $id, $name, $owner_id, $description, $avatar_id, $background_id, $locked, $created_time;
        
        #foreign key objects
        public $owner, $avatar, $background;
        
        #contains objects
        public $orders, $products;
        
        #address object
        public $ward;
        
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
        
        public function loadOrders(){
            $this->orders = [];
            $rows = $this->database->select('id')->from(DB_TABLE_ORDER)->where('shop_id=' . (int)$this->id)->execute();
            foreach($rows as $row){
                $order = new OrderModel($this->database);
                $order->id = $row->id;
                if($order->loadData()){
                    $this->orders[] = $order;
                }
            }
        }
        
        public function loadProducts(){
            $this->products = [];
            $rows = $this->database->select('id')->from(DB_TABLE_PRODUCT)->where('shop_id=' . (int)$this->id)->execute();
            foreach($rows as $row){
                $product = new ProductModel($this->database);
                $product->id = $row->id;
                if($product->loadData()){
                    $this->products[] = $product;
                }
            }
        }
        
        public function loadOwner(){
            $this->owner = new UserModel($this->database);
            $this->owner->id = $this->owner_id;
            return $this->owner->loadData();
        }
        
        public function loadAvatar(){
            $this->avatar = new ImageMapModel($this->database);
            $this->avatar->id = $this->avatar_id;
            return $this->avatar->loadData();
        }
        
        public function loadBackground(){
            $this->background = new ImageMapModel($this->database);
            $this->background->id = $this->background_id;
            return $this->background->loadData();
        }
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_SHOP)->where('id=' . $this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                #lazy load
                
                foreach($row as $key => $value){
                    $this->$key = $value;
                }
                return true;
            }else{
                return false;
            }
        }
        
        public function loadWard(){
            $rows = $this->database->select('ward.id as id')->from(DB_TABLE_SHOP)->join(DB_TABLE_USER)->on('shop.owner_id=user.id')->join(DB_TABLE_DELIVERYADDRESS)->on('deliveryaddress.user_id=user.id')->join(DB_TABLE_WARD)->on('deliveryaddress.ward_id=ward.id')->where('deliveryaddress.def=' . DeliveryAddressModel::DEF . ' and shop.id=' . (int)$this->id)->execute();
            
            if(count($rows)){
                $row = $rows[0];
                $this->ward = new WardModel($this->database);
                $this->ward->id = $row->id;
                return $this->ward->loadData();
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
        
        
        public function update(ShopModel $shop){
            $id = (int)$this->id;
            $name = $this->database->escape($shop->name);
            $description = $this->database->escape($shop->description);
            $this->database->update(DB_TABLE_SHOP, ['name' => new DBString($name), 'description' => new DBString($description)], 'id=' . $id);
            $this->name = $shop->name;
            $this->description = $shop->description;
        }
        
        #@@@
        public function delete(){
            #@@@
        }
        
        public function lock(){
            $this->database->update(DB_TABLE_SHOP, ['lock' => new DBNumber(ShopModel::LOCK)], 'id='.(int)$this->id);
        }
        
        public function getAvatarPath(){
            if($this->avatar_id === null){
                #default logo
                $this->loadOwner();
                return $this->owner->gender == UserModel::FEMALE ? '/images/shops/girlavatar.png' : '/images/shops/menavatar.png';
            }else{
                if($this->avatar!=null){
                    return $this->avatar->urlpath;
                }else{
                    $this->avatar = new ImageMapModel($this->database);
                    $this->avatar->id = $this->avatar_id;
                    $this->avatar->loadData();
                    return $this->avatar->urlpath;
                }
            }
        }
        
        public function getBackgroundPath(){
            if($this->background_id === null){
                #default logo
                return '/images/shops/defwallpaper.jpg';
            }else{
                if($this->background!=null){
                    return $this->background->urlpath;
                }else{
                    $this->background = new ImageMapModel($this->database);
                    $this->background->id = $this->background_id;
                    $this->background->loadData();
                    return $this->background->urlpath;
                }
            }
        }
        
        public function updateAvatarId($id){
            $id = (int)$id;
            $this->database->update(DB_TABLE_SHOP, ['avatar_id' => new DBNumber($id)], 'id=' . (int)$this->id);
            $this->avatar_id = $id;
        }
        
        public function updateBackgroundId($id){
            $id = (int)$id;
            $this->database->update(DB_TABLE_SHOP, ['background_id' => new DBNumber($id)], 'id=' . (int)$this->id);
            $this->background_id = $id;
        }
        
        public function getProductTotal(){
            $rows = $this->database->select('count(*) as count ')->from(DB_TABLE_PRODUCT)->where('shop_id=' . (int)$this->id)->execute();
            $row = $rows[0];
            return $row->count;
        }
        
        public function getWaitOrdersTotal(){
            $rows = $this->database->select('count(*) as count')->from(DB_TABLE_ORDER)->where('order.shop_id=' . (int)$this->id . ' and order.status=' . OrderModel::CHO_NGUOI_BAN_XAC_NHAN)->execute();
            
            return $rows[0]->count;
        }
        
        public function getToshipOrdersTotal(){
            $rows = $this->database->select('count(*) as count')->from(DB_TABLE_ORDER)->where('order.shop_id=' . (int)$this->id . ' and order.status=' . OrderModel::CHO_LAY_HANG)->execute();
            
            return $rows[0]->count;
        }
        
        public function getShippingOrdersTotal(){
            
            $rows = $this->database->select('count(*) as count')->from(DB_TABLE_ORDER)->where('order.shop_id=' . (int)$this->id . ' and order.status=' . OrderModel::DANG_GIAO)->execute();
            
            return $rows[0]->count;
        }
        
        public function getCompletedOrdersTotal(){
            $array = [OrderModel::HOAN_TAT, OrderModel::DA_GIAO];
            
            $in = '(' . implode(',', $array) . ')';
            $rows = $this->database->select('count(*) as count')->from(DB_TABLE_ORDER)->where('order.shop_id=' . (int)$this->id . ' and order.status in ' . $in)->execute();
            
            return $rows[0]->count;
        }
        
        public function getCancelledOrdersTotal(){
            $array = [OrderModel::HUY_DON_HANG, OrderModel::KHONG_CON_HANG, OrderModel::HUY_DO_KHONG_LAY_DUOC_HANG, OrderModel::HUY_DO_HE_THONG, OrderModel::GIAO_THAT_BAI];
            
            $in = '(' . implode(',', $array) . ')';
            $rows = $this->database->select('count(*) as count')->from(DB_TABLE_ORDER)->where('order.shop_id=' . (int)$this->id . ' and order.status in ' . $in)->execute();
            
            return $rows[0]->count;
        }
        
        
        public function getWaitOrders(){
            $orders = [];
            
            $rows = $this->database->select('id')->from(DB_TABLE_ORDER)->where('shop_id=' . (int)$this->id . ' and order.status=' . OrderModel::CHO_NGUOI_BAN_XAC_NHAN)->execute();
            
            foreach($rows as $row){
                $order = new OrderModel($this->database);
                $order->id = $row->id;
                $order->loadData();
                
                $orders[] = $order;
            }
            
            return $orders;
        }
    }