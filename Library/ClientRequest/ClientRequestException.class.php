<?php
    namespace Library\ClientRequest;
    
    class ClientRequestException extends \Exception{
        public function __construct($message, $code){
            parent::__construct($message, $code);
        }
    }