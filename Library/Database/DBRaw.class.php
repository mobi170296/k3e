<?php
    namespace Library\Database;
    
    class DBRaw implements DBDataType{
        public $raw;
        public function __construct($raw){
            $this->raw = $raw;
        }
        public function toValue(){
            return $this->raw;
        }
        public function SqlValue() {
            return $this->raw;
        }
        public function __toString(){
            return $this->raw;
        }
    }