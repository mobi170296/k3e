<?php
    namespace App\Controllers;
    
    use Core\Controller;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\ProductModel;
    use App\Models\MainCategoryList;
    
    
    class SearchController extends Controller{
        public function Product($keyword){
            $this->View->Data->keyword = $keyword;
            
            try{
                $database = new Database();
                
                $maincategorylist = new MainCategoryList($database);
                $this->View->Data->maincategorylist = $maincategorylist->getAll();
                return $this->View->RenderTemplate();
            }catch(DBException $ex){
                $this->View->Data->ErrorMessage = 'DBERR';
                return $this->View->RenderTemplate('_error');
            }
        }
    }