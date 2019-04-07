<?php
    namespace App\Exception;
    
    class AuthenticationException extends \Exception{
        public function __construct($message, $code){
            parent::__construct($message, $code);
        }
        public function __toString(){
            return $this->getMessage();
        }
    }