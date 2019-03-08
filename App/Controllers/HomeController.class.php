<?php
    namespace App\Controllers;
    use Core\MySQLUtility;
    use Library\DBDateTime;
    use Library\DBDate;
    use Library\DBString;
    use Library\DBNumber;
    
    class HomeController extends \Core\Controller{
        public function Index($id, \App\Models\UserModel $userinfo){
            
            return $this->View->RenderTemplate();
        }
        public function About(){
            $this->View->ViewData['title'] = 'About';
            return $this->View->RenderTemplate();
        }
        public function Contact(){
            $db = new MySQLUtility('localhost', 'root', 'nguyenthithuyhang', 'employees');
            $result = $db->select('first_name, last_name')->from('employees')->limit(10)->execute();
            
            while($row = $result->fetch_assoc()){
                echo '<div style="white-space:pre-wrap">';
                print_r($row);
                echo '</div>' .PHP_EOL;
            }
        }
    }