<?php
    namespace Core;
    
    class Controller{
        public $View;
        public $controller, $action;
        
        public function __construct($controller, $action){
            $this->View = new View($controller, $action);
            $this->controller = $controller;
            $this->action = $action;
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