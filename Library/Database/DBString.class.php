<?php
    namespace Library\Database;
    
    class DBString implements DBDataType{
        public $string;
        public function __construct($string){
            $this->string = $string;
        }
        public function toValue(){
            return "'{$this->string}'";
        }
        public function __toString(){
            return "'{$this->string}'";
        }
    }