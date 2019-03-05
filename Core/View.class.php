<?php
    namespace Core;
    
    abstract class View{
        public $controller, $action;
        public $bodyContent;
        public $fileSection = [];
        public $contentSection = [];
        
        public function __construct($controller, $action){
            $this->controller = $controller;
            $this->action = $action;
        }
        
        public abstract function render();
        
        public function renderSection($name, $file = false){
            if($file){
                include $file;
            }else{
                echo $this->contentSection[$name];
            }
        }
        
        public function addSection($name, $content, $file = false){
            if($file){
                $this->fileSection[$name] = $content;
            }else{
                $this->contentSection[$name] = $content;
            }
        }
    }