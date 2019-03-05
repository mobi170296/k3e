<?php
    namespace App\Models;
    
    class Student extends \Core\Model{
        public $name, $age;
        
        public function __construct($name = '', $age = ''){
            
        }
        
        public function __set($name, $value){
            $this->$name = $value;
        }
        
        public function __get($name){
            return $this->$name;
        }
    }