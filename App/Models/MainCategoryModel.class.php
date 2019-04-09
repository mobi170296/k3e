<?php
    namespace App\Models;
    use Library\Database\DBString;
    use Library\Database\DBNumber;
    
    class MainCategoryModel extends \Core\Model{
        public $id, $name, $link, $norder, $subcategories = [];
        
        public function setId($id) {
            $this->id = $id;
            return $this;
        }

        public function setName($name) {
            $this->name = $name;
            return $this;
        }

        public function setLink($link) {
            $this->link = $link;
            return $this;
        }

        public function setSubcategories($subcategory) {
            $this->subcategory = $subcategory;
            return $this;
        }
        
        public function setNOrder($norder){
            $this->norder = $norder;
            return $this;
        }
        
        public function getId(){
            return $this->id;
        }
        
        public function getName(){
            return $this->name;
        }
        
        public function getLink(){
            return $this->link;
        }
        
        public function getNOrder(){
            return $this->norder;
        }
        
        public function getSubCategories(){
            return $this->subcategories;
        }
        
        public function checkValidForName(){
            if(isset($this->name) && is_string($this->name)){
                $length = mb_strlen($this->name);
                if($length===0){
                    $this->addErrorMessage('name', 'Tên danh mục không được phép rỗng');
                }
                if($length>200){
                    $this->addErrorMessage('name', 'Tên danh mục không được phép vượt quá 200 ký tự');
                }
            }else{
                $this->addErrorMessage('name', 'Tên danh mục chính không hợp lệ!');
            }
            return $this;
        }
        
        public function checkValidForLink(){
            if(isset($this->link) && is_string($this->link)){
                $length = mb_strlen($this->link);
                if($length > 1024){
                    $this->addErrorMessage('link', 'Độ dài liên kết không hợp lệ phải không vượt quá 1024 ký tự');
                }
            }else{
                $this->addErrorMessage('link', 'Liên kết không hợp lệ!');
            }
            
            return $this;
        }
        
        public function checkValidForId(){
            if(!isset($this->id) || !is_numeric($this->id)){
                $this->addErrorMessage('id', 'ID không hợp lệ');
            }
            return $this;
        }
        
        public function checkValidForNOrder(){
            if(!isset($this->norder) || !is_numeric($this->norder) || $this->norder<0){
                $this->addErrorMessage('norder', 'Số thứ tự danh mục không hợp lệ');
            }
            return $this;
        }
        
        public function standardization(){
            $this->name = $this->database->escape($this->name);
            $this->link = $this->database->escape($this->link);
            return $this;
        }
        
        public function loadData(){
            $rows = $this->database->select('*')->from(DB_TABLE_MAINCATEGORY)->where('id=' . (new DBNumber((int)$this->id))->SqlValue())->execute();
            if(count($rows)){
                $row = $rows[0];
                $this->name = $row->name;
                $this->link = $row->link;
                $this->norder = $row->norder;
                return true;
            }else{
                return false;
            }
        }
        
        public function loadSubCategories(){
            $rows = $this->database->select('*')->from(DB_TABLE_SUBCATEGORY)->where('maincategory_id=' . (new DBNumber((int)$this->id))->SqlValue())->execute();
            foreach($rows as $row){
                $subcategory = new SubCategoryModel($this->database);
                $subcategory->id = $row->id;
                if($subcategory->loadData()){
                    $this->subcategories[] = $subcategory;
                }
            }
        }
        
        public function add(){
            $rows = $this->database->select('max(norder) as maxorder')->from(DB_TABLE_MAINCATEGORY)->lock();
            $row = $rows[0];
            $nextid = $row->maxorder + 1;
            $this->database->insert(DB_TABLE_MAINCATEGORY, ['name'=>new DBString($this->name), 'link'=>new DBString($this->link), 'norder'=>new DBNumber($nextid)]);
            return true;
        }
        
        public function delete(){
            $this->database->delete(DB_TABLE_MAINCATEGORY, 'id=' . new DBNumber($this->id));
            return true;
        }
        
        public function update($mc){
            $this->database->update(DB_TABLE_MAINCATEGORY, ['name'=>new DBString($mc->name), 'link'=>new DBString($mc->link)], 'id=' . (new DBNumber($this->id))->SqlValue());
            $this->name = $mc->name;
            $this->link = $mc->link;
            $this->norder = $mc->norder;
            return true;
        }
        
        public function moveUp(){
            $rows = $this->database->select('*')->from(DB_TABLE_MAINCATEGORY)->orderby('norder')->desc()->lock();
            
            $upper = null;
            
            if(count($rows) && $this->norder > $rows[count($rows) - 1]->norder){
                foreach($rows as $row){
                    if($row->norder < $this->norder){
                        $upper = $row;
                        break;
                    }
                }
                
                if($upper != null){
                    #trao doi 2 phan tu nay
                    $this->database->update(DB_TABLE_MAINCATEGORY, ['norder'=>new DBNumber(10000)], 'id='. $this->id);
                    $this->database->update(DB_TABLE_MAINCATEGORY, ['norder'=>new DBNumber($this->norder)], 'id='. $upper->id);
                    $this->database->update(DB_TABLE_MAINCATEGORY, ['norder'=>new DBNumber($upper->norder)], 'id='. $this->id);
                    $this->norder = $upper->norder;
                }
                
                return true;
            }
            return false;
        }
        
        public function moveDown(){
            $rows = $this->database->select('*')->from(DB_TABLE_MAINCATEGORY)->orderby('norder')->asc()->lock();
            
            $below = null;
            
            if(count($rows) && $this->norder < $rows[count($rows) - 1]->norder){
                foreach($rows as $row){
                    if($row->norder > $this->norder){
                        $below = $row;
                        break;
                    }
                }
                
                if($below != null){
                    #trao doi 2 phan tu nay
                    $this->database->update(DB_TABLE_MAINCATEGORY, ['norder'=>new DBNumber(10000)], 'id='. $this->id);
                    $this->database->update(DB_TABLE_MAINCATEGORY, ['norder'=>new DBNumber($this->norder)], 'id='. $below->id);
                    $this->database->update(DB_TABLE_MAINCATEGORY, ['norder'=>new DBNumber($below->norder)], 'id='. $this->id);
                    $this->norder = $below->norder;
                }
                
                return true;
            }
            return false;
        }
    }