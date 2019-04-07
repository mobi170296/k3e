<?php
    namespace App\Exception;
    
    class InputException extends \Exception{
        public $errors = [];
        public function __construct($errors){
            parent::__construct();
            $this->errors = $errors;
        }
        public function getLength(){
            return count($this->errors);
        }
        public function getErrorMessage($n){
            return $this->errors[$n];
        }
        public function getErrorsMap(){
            return $this->errors;
        }
    }
    