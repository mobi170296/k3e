<?php
    namespace App\Controllers;
    
    use Core\Controller;
   
    use Library\Database\Database;
    use App\Models\OrderModel;
    use App\Models\GHNTransporterModel;
    use Library\Database\DBException;
    
    class EmulateController extends Controller{
        public function GHN($ghnordercode){
            try{
                
                return $this->View->RenderPartial();
            } catch (DBException $ex) {
                return $this->View->RenderContent('DBERR'); 
            }
        }
    }