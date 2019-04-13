<?php
    namespace Core;
    
    class Controller{
        public $View;
        public $controller, $action;
        public $config;
        public $request;
        public $get;
        public $files;
        public $post;
        public $method;
        public $rawbody;
        
        public function __construct($controller, $action){
            $this->get = new \stdClass();
            $this->post = new \stdClass();
            $this->files = new \stdClass();
            $this->request = new \stdClass();
            $this->method = $_SERVER['REQUEST_METHOD'];
            global $k3_config;
            $this->config = $k3_config;
            $this->View = new View($controller, $action);
            $this->controller = $controller;
            $this->action = $action;
            $this->__init();
        }
        protected function __init(){
            
        }
        
        public function isPOST(){
            return $this->method === 'POST';
        }
        
        public function isGET(){
            return $this->method === 'GET';
        }
        
        public function isPUT(){
            return $this->method === 'PUT';
        }
        
        public function isHEAD(){
            return $this->method === 'HEAD';
        }
        
        public function isDELETE(){
            return $this->method === 'DELETE';
        }
        
        public function isCONNECT(){
            return $this->method === 'CONNECT';
        }
        
        public function isOPTIONS(){
            return $this->method === 'OPTIONS';
        }
        
        public function isTRACE(){
            return $this->method === 'TRACE';
        }
        
        protected function redirectToAction($action, $controller, $params=null){
            $querystring = '';
            if($params!=null){
                foreach($params as $k => $v){
                    $p[] = $k . '=' . urlencode($v);
                }
                $querystring = implode($p, '&');
            }
            header('location: /' . $controller . '/' . $action . (!empty($querystring)?'?' . $querystring : ''));
            exit;
        }
    }