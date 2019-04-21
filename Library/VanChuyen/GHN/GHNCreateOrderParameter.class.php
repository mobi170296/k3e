<?php
    namespace Library\VanChuyen\GHN;
    
    class GHNCreateOrderParameter{
        const PAYMENT_SHOP = 1, PAYMENT_BUYER = 2, PAYMENT_GHNWALLET = 4, PAYMENT_CREDIT = 5;
        const NOTE_CHOTHUHANG = 'CHOTHUHANG', NOTE_CHOXEMHANGKHONGTHU = 'CHOXEMHANGKHONGTHU', NOTE_KHONGCHOXEMHANG = 'KHONGCHOXEMHANG';
        public $token;
        public $PaymentTypeID, $FromDistrictID, $ToDistrictID; #use PAYMENT
        public $ClientContactName, $ClientContactPhone, $ClientAddress;
        public $CustomerName, $CustomerPhone, $ShippingAddress;
        public $NoteCode; #use NOTE CODE
        public $ServiceID;
        public $Weight, $Length, $Width, $Height;
        public $InsuranceFee;
        public $CoDAmount;
        public $Note;
        public $Content;
        
        public function __construct($fromd, $tod, $clientName, $clientPhone, $clientAddress, $customerName, $customerPhone, $customerAddress, $serviceId, $weight, $length, $width, $height, $insuranceFee, $codAmount, $note, $content){
            $this->FromDistrictID = $fromd;
            $this->ToDistrictID = $tod;
            $this->ClientContactName = $clientName;
            $this->ClientContactPhone = $clientPhone;
            $this->ClientAddress = $clientAddress;
            $this->CustomerName = $customerName;
            $this->CustomerPhone = $customerPhone;
            $this->ShippingAddress = $customerAddress;
            $this->ServiceID = $serviceId;
            $this->Weight = $weight;
            $this->Length = $length;
            $this->Width = $width;
            $this->Height = $height;
            $this->InsuranceFee = $insuranceFee;
            $this->CoDAmount = $codAmount;
            $this->Note = $note;
            $this->Content = $content;
        }
    }