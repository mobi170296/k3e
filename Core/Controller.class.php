<?php
    namespace Core;
    
    class Controller{
        public function redirectToAction($controller, $action, $params){
            foreach($params as $k => $v){
                $p[] = $k . '=' . urlencode($v);
            }
            $querystring = implode($p, '&');
            header('location: /' . $controller . '/' . $action . '?' . $querystring);
            exit;
        }
    }