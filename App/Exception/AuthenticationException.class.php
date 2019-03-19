<?php
    namespace App\Exception;
    
    class AuthenticationException extends \Exception{
        public function __toString(){
            return '<div>'. $this->message.'</div>';
        }
    }