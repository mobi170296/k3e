<?php
    namespace Library\VanChuyen\GHN;
    
    class GHNServiceResult{
        public $ExpectedDeliveryTime, $Extras, $Name, $ServiceFee, $ServiceID;
        public function __construct($edt, $es, $n, $sf, $sid){
            $this->ExpectedDeliveryTime = $edt;
            $this->Extras = $es;
            $this->Name = $n;
            $this->ServiceFee = $sf;
            $this->ServiceID = $sid;
        }
    }