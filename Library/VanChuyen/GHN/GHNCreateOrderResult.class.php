<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    namespace Library\VanChuyen\GHN;
    
    
    class GHNCreateOrderResult{
        public $OrderID, $PaymentTypeID, $OrderCode, $CurrentStatus, $ExtraFee, $TotalServiceFee, $ExpectedDeliveryTime, $ClientHubID, $SortCode;
        
        public function __construct($oid, $pti, $oc, $cs, $ef, $tsf, $edt, $chi, $sc) {
            $this->OrderID = $oid;
            $this->PaymentTypeID = $pti;
            $this->OrderCode = $oc;
            $this->CurrentStatus = $cs;
            $this->ExtraFee = $ef;
            $this->TotalServiceFee = $tsf;
            $this->ExpectedDeliveryTime = $edt;
            $this->ClientHubID = $chi;
            $this->SortCode = $sc;
        }
    }