<?php 
    namespace Library\VanChuyen\GHN;
    
    class GHNFeeParameter{
        public $token, $FromDistrictID, $ToDistrictID, $ServiceID, $Weight, $Length, $Width, $Height, $OrderCosts = [], $CouponCode, $InsuranceFee;
        
        public function __construct($fromd, $tod, $serviceid, $wei, $len, $wid, $hei, $ins){
            $this->FromDistrictID = $fromd;
            $this->ToDistrictID = $tod;
            $this->ServiceID = $serviceid;
            $this->Weight = $wei;
            $this->Length = $len;
            $this->Width = $wid;
            $this->Height = $hei;
            $this->InsuranceFee = $ins;
        }
    }