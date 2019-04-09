<?php
    namespace App\Models;
    use Core\Model;
    
    class SubCategoryList extends Model{
        public $list;
        public function getAll(){
            $this->list = [];
            
            $rows = $this->database->select('*')->from(DB_TABLE_SUBCATEGORY)->orderby('norder')->execute();
            foreach($rows as $row){
                $subcategory = new SubCategoryModel($this->database);
                $subcategory->id = $row->id;
                if($subcategory->loadData()){
                    $this->list[] = $subcategory;
                }
            }
            
            return $this->list;
        }
        
        public function getWhere($where){
            $this->list = [];
            $rows = $this->database->select('*')->from(DB_TABLE_SUBCATEGORY)->where($where)->execute();
            foreach($rows as $row){
                $subcategory = new SubCategoryModel($this->database);
                $subcategory->id = $row->id;
                if($subcategory->loadData()){
                    $this->list[] = $subcategory;
                }
            }
            
            return $this->list;
        }
    }