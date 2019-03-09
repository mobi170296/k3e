<?php
    namespace App\Models;
    
    class MainCategoryModel extends \Core\Model{
        public $id, $name, $link;
        public $subcategory = [];
        
        public function loadFromDB(){
            if($this->id != null){
                $result = $this->dbcon->select('*')->from('maincategory')->where('id = ' . $this->id)->limit(1)->execute();
                if($result->num_rows){
                    $row = $result->fetch_assoc();
                    $this->id = $row['id'];
                    $this->link = $row['link'];
                    $this->name = $row['name'];
                    $result = $this->dbcon->select('*')->from('subcategory')->where('maincategory_id=' . $this->id)->execute();
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
    }