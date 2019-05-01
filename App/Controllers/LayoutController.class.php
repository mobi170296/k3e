<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\MainCategoryList;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    
    class LayoutController extends Controller{
        public function Header(){
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                $this->View->Data->user = $user;
            }catch(DBException $e){
                return $this->View->RenderContent('Cannot load Header of Layout ' . $e->getMessage());
            }catch(AuthenticateException $e){
                $this->View->Data->user = null;
            }
            return $this->View->RenderPartial();
        }
        public function ControlBar(){
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                $this->View->Data->user = $user;
                $this->View->Data->maincategorylist = (new MainCategoryList($database))->getAll();
                foreach($this->View->Data->maincategorylist as $maincategory){
                    $maincategory->loadSubCategories();
                }
            } catch (AuthenticateException $ex) {
                $this->View->Data->user = null;
            } catch(DBException $e){
                return $this->View->RenderContent('Cannot load ControlBar of layout ' . $e->getMessage());
            }
            return $this->View->RenderPartial();
        }
    }