<?php
    namespace Library;
    
    class DBNumber implements DBDataType{
        public $number;
        
        public function __construct($number){
            $this->number = $number;
        }
        public function toValue(){
            return $this->number;
        }
        public function __toString(){
            return $this->number;
        }
    }
