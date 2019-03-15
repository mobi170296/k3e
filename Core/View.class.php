<?php
    namespace Core;
    
    class View{
        public $controller, $action;
        public $ViewData;
        public $bodyContent;
        public $fileSection = [];
        public $contentSection = [];
        public $layout = null;
        public $config;
        public $dbcon;
        public $user;
        
        public function __construct($controller, $action){
            $this->controller = $controller;
            $this->action = $action;
            $this->ViewData = [];
            global $k3_config;
            $this->config = $k3_config;
        }
        
        public function render(){
            if($this->layout != null){
                require_once TEMPLATE_DIR . DS . $this->layout;
            }else{
                $this->renderBody();
            }
        }
        
        public function renderSection($name, $file = false){
            if($file){
                if(isset($this->fileSection[$name])){
                    require_once k3_ROOT . DS . 'App' . DS . 'Template' . DS . $this->fileSection[$name];
                }
            }else{
                if (!empty($this->contentSection[$name])) {
                    echo $this->contentSection[$name];
                }
            }
        }
        
        public function addSection($name, $content, $file = false){
            if($file){
                $this->fileSection[$name] = $content;
            }else{
                $this->contentSection[$name] = $content;
            }
        }
        
        public function RenderContent($content){
            $this->bodyContent = $content;
            return $this;
        }
        
        public function renderBody(){
            echo $this->bodyContent;
        }
        
        public function RenderTemplate($controller = null, $action = null){
            ob_start();
            if($controller == null || $action == null){
                require_once VIEW_DIR . DS . $this->controller . DS . $this->action . '.php';
            }else{
                require_once VIEW_DIR . DS . $controller . DS . $action . '.php';
            }
            $this->bodyContent = ob_get_contents();
            ob_end_clean();
            return $this;
        }
    }