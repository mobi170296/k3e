<?php

    namespace Library;
    
    class DBString implements DBDataType{
        public $string;
        public function __construct($string){
            $this->string = $string;
        }
        public function toValue(){
            return "'{$this->string}'";
        }
    }