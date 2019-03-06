<?php
    namespace App\Controllers;
    use App\Models\Student;
    
    class HomeController extends \Core\Controller{
        public function Index($id, Student $student){
            return $this->View->RenderContent("Content to dump data");
        }
        public function About(){
            $this->View->ViewData['title'] = 'About';
            return $this->View->RenderTemplate();
        }
        public function Contact(){
            echo 'Contact action of HomeController';
        }
    }