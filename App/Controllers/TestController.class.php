<?php

    namespace App\Controllers;
    use Core\Controller;
    class TestController extends Controller{
        public function Index($name, $id = 1){
            return $this->View->RenderContent("id = $id; name = $name");
        }
    }
