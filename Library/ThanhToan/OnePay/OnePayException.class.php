<?php
    namespace Library\ThanhToan\OnePay;
    
    class OnePayException extends \Exception{
        public function __construct($message, $code){
            parent::__construct($message, $code);
        }
    }