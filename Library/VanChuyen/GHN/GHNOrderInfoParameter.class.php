<?php
    namespace Library\VanChuyen\GHN;
    
    class GHNOrderInfoParameter{
        public $OrderCode;
        public function __construct($ordercode){
            $this->OrderCode = $ordercode;
        }
    }