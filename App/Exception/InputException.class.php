<?php
    namespace App\Exception;
    
    class InputException extends \Exception{
        public $errors = [];
        public function __construct($errors, $message = null, $code = null){
            parent::__construct($message, $code);
            $this->errors = $errors;
        }
        public function getErrorsLength(){
            return count($this->errors);
        }
        public function getErrorMessage($n){
            return $this->errors[$n];
        }
        public function getErrorsMap(){
            return $this->errors;
        }
    }
    