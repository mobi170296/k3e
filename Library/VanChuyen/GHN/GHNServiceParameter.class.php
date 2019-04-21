<?php 
    namespace Library\VanChuyen\GHN;
    
    class GHNServiceParameter{
        public $token;
        public $FromDistrictID, $ToDistrictID, $Weight, $Length, $Height, $Width;
        
        public function __construct($fromd, $tod, $wei, $len, $wid, $hei){
            $this->FromDistrictID = $fromd;
            $this->ToDistrictID = $tod;
            $this->Weight = $wei;
            $this->Length = $len;
            $this->Height = $hei;
            $this->Width = $wid;
        }
    }