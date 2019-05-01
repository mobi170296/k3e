<?php
    namespace Library\ThanhToan\OnePay;
    
    class QueryParameter{
        public $MerchTxnRef;
        
        public function __construct($ref){
            $this->MerchTxnRef = $ref;
        }
    }