<?php 
    namespace Library\Image;
    
    class ImageResizerException extends \Exception{
        public function __construct($msg, $code) {
            parent::__construct($msg, $code);
        }
    }