<?php
    namespace App\Models;
    class UserModel extends \Core\Model{
        public $id;
        public $username;
        public $password;
        public $email;
        public $address;
        public $district_id;
        public $created_date;
        public $locked;
        public $day;
        public $month;
        public $year;
        public $money;
        public $role;
        public $gender;
        
        public function register(){
            
        }
        
        public function login(){
            
        }
    }
