<?php
    namespace Library\ClientRequest;
    
    class ClientRequest{
        public $curl;
        public function __construct($url = null) {
            $this->curl = curl_init($url);
            if($this->curl===false){
                throw new ClientRequestException('Initialize CURL failed!', -1);
            }
        }
        
        public function getErrorCode(){
            return curl_errno($this->curl);
        }
        
        public function getErrorMessage(){
            return curl_error($this->curl);
        }
        
        public function getEscape($str){
            return curl_escape($this->curl, $str);
        }
        
        public function getUnescape($str){
            return curl_unescape($this->curl, $str);
        }
        
        public function execute(){
            $result = curl_exec($this->curl);
            if(curl_errno($this->curl)){
                throw new ClientRequestException(curl_error($this->curl), curl_errno($this->curl));
            }
            return $result;
        }
        
        public function getOption($opt){
            return curl_getinfo($this->curl, $opt);
        }
        
        public function getOptions(){
            return curl_getinfo($this->curl);
        }
        
        public function reset(){
            curl_reset($this->curl);
        }
        
        public function setOption($opt, $value){
            if(curl_setopt($this->curl, $opt, $value)){
                return $this;
            }else{
                throw new ClientRequestException(curl_error($this->curl), curl_errno($this->curl));
            }
        }
        
        public function setOptions($aopt){
            if(curl_setopt_array($this->curl, $aopt)){
                return $this;
            }else{
                throw new ClientRequestException(curl_error($this->error), curl_errno($this->errno));
            }
        }
        
        public function close(){
            curl_close($this->curl);
        }
    }