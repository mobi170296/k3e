<?php
    namespace Core;
    
    class Controller{
        public $View;
        public $controller, $action;
        public $config;
        public $dbcon;
        
        public function __construct($controller, $action){
            global $k3_config;
            $this->config = $k3_config;
            $this->View = new View($controller, $action);
            $this->controller = $controller;
            $this->action = $action;
            $this->__init();
        }
        protected function __init(){
            
        }
        public function redirectToAction($controller, $action, $params){
            foreach($params as $k => $v){
                $p[] = $k . '=' . urlencode($v);
            }
            $querystring = implode($p, '&');
            header('location: /' . $controller . '/' . $action . '?' . $querystring);
            exit;
        }
    }