<?php
    namespace Core;
    
    class Model{
        public $_errorsmap = [];
        public function AddErrorMessage($name, $message){
            $this->_errorsmap[$name] = $message;
            return $this;
        }
        public function GetErrorMessage($name){
            return isset($this->_errorsmap[$name]) ? $this->_errorsmap : null;
        }
        public function GetErrorsMap(){
            return $this->_errorsmap;
        }
                
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