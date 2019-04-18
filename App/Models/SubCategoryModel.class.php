<?php
    namespace App\Models;
    use Library\Database\DBString;
    use Library\Database\DBNumber;
    use Core\Model;
    
    class SubCategoryModel extends Model{
        #name:
        public $id, $name, $link, $norder, $maincategory_id, $maincategory;
        #danh mục phụ bao gồm nhiều sản phẩm
        #sản phẩm thuộc vào một danh mục phụ
        public $products;
        
        public function getId() {
            return $this->id;
        }

        public function getName() {
            return $this->name;
        }

        public function getLink() {
            return $this->link;
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
        
        public function checkValidForLink(){
            if(isset($this->link) && is_string($this->link)){
                $length = mb_strlen($this->link);
                if($length > 1024){
                    $this->addErrorMessage('link', 'Liên kết danh mục phụ có chiều dài không hợp lệ, độ dài phải nhỏ hơn 1024 ký tự');
                }
            }else{
                $this->addErrorMessage('link', 'Liên kết không hợp lệ!');
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
            $this->link = $this->database->escape($this->link);
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
                $this->name = $row->name;
                $this->link = $row->link;
                $this->norder = $row->norder;
                $this->maincategory_id = $row->maincategory_id;
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
        
        public function add(){
            $rows = $this->database->select('max(norder) as maxorder')->from(DB_TABLE_SUBCATEGORY)->lock();
            $maxorder = $rows[0]->maxorder;
            $maxorder += 1;
            $this->database->insert(DB_TABLE_SUBCATEGORY, ['name'=>new DBString($this->name), 'link'=>new DBString($this->link), 'maincategory_id'=>new DBNumber($this->maincategory_id), 'norder'=>new DBNumber($maxorder)]);
            return true;
        }
        
        public function update($subcate){
            $this->database->update(DB_TABLE_SUBCATEGORY, ['name'=>new DBString($subcate->name), 'link'=>new DBString($subcate->link), 'maincategory_id'=>new DBNumber($subcate->maincategory_id)], 'id='.new DBNumber($this->id));
            $this->name = $subcate->name;
            $this->link = $subcate->link;
            return true;
        }
        
        public function delete(){
            $this->database->delete(DB_TABLE_SUBCATEGORY, 'id='. (int)$this->id);
            return true;
        }
    }