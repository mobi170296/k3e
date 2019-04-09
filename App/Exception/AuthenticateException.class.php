<?php

    namespace App\Exception;
    
    class AuthenticateException extends \Exception{
        public function __construct($message, $code){
            parent::__construct($message, $code);
        }
    }
