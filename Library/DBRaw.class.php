<?php
    namespace Library;
    class DBRaw implements DBDataType{
        public $raw;
        public function __construct($raw){
            $this->raw = $raw;
        }
        public function toValue(){
            return $this->raw;
        }
    }