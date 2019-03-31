<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\MainCategoryModel;
    
    class LayoutController extends Controller{
        public function __init(){
            $this->__init_db_authenticate();
        }
        public function Header(){
            $this->View->ViewData['user'] = $this->user;
            return $this->View->RenderPartial();
        }
        public function ControlBar(){
            $this->View->ViewData['user'] = $this->user;
            $this->View->ViewData['maincategorylist'] = [];
            $result = $this->dbcon->select('id')->from('maincategory')->execute();
            while($row = $result->fetch_assoc()){
                $mcate = new MainCategoryModel($this->dbcon);
                $mcate->id = $row['id'];
                $mcate->loadFromDB();
                $this->View->ViewData['maincategorylist'][] = $mcate;
            }
            return $this->View->RenderPartial();
        }
    }