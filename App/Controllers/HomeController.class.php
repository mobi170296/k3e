<?php
    namespace App\Controllers;
    
    class HomeController extends \Core\Controller{
        public function Index(){
            echo 'Index action of HomeController';
        }
        public function About(){
            echo 'About action of HomeController';
        }
        public function Contact(){
            echo 'Contact action of HomeController';
        }
    }