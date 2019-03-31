<?php
    namespace Core;
    
    class Model{
        public $dbcon;
        public function __construct($dbcon){
            $this->dbcon = $dbcon;
            $this->__init();
        }
        
        protected function __init(){
            
        }
        
        public function setDBCon($dbcon){
            $this->dbcon = $dbcon;
        }
        
        public function __set($name, $value) {
            $this->$name = $value;
        }
        
        public function __get($name) {
            return $this->$name;
        }
    }