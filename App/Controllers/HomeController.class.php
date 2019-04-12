<?php
    namespace App\Controllers;
    use Core\Controller;
    
    class HomeController extends Controller{
        public function Index($p = 1){
            $this->View->TemplateData->pagination = new \App\Models\Pagination($p, 5, ['query' => 'linh']);
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