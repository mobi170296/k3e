<?php
    namespace App\Controllers;
    
    class UserController extends \Core\Controller{
        public function __init(){
            $this->dbcon = new \Library\MySQLUtility($this->config['db']['host'], $this->config['db']['username'], $this->config['db']['password'], $this->config['db']['dbname']);
            $this->View->dbcon = $this->dbcon;
        }
        public function Index(){
            return $this->View->RenderTemplate();
        }
        public function Register($action, \App\Models\UserModel $user){
            $this->View->ViewData['action'] = $action;
            if($action != null){
                try{
                    $user->register();
                    $_SESSION['username'] = $user->username;
                    $_SESSION['password'] = $user->password;
                    return $this->redirectToAction('Home', 'Index', null);
                } catch (\App\Exception\InputException $ie) {
                    $this->View->ViewData['model'] = $user;
                    $this->View->ViewData['error'] = $ie;
                    return $this->View->RenderTemplate();
                } catch(\App\Exception\DBException $de){
                    $this->View->ViewData['model'] = $user;
                    $this->View->ViewData['error'] = $de;
                    return $this->View->RenderTemplate();
                }
            }else{
                return $this->View->RenderTemplate();
            }
        }
        public function Login($username, $password){
            return $this->View->RenderTemplate();
        }
        public function Logout(){
            
        }
        public function Info(){
            
        }
    }