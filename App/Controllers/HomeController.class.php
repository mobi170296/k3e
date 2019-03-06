<?php
    namespace App\Controllers;
    use App\Models\Student;
    
    class HomeController extends \Core\Controller{
        public function Index($id, Student $student){
            $this->redirectToAction("Home", "About", ['username' => 'linh170296', 'password' => '01208663626']);
        }
        public function About(){
            $this->View->ViewData['title'] = 'About';
            return $this->View->RenderTemplate();
        }
        public function Contact(){
            echo 'Contact action of HomeController';
        }
    }