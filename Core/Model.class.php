<?php
    namespace Core;
    
    class Model{
        public $_errorsmap = [];
        public $database;
        
        public function __construct($d){
            $this->database = $d;
            $this->_errorsmap = [];
            $this->__init();
        }
        
        public function setDatabase($d){
            $this->database = $d;
            return $this;
        }
        
        public function addErrorMessage($name, $message){
            $this->_errorsmap[$name] = $message;
            return $this;
        }
        public function getErrorMessage($name){
            return isset($this->_errorsmap[$name]) ? $this->_errorsmap : null;
        }
        public function getErrorsMap(){
            return $this->_errorsmap;
        }
        
        public function getErrorsLength(){
            return count($this->_errorsmap);
        }
        
        public function isValid(){
            return count($this->_errorsmap) === 0;
        }
        
        protected function __init(){
            
        }
        
        public function __set($name, $value) {
            $this->$name = $value;
        }
        
        public function __get($name) {
            return $this->$name;
        }
        
    }