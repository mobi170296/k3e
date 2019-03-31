<?php
    namespace App\Exception;
    class DBException extends \Exception{
        public function __toString(){
            return '<div>'.$this->message.'</div>';
        }
    }