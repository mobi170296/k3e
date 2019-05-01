<?php
    namespace Library\ThanhToan\OnePay;
    
    class QueryResult{
        public $Exists, $TxnResponseCode, $Message, $AdditionData, $Amount, $AuthenticationDate, $Command, $CurrencyCode, $Locale, $MerchTxnRef, $Merchant, $OrderInfo, $TransactionNo;
        
        public function __construct($exists, $responsecode, $message, $additiondata, $amount, $authenticationdate, $command, $currencycode, $locale, $merchtxnref, $merchant, $orderinfo, $transactionno){
            $this->Exists = $exists;
            $this->TxnResponseCode = $responsecode;
            $this->Message = $message;
            $this->AdditionData = $additiondata;
            $this->Amount = $amount;
            $this->AuthenticationDate = $authenticationdate;
            $this->Command = $command;
            $this->CurrencyCode = $currencycode;
            $this->Locale = $locale;
            $this->MerchTxnRef = $merchtxnref;
            $this->Merchant = $merchant;
            $this->OrderInfo = $orderinfo;
            $this->TransactionNo = $transactionno;
        }
    }