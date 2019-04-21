<?php
    namespace Library\VanChuyen\GHN;
    
    class GHNException extends \Exception{
        public function __construct($message, $code) {
            parent::__construct($message, $code);
        }
    }