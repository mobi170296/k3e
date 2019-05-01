<?php
    namespace Library\ThanhToan\OnePay;
    
    class PaymentResponseResult{
        public $Command, $Locale, $CurrencyCode, $MerchTxnRef, $Merchant, $OrderInfo, $Amount, $TxnResponseCode, $TransactionNo, $Message, $AdditionData, $SecureHash;
        
        public function __construct($command, $locale, $currencycode, $merchtxnref, $merchant, $orderinfo, $amount, $txnresponsecode, $transactionno, $message, $additiondata, $securehash){
            $this->Command = $command;
            $this->Locale = $locale;
            $this->CurrencyCode = $currencycode;
            $this->MerchTxnRef = $merchtxnref;
            $this->Merchant = $merchant;
            $this->OrderInfo = $orderinfo;
            $this->Amount = $amount;
            $this->TxnResponseCode = $txnresponsecode;
            $this->TransactionNo = $transactionno;
            $this->Message = $message;
            $this->AdditionData = $additiondata;
            $this->SecureHash = $securehash;
        }
    }