<?php
    namespace App\Controllers;
    
    class HomeController extends \Core\Controller{
        public function Index($id, \App\Models\UserModel $userinfo){
            
            return $this->View->RenderTemplate();
        }
        public function About(){
            $this->View->ViewData['title'] = 'About';
            return $this->View->RenderTemplate();
        }
        public function Contact(){
            echo 'Contact action of HomeController';
        }
    }