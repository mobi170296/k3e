<?php
    namespace App\Models;
    use App\Exception\InputException;
    use App\Exception\DBException;
    use Library\DBString;
    use Library\DBNumber;
    
    class SubCategoryModel extends \Core\Model{
        #name:
        public $id, $name, $link, $maincategory;
        
        public function checkValid($subcategory){
            $errors = [];
            if(mb_strlen($subcategory->name)==0||mb_strlen($subcategory->name)>200){
                $errors['name'] = 'Tên danh mục phụ có độ dài không hợp lệ';
            }
            if(mb_strlen($subcategory->link)>1024){
                $errors['link'] = 'Liên kết danh mục phụ có độ dài tối đa cho phép là 1024';
            }
            return $errors;
        }
        
        public function loadFromDB(){
            $result = $this->dbcon->select('*')->from(DB_TABLE_SUBCATEGORY)->where('id='.$this->id);
            if(!$this->dbcon->errno() && $result->num_rows){
                $row = $result->fetch_assoc();
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->link = $row['link'];
                $this->maincategory = new MainCategoryModel($this->dbcon);
                $this->maincategory->id = $row['maincategory_id'];
                return $this->maincategory->loadFromDB();
            }else{
                return false;
            }
        }
        
        public function add(){
            $errors = $this->checkValid($this);
            if(count($errors)){
                throw new InputException($errors);
            }
            $this->dbcon->insert(DB_TABLE_SUBCATEGORY, ['name'=>new DBString($this->name), 'link'=>new DBString($this->name)]);
            if($this->dbcon->errno()){
                throw new DBException($this->dbcon->error());
            }
        }
        
        public function update($subcate){
            $errors = $this->checkValid($subcate);
            if(count($errors)){
                throw new InputException($errors);
            }
            $this->name = $subcate->name;
            $this->link = $subcate->link;
            $this->dbcon->update(DB_TABLE_SUBCATEGORY, ['name'=>new DBString($this->name), 'link'=>new DBString($this->link)], 'id='.new DBNumber($this->id));
            if($this->dbcon->errno()){
                throw new DBException($this->dbcon->error());
            }
        }
        
        public function remove(){
            if($this->loadFromDB()){
                $this->dbcon->delete(DB_TABLE_SUBCATEGORY, 'id='.new DBNumber($this->id));
                if($this->dbcon->errno()){
                    throw new DBException($this->dbcon->error());
                }
            }else{
                throw new InputException(['id'=>'ID danh mục phụ không tồn tại!']);
            }
        }
    }