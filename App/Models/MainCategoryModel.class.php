<?php
    namespace App\Models;
    
    class MainCategoryModel extends \Core\Model{
        public $id, $name, $link;
        public $subcategory = [];
        
        public function __construct(){
            $this->id = $this->name = $this->link = null;
        }
        
        public function loadFromDB(\Library\MySQLUtility $db){
            if($this->id != null){
                $result = $db->select('*')->from('maincategory')->where('id = ' . $this->id)->limit(1)->execute();
                echo $db->dbcon->error;
                if($result->num_rows){
                    $row = $result->fetch_assoc();
                    $this->id = $row['id'];
                    $this->link = $row['link'];
                    $this->name = $row['name'];
                    $result = $db->select('*')->from('subcategory')->where('maincategory_id=' . $this->id)->execute();
                    while($row = $result->fetch_assoc()){
                        $subcategory = new SubCategoryModel();
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