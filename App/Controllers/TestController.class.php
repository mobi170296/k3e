<?php
    namespace App\Controllers;
    use Core\Controller;
    use App\Models\UserModel;
    class TestController extends Controller{
        public function Index(UserModel $user){
            $this->View->Data->user = $user;
            return $this->View->RenderTemplate();
        }
    }