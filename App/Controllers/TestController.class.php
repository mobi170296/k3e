<?php
    namespace App\Controllers;
    use Core\Controller;
    
    class TestController extends Controller{
        public function Index($category){
            if($category === null){
                return $this->View->RenderContent("CATEGORY KHONG CO!");
            }else{
                return $this->View->RenderContent("CATEGORY = " . $category);
            }
        }
    }