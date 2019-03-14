<?php
    namespace App\Exception;
    
    class InputException extends \Exception{
        public $errors = [];
        public function __construct($errors){
            $this->errors = $errors;
        }
        public function getLength(){
            return count($this->errors);
        }
        public function getError($i){
            return $this->errors[$i];
        }
    }
    