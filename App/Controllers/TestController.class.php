<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\UserModel;
    
    class TestController extends Controller{
        public function Index(UserModel $user){
            return $this->View->RenderContent("" . $this->rawbody);
        }
        
        public function Test(){
            return $this->View->RenderContent('' . session_id());
        }
    }