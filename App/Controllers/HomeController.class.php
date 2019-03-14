<?php
    namespace App\Controllers;
    
    use App\Models\MainCategoryModel;
    use Library\MySQLUtility;
    
    class HomeController extends \Core\Controller{
        protected function __init(){
            $this->dbcon = new MySQLUtility($this->config['db']['host'], $this->config['db']['username'], $this->config['db']['password'], $this->config['db']['dbname']);
            if($this->dbcon->connect_errno()){
                echo 'Lỗi Database: <b style="color:red">' . $this->dbcon->connect_error() .'</b>';
                exit;
            }
            $this->View->dbcon = $this->dbcon;
        }
        public function Index($id, \App\Models\UserModel $userinfo){
            
            return $this->View->RenderTemplate();
        }
        public function About(){
            $this->View->ViewData['title'] = 'About';
            return $this->View->RenderTemplate();
        }
        public function Contact(){
            $db = new MySQLUtility($this->config['db']['host'], $this->config['db']['username'], $this->config['db']['password'], $this->config['db']['dbname']);
            $result = $db->select('first_name, last_name')->from('employees')->limit(10)->execute();
            
            while($row = $result->fetch_assoc()){
                echo '<div style="white-space:pre-wrap">';
                print_r($row);
                echo '</div>' .PHP_EOL;
            }
        }
        public function Test(){
            $categorylist = [];
            $result = $this->dbcon->select('*')->from('maincategory')->execute();
            while($row = $result->fetch_assoc()){
                $mcate = new MainCategoryModel($this->dbcon);
                $mcate->id = $row['id'];
                $mcate->loadFromDB();
                $categorylist[] = $mcate;
            }
            $this->View->ViewData['categorylist'] = $categorylist;
            return $this->View->RenderTemplate();
        }
    }