<?php
    namespace App\Controllers;
    use Core\Controller;
    
    class HomeController extends Controller{
        public function Index(){
            return $this->View->RenderTemplate();
        }
        public function About(){
            $this->View->ViewData['title'] = 'About';
            return $this->View->RenderTemplate();
        }
        public function Info(){
            return $this->View->RenderContent("NOI DUNG OF INFO");
        }
    }