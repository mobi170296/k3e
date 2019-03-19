<?php
    namespace App\Models;
    use \App\Exception\InputException;
    use \Library\DBString;
    use \Library\DBNumber;
    use \App\Exception\DBException;
    
    class MainCategoryModel extends \Core\Model{
        public $id, $name, $link;
        public $subcategory = [];
        
        public function loadFromDB(){
            if($this->id != null){
                $result = $this->dbcon->select('*')->from(DB_TABLE_MAINCATEGORY)->where('id = ' . $this->id)->limit(1)->execute();
                if($result->num_rows){
                    $row = $result->fetch_assoc();
                    $this->id = $row['id'];
                    $this->link = $row['link'];
                    $this->name = $row['name'];
                    $result = $this->dbcon->select('*')->from(DB_TABLE_SUBCATEGORY)->where('maincategory_id=' . $this->id)->execute();
                    while($row = $result->fetch_assoc()){
                        $subcategory = new SubCategoryModel($this->dbcon);
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
            $this->dbcon->insert(DB_TABLE_MAINCATEGORY, ['name'=>new DBString($this->name), 'link'=>new DBString($this->link)]);
            if($this->dbcon->errno()){
                throw new DBException($this->dbcon->error());
            }
        }
        
        public function delete(){
            if($this->loadFromDB()){
                $this->dbcon->delete(DB_TABLE_MAINCATEGORY, 'id='. new DBNumber($this->id));
                if($this->dbcon->errno()){
                    throw new DBException($this->dbcon->error());
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
            $this->dbcon->update(DB_TABLE_MAINCATEGORY, ['name'=>new DBString($this->name), 'link'=>new DBString($this->link)], 'id=' . new \Library\DBNumber($this->id));
            if($this->dbcon->errno()){
                throw new DBException($this->dbcon->error());
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