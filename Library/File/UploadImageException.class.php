<?php
    namespace Library\File;
    
    class UploadImageException extends \Exception{
        public $errors = [];
        public function __construct($errs){
            parent::__construct();
            $this->errors = $errs;
        }
        public function getErrorsArray(){
            return $this->errors;
        }
        public function __toString(){
            $result = '';
            foreach($this->errors as $e){
                $result .= $e;
            }
            return $result;
        }
    }
