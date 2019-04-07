<?php
    namespace App\Models;
    use App\Exception\InputException;
    use App\Exception\DBException;
    use Library\Database\DBString;
    use Library\Database\DBNumber;
    
    class MainCategoryModel extends \Core\Model{
        protected $id, $name, $link, $subcategory = [];
        
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

        public function setSubcategory($subcategory) {
            $this->subcategory = $subcategory;
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
        
        public function getSubCategories(){
            return $this->subcategory;
        }
        
        public function loadFromDB(){
            if($this->id != null){
                $result = $this->database->select('*')->from(DB_TABLE_MAINCATEGORY)->where('id = ' . $this->id)->limit(1)->execute();
                if($result->num_rows){
                    $row = $result->fetch_assoc();
                    $this->id = $row['id'];
                    $this->link = $row['link'];
                    $this->name = $row['name'];
                    $result = $this->database->select('*')->from(DB_TABLE_SUBCATEGORY)->where('maincategory_id=' . new DBNumber($this->id))->execute();
                    while($row = $result->fetch_assoc()){
                        $subcategory = new SubCategoryModel($this->database);
                        $subcategory->id = $row['id'];
                        $subcategory->name = $row['name'];
                        $subcategory->link = $row['link'];
                        $subcategory->maincategory = $this;
                        
                        $this->subcategory[] = $subcategory;
                    }
                    $result->free();
                    return true;
                }else{
                    return false;
                }
            }
            return false;
        }
        
        public function add(){
            $errors = $this->checkValid($this);
            if(count($errors)){
                throw new InputException($errors);
            }
            $this->database->insert(DB_TABLE_MAINCATEGORY, ['name'=>new DBString($this->name), 'link'=>new DBString($this->link)]);
            if($this->database->errno()){
                throw new DBException($this->database->error());
            }
        }
        
        public function delete(){
            if($this->loadFromDB()){
                $this->database->delete(DB_TABLE_MAINCATEGORY, 'id='. new DBNumber($this->id));
                if($this->database->errno()){
                    throw new DBException($this->database->error());
                }
            }else{
                throw new InputException(['id'=>'ID danh mục không tồn tại']);
            }
        }
        
        public function update($mc){
            $errors = $this->checkValid($mc);
            if(count($errors)){
                throw new InputException($errors);
            }
            $this->name = $mc->name;
            $this->link = $mc->link;
            $this->database->update(DB_TABLE_MAINCATEGORY, ['name'=>new DBString($this->name), 'link'=>new DBString($this->link)], 'id=' . new DBNumber($this->id));
            if($this->database->errno()){
                throw new DBException($this->database->error());
            }
        }
        
        private function checkValid($m){
            $errors = [];
            if(mb_strlen($m->name)==0 || mb_strlen($m->name)>200){
                $errors['name'] = 'Độ dài tên danh mục không hợp lệ!';
            }
            if(mb_strlen($m->link)>1024){
                $errors['link'] = 'Độ dài liên kết không được vượt quá 1024';
            }
            return $errors;
        }
    }