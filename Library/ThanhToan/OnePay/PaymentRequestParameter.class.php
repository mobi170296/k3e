<?php
    namespace Library\ThanhToan\OnePay;
    
    class PaymentRequestParameter{
        public $version, $currency, $command, $accesscode, $merchant, $locale, $returnurl, $merchtxnref, $orderinfo, $amount, $ticketno, $againlink, $title;
        
        public function __construct($orderinfo, $merchtxnref, $amount, $returnurl, $ip, $againlink, $title){
            $this->returnurl = $returnurl;
            $this->merchtxnref = $merchtxnref;
            $this->orderinfo = $orderinfo;
            $this->amount = $amount;
            $this->ticketno = $ip;
            $this->againlink = $againlink;
            $this->title = $title;
        }
    }