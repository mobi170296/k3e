<?php
    namespace App\Controllers;
    
    class UserController extends \Core\Controller{
        public function __init(){
            $this->dbcon = new \Library\MySQLUtility($this->config['db']['host'], $this->config['db']['username'], $this->config['db']['password'], $this->config['db']['dbname']);
            if($this->dbcon->connect_errno()){
                echo 'Lỗi Database: <b style="color:red">' . $this->dbcon->connect_error() .'</b>';
                exit;
            }
            $this->authenticate();
            $this->View->dbcon = $this->dbcon;
            $this->View->user = $this->user;
        }
        public function Index(){
            return $this->View->RenderTemplate();
        }
        public function Register($action, \App\Models\UserModel $user){
            if($this->user->isLogin()){
                $this->redirectToAction('Home', 'Index', null);
            }
            $user->dbcon = $this->dbcon;
            $this->View->ViewData['action'] = $action;
            if($action != null){
                try{
                    $user->register();
                    $_SESSION['username'] = $user->username;
                    $_SESSION['password'] = $user->password[0];
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
        public function Login($action, $username, $password){
            if($this->user->isLogin()){
                return $this->redirectToAction('Home', 'Index', null);
            }
            if($action!=null){
                $user = new \App\Models\UserModel($this->dbcon);
                $user->username = $username;
                $user->password = $password;
                if($user->login()){
                    $_SESSION['username'] = $username;
                    $_SESSION['password'] = $password;
                    return $this->redirectToAction('Home', 'Index', null);
                }else{
                    $this->View->ViewData['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
                    return $this->View->RenderTemplate();
                }
            }else{
                return $this->View->RenderTemplate();
            }
        }
        public function Info($action, \App\Models\UserModel $user){
            if($this->user->isLogin()){
                if($action != null){
                    try{
                        $this->user->update($user);
                        $this->View->ViewData['user'] = $user;
                        $this->View->ViewData['success'] = 'Bạn đã cập nhật thông tin thành công';
                        return $this->View->RenderTemplate();
                    } catch (\App\Exception\DBException $de) {
                        $this->View->ViewData['user'] = $user;
                        $this->View->ViewData['error'] = $de;
                        return $this->View->RenderTemplate();
                    } catch (\App\Exception\InputException $ie){
                        $this->View->ViewData['user'] = $user;
                        $this->View->ViewData['error'] = $ie;
                        return $this->View->RenderTemplate();
                    }
                }
                return $this->View->RenderTemplate();
            }else{
                $this->redirectToAction('Home', 'Index', null);
            }
        }
        public function Logout(){
            unset($_SESSION['username']);
            unset($_SESSION['password']);
            $this->redirectToAction('Home', 'Index', null);
        }
    }