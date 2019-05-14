<?php
    namespace App\Models;
    use Library\Database\DBString;
    use Library\Database\DBNumber;
    use Library\Database\DBDateTime;
    use Core\Model;
    
    class SubCategoryModel extends Model{
        #name:
        public $id, $name, $norder, $maincategory_id, $maincategory, $created_time;
        #danh mục phụ bao gồm nhiều sản phẩm
        

        #sản phẩm thuộc vào một danh mục phụ
        public $products;
        
        public function getId() {
            return $this->id;
        }

        public function getName() {
            return $this->name;
        }
        public function getMainCategory() {
            return $this->maincategory;
        }
        
        public function getMainCategoryId(){
            return $this->maincategory_id;
        }
        
        public function checkValidForName(){
            if(isset($this->name) && is_string($this->name)){
                $length = mb_strlen($this->name);
                if($length===0){
                    $this->addErrorMessage('name', 'Tên danh mục phụ không được để trống');
                }
                if($length>200){
                    $this->addErrorMessage('name', 'Tên danh mục phụ có độ dài không được vượt quá 200 ký tự');
                }
            }else{
                $this->addErrorMessage('name', 'Tên danh mục phụ không hợp lệ');
            }
            
            return $this;
        }
        
        
        public function checkValidForNOrder(){
            if(!isset($this->norder) || !is_numeric($this->norder)){
                $this->addErrorMessage('norder', 'Thứ tự danh mục không hợp lệ!');
            }
            return $this;
        }
        
        public function checkValidForId(){
            if(!isset($this->id) || !is_numeric($this->id)){
                $this->addErrorMessage('id', 'ID không hợp lệ!');
            }
            return $this;
        }
        
        public function checkValidForMainCategoryId(){
            if(!isset($this->maincategory_id) || !is_numeric($this->maincategory_id)){
                $this->addErrorMessage('maincategory_id', 'ID danh mục chính không hợp lệ!');
            }else{
                $rows = $this->database->select('count(*) as total')->from(DB_TABLE_MAINCATEGORY)->where('id='. $this->maincategory_id)->execute();
                if(!$rows[0]->total){
                    $this->addErrorMessage('db_maincategory_id', 'Danh mục chính không tồn tại');
                }
            }
            
            return $this;
        }
        
        public function standardization(){
            $this->name = $this->database->escape($this->name);
            return $this;
        }
        
        public function hasProduct(){
            $rows = $this->database->select('count(*) total')->from(DB_TABLE_SUBCATEGORY)->join(DB_TABLE_PRODUCT)->on('subcategory.id=product.subcategory_id')->where('subcategory.id=' . (int)$this->id)->execute();
            return $rows[0]->total != 0;
        }
        
        public function loadData(){
            $rows = $this->database->select('*')->from(DB_TABLE_SUBCATEGORY)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                foreach($row as $k => $v){
                    $this->$k = $v;
                }
                $this->created_time = DBDateTime::parse($row->created_time);
                return true;
            }else{
                return false;
            }
        }
        
        public function loadMainCategory(){
            $this->maincategory = new MainCategoryModel($this->database);
            $this->maincategory->id = $this->maincategory_id;
            return $this->maincategory->loadData();
        }
        
        #load nhung san pham thuoc danh muc nay
        public function loadProducts($from, $total){
            $this->products = [];
            $rows = $this->database->select('product.id as id')->from(DB_TABLE_SUBCATEGORY)->join(DB_TABLE_PRODUCT)->on('subcategory.id=product.subcategory_id')->limit($from, $total)->where('subcategory.id='. (int)$this->id)->execute();
            foreach($rows as $row){
                $product = new ProductModel($this->database);
                $product->id = $row->id;
                if($product->loadData()){
                    $this->products[] = $product;
                }else{
                    return false;
                }
            }
            return true;
        }
        
        public function add(){
            $rows = $this->database->select('max(norder) as maxorder')->from(DB_TABLE_SUBCATEGORY)->lock();
            $maxorder = $rows[0]->maxorder;
            $maxorder += 1;
            $this->database->insert(DB_TABLE_SUBCATEGORY, ['name'=>new DBString($this->name), 'maincategory_id'=>new DBNumber($this->maincategory_id), 'norder'=>new DBNumber($maxorder)]);
            return true;
        }
        
        public function update($subcate){
            $this->database->update(DB_TABLE_SUBCATEGORY, ['name'=>new DBString($subcate->name), 'maincategory_id'=>new DBNumber($subcate->maincategory_id)], 'id='.new DBNumber($this->id));
            $this->name = $subcate->name;
            return true;
        }
        
        public function delete(){
            $this->database->delete(DB_TABLE_SUBCATEGORY, 'id='. (int)$this->id);
            return true;
        }
    }