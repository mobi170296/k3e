<?php
    namespace Library\VanChuyen\GHN;
    
    class GHNFeeResult{
        public $CalculatedFee, $ServiceFee, $CoDFee, $OrderCosts, $DiscountFee, $WeightDimension;
        
        public function __construct($cf, $sf, $codf, $ocs, $df, $wd){
            $this->CalculatedFee = $cf;
            $this->ServiceFee = $sf;
            $this->CoDFee = $codf;
            $this->OrderCosts = $ocs;
            $this->DiscountFee = $df;
            $this->WeightDimension = $wd;
        }
    }