<?php
    namespace App\Models;
    use \App\Exception\InputException;
    use \Library\DBString;
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
            $errors = [];
            if(mb_strlen($this->name)==0 || mb_strlen($this->name)>200){
                $errors['name'] = 'Độ dài tên danh mục không hợp lệ!';
            }
            if(mb_strlen($this->link)>1024){
                $errors['link'] = 'Độ dài liên kết không được vượt quá 1024';
            }
            if(count($errors)){
                throw new InputException($errors);
            }
            $this->dbcon->insert(DB_TABLE_MAINCATEGORY, ['name'=>new DBString($this->name), 'link'=>new DBString($this->link)]);
            if($this->dbcon->errno()){
                throw new DBException($this->dbcon->error());
            }
        }
        
        public function delete(){
            
        }
        
        public function update($mc){
            
        }
    }