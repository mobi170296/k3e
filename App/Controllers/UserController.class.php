<?php
    namespace App\Controllers;
    
    class UserController extends \Core\Controller{
        public function Index(){
            return $this->View->RenderTemplate();
        }
        public function Register(\App\Models\UserModel $user){
            #var_dump($user);
            return $this->View->RenderTemplate();
        }
        public function Login($username, $password){
            $this->View->ViewData['username'] = $username;
            $this->View->ViewData['password'] = $password;
            return $this->View->RenderTemplate();
        }
        public function Logout(){
            
        }
        public function Info(){
            
        }
    }