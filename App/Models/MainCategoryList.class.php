<?php
    namespace App\Models;
    use App\Models\MainCategoryModel;
    use Core\Model;
    
    class MainCategoryList extends Model{
        public $list;
        
        public function getAll(){
            $this->list = [];
            $rows = $this->database->select('id')->from(DB_TABLE_MAINCATEGORY)->orderby('norder')->execute();
            foreach($rows as $row){
                $m = new MainCategoryModel($this->database);
                $m->id = $row->id;
                if($m->loadData()){
                    $this->list[] = $m;
                }
            }
            return $this->list;
        }
    }