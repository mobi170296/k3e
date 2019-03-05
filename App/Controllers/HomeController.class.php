<?php
    namespace App\Controllers;
    use App\Models\Student;
    
    class HomeController extends \Core\Controller{
        public function Index($id, Student $student){
            print_r($student);
            echo 'Index action of HomeController id = ' . $id;
        }
        public function About(){
            echo 'About action of HomeController';
        }
        public function Contact(){
            echo 'Contact action of HomeController';
        }
    }