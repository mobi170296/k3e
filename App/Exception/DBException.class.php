<?php
    namespace App\Exception;
    class DBException extends \Exception{
        public function __construct($message, $code){
            parent::__construct($message, $code);
        }
        public function __toString(){
            return '<div>'.$this->message.'</div>';
        }
    }