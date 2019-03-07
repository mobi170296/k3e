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
            $db = new MySQLUtility('localhost', 'root', 'nguyenthithuyhang', 'employees', 3306);
            $db->insert('user', [['name' => new DBString('Trịnh Văn Linh'), 'money' => new DBNumber(23), 'birthday' => new DBDateTime(17, 02, 1996)],
                ['name' => new DBString('Dương Thúy Oanh'), 'money' => new DBNumber(500000), 'birthday' => new DBDateTime(1, 5, 1997)]]);
        }
    }