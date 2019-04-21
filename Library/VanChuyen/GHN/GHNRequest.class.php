<?php
    namespace Library\VanChuyen\GHN;
    use Library\ClientRequest\ClientRequest;
    use Library\ClientRequest\ClientRequestException;
    
    class GHNRequest{
        const token = 'TokenStaging';
        const email = 'linh17021996@gmail.com';
        const password = 'Mg01669334569@';
        
        public $clientId, $clientName, $token;
        public $request;
        
        public function __construct(){
            $this->token = self::token;
            $this->request = new ClientRequest('https://apiv3-test.ghn.vn/api/v1/apiv3/SignIn');
            $this->request->setOption(CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json']);
            $this->request->setOption(CURLOPT_HEADER, false);
            $this->request->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->request->setOption(CURLOPT_POST, true);
//            $params = new \stdClass();
//            $params->token = self::token;
//            $params->Email = self::email;
//            $params->Password = self::password;
//            $this->request->setOption(CURLOPT_POSTFIELDS, json_encode($params));
//            try{
//                $clientinfo = json_decode($this->request->execute());
//                if($clientinfo->code===1){
//                    $this->clientId = $clientinfo->data->ClientID;
//                    $this->clientName = $clientinfo->data->ClientName;
//                    $this->token = $clientinfo->data->Token;
//                }else{
//                    throw new GHNException($clientinfo->msg, $clientinfo->code);
//                }
//            } catch (ClientRequestException $ex) {
//                throw new GHNException($ex->getMessage(), $ex->getCode());
//            }
        }
        
        public function signin(){
            
        }
        
        public function getServices(GHNServiceParameter $param){
            $param->token = self::token;
            $requestjson = json_encode($param);
            $this->request->setOption(CURLOPT_URL, 'https://apiv3-test.ghn.vn/api/v1/apiv3/FindAvailableServices');
            $this->request->setOption(CURLOPT_POSTFIELDS, $requestjson);
            try{
                $result = json_decode($this->request->execute());
                if(json_last_error()){
                    throw new GHNException(json_last_error_msg(), json_last_error());
                }
                if($result->code===0){
                    throw new GHNException($result->msg, $result->code);
                }
                
                return $result->data;
            } catch (ClientRequestException $ex) {
                throw GHNException($ex->getMessage(), $ex->getCode());
            }
        }
        
        public function calculateFee(GHNFeeParameter $feeparameter){
            $feeparameter->token = self::token;
            $requestjson = json_encode($feeparameter);
            $this->request->setOption(CURLOPT_URL, 'https://apiv3-test.ghn.vn/api/v1/apiv3/CalculateFee');
            $this->request->setOption(CURLOPT_POSTFIELDS, $requestjson);
            
            try{
                $result = json_decode($this->request->execute());
                if(json_last_error()){
                    throw new GHNException(json_last_error_msg(), json_last_error());
                }
                if($result->code===0){
                    throw new GHNException($result->msg, $result->code);
                }
                
                return $result->data;
            } catch (ClientRequestException $ex) {
                throw GHNException($ex->getMessage(), $ex->getCode());
            }
        }
        
        public function createOrder(GHNCreateOrderParameter $createorderparameter){
            $createorderparameter->token = self::token;
            $createorderparameter->NoteCode = GHNCreateOrderParameter::NOTE_KHONGCHOXEMHANG;
            $createorderparameter->PaymentTypeID = GHNCreateOrderParameter::PAYMENT_SHOP;
            
            $requestjson = json_encode($createorderparameter);
            
            $this->request->setOption(CURLOPT_URL, 'https://apiv3-test.ghn.vn/api/v1/apiv3/CreateOrder');
            $this->request->setOption(CURLOPT_POSTFIELDS, $requestjson);
            
            try{
                $result = json_decode($this->request->execute());
                if(json_last_error()){
                    throw new GHNException(json_last_error_msg(), json_last_error());
                }else{
                    if($result->code === 0){
                        throw new GHNException($result->msg, $result->code);
                    }
                }
                
                return $result->data;
            } catch (ClientRequestException $ex) {
                throw new GHNException($ex->getMessage(), $ex->getCode());
            }
        }
    }