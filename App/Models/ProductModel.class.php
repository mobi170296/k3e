<?php
    namespace App\Models;
    use Core\Model;
    use Library\Database\DBString;
    use Library\Database\DBNumber;
    use Library\Database\DBRaw;
    
    class ProductModel extends Model{
        const LOCKED = 1, UNLOCKED = 0;
        const VERIFIED = 1, UNVERIFIED = 0;
        public $id, $name, $description, $quantity, $shop_id, $original_price, $price, $subcategory_id, $weight, $length, $width, $height, $main_image_id;
        public $created_time, $locked, $verified, $verified_time, $warranty_months_number;
        
        public $productimages = [];
        public $productattributes = [];
        public $carditems = [];
        public $assessments = [];
        public $orderitems = [];
        
        public $shop, $subcategory;
        
        public function checkName(){
            if(!is_string($this->name)){
                $this->addErrorMessage('name', 'Tên sản phẩm không hợp lệ!');
            }else{
                if(mb_strlen($this->name)==0){
                    $this->addErrorMessage('name', 'Tên sản phẩm không được phép bỏ trống');
                }elseif(mb_strlen($this->name) > 200){
                    $this->addErrorMessage('name', 'Tên sản phẩm không được vượt quá 200 ký tự');
                }
            }
            
            return $this;
        }
        
        public function checkDescription(){
            $doc = new \DOMDocument();
            $doc->loadHTML($this->description);
            $length = mb_strlen($doc->documentElement->textContent);
            if($length < 100){
                $this->addErrorMessage('description', 'Mô tả phải có độ dài ít nhất 100 ký tự');
            }
            return $this;
        }
        
        public function checkQuantity(){
            if(!is_numeric($this->quantity)){
                $this->addErrorMessage('quantity', 'Số lượng của sản phẩm không hợp lệ!');
            }else{
                if($this->quantity > 999999 || $this->quantity < 0){
                    $this->addErrorMessage('quantity', 'Số lượng sản phẩm phải từ 0 đến 999,999 đơn vị');
                }
            }
            return $this;
        }
        
        public function checkShopId(){
            if(is_numeric($this->shop_id)){
                $rows = $this->database->select('count(*) as count')->from(DB_TABLE_SHOP)->where('id=' . (int)$this->shop_id)->execute();
                if($rows[0]->count==0){
                    $this->addErrorMessage('shop_id', 'Cửa hàng không tồn tại');
                }
            }else{
                $this->addErrorMessage('shop_id', 'Cửa hàng không tồn tại');
            }
            return $this;
        }
        
        public function checkSubcategoryId(){
            if(is_numeric($this->subcategory_id)){
                $rows = $this->database->select('count(*) as count')->from(DB_TABLE_SUBCATEGORY)->where('id=' . (int)$this->subcategory_id)->execute();
                if($rows[0]->count==0){
                    $this->addErrorMessage('subcategory_id', 'Danh mục không tồn tại');
                }
            }else{
                $this->addErrorMessage('subcategory_id', 'Danh mục không tồn tại');
            }
            return $this;
        }
        
        public function checkOriginalPrice(){
            #0 -> 100e6
            if(!is_numeric($this->original_price)){
                $this->addErrorMessage('original_price', 'Giá gốc của sản phẩm không hợp lệ');
            }else{
                if($this->original_price < 0 || $this->original_price > 100e6){
                    $this->addErrorMessage('original_price', 'Giá gốc của sản phẩm không được vượt quá giới hạn phải từ 0 đến 100 triệu vnđ');
                }
            }
            return $this;
        }
        
        public function checkPrice(){
            if(!is_numeric($this->price)){
                $this->addErrorMessage('price', 'Giá bán của sản phẩm không hợp lệ');
            }else{
                if($this->price < 10e3 || $this->price > 100e6){
                    $this->addErrorMessage('price', 'Giá bán của sản phẩm không được vượt quá giới hạn phải từ 0 đến 100 triệu vnđ');
                }
            }
            return $this;
        }
        
        public function checkWeight(){
            #units gram
            if(!is_numeric($this->weight)){
                $this->addErrorMessage('weight', 'Cân nặng không hợp lệ!');
            }else{
                #PHP auto convert string to number when context require number
                if($this->weight <= 10){
                    $this->addErrorMessage('weight', 'Cân nặng không thể nhỏ hơn 10g');
                }elseif($this->weight > 1e5){
                    $this->addErrorMessage('weight', 'Trọng lượng không thể vượt quá 100Kg');
                }
            }
            return $this;
        }
        
        public function checkLength(){
            #centimet
            if(!is_numeric($this->length) || $this->length > 200 || $this->length <= 0){
                $this->addErrorMessage('length', 'Chiều dài không hợp lệ');
            }
            return $this;
        }
        
        public function checkWidth(){
            #centimet
            if(!is_numeric($this->width) || $this->width > 200 || $this->width <= 0){
                $this->addErrorMessage('width', 'Chiều dài không hợp lệ');
            }
            return $this;
        }
        
        public function checkHeight(){
            #centimet
            if(!is_numeric($this->height) || $this->height > 200 || $this->height <= 0){
                $this->addErrorMessage('height', 'Chiều dài không hợp lệ');
            }
            return $this;
        }
        
        public function checkWarrantyMonthsNumber(){
            if(!is_numeric($this->warranty_months_number) || $this->warranty_months_number < 0){
                $this->addErrorMessage('warranty_months_number', 'Thời gian bảo hành (tính theo tháng) không hợp lệ');
            }
            return $this;
        }
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_PRODUCT)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                #lazy load
                foreach($row as $k => $v){
                    $this->$k = $v;
                }
                return true;
            }else{
                return false;
            }
        }
        
        public function loadShop(){
            $this->shop = new ShopModel($this->database);
            $this->shop->id = $this->shop_id;
            $this->shop->loadData();
        }
        
        public function loadSubcategory(){
            $this->subcategory = new SubCategoryModel($this->database);
            $this->subcategory->id = $this->subcategory_id;
            $this->subcategory->loadData();
        }
        
        public function loadProductImages(){
            $rows = $this->database->selectall()->from(DB_TABLE_PRODUCTIMAGE)->where('product_id=' . (int)$this->id)->execute();
            $this->productimages = [];
            foreach($rows as $row){
                $productimage = new ProductImageModel($this->database);
                $productimage->product_id = $row->product_id;
                $productimage->norder = $row->norder;
                $productimage->loadData();
                $this->productimages[] = $productimage;
            }
            return true;
        }
        
        public function loadProductAttributes(){
            $this->productattributes = [];
            $rows = $this->database->selectall()->from(DB_TABLE_PRODUCTATTRIBUTE)->where('product_id=' . (int)$this->id)->execute();
            foreach($rows as $row){
                $productattribute = new ProductAttributeModel($this->database);
                $productattribute->product_id = $row->product_id;
                $productattribute->norder = $row->norder;
                $productattribute->loadData();
                $this->productattributes[] = $productattribute;
            }
            return true;
        }
        
        public function add(){
            $this->database->insert(DB_TABLE_PRODUCT, ['name' => new DBString($this->database->escape($this->name)), 'description' => new DBString($this->database->escape($this->description)), 'quantity' => new DBNumber($this->quantity), 'shop_id' => new DBNumber($this->shop_id), 'original_price' => new DBNumber($this->original_price), 'price' => new DBNumber($this->price), 'subcategory_id' => new DBNumber($this->subcategory_id), 'weight' => new DBNumber($this->weight), 'length' => new DBNumber($this->length), 'width' => new DBNumber($this->width), 'height' => new DBNumber($this->height), 'created_time' => new DBRaw('now()'), 'locked' => new DBNumber(self::UNLOCKED), 'verified' => new DBNumber(self::VERIFIED), 'verified_time' => new DBRaw('now()'), 'warranty_months_number' => new DBNumber($this->warranty_months_number), 'main_image_id' => new DBNumber($this->main_image_id)]);
            return true;
        }
        
        public function update(){
            #Khong duoc cap nhat mot so truong khi san pham da duoc mua
            #Duoc cap nhat tat ca khi san pham chua duoc mua
            
        }
        
        public function delete(){
            #cho phep xoa khi san pham chua duoc mua
            if(!$this->hasBought()){
                $this->database->delete(DB_TABLE_PRODUCT, 'id=' . (int)$this->id);
            }
        }
        
        public function hasBought(){
            $rows = $this->database->select('count(*) as count')->from(DB_TABLE_ORDERITEM)->where('product_id=' . (int)$this->id)->execute();
            return $rows[0]->count != 0;
        }
    }