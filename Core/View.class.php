<?php
    namespace Core;
    use Core\Router;
    
    class View{
        public $controller, $action;
        public $TemplateData;
        public $ViewData;
        public $bodyContent;
        public $fileSection = [];
        public $contentSection = [];
        public $layout = null;
        public $config;
        public $dbcon;
        public $user;
        
        public function __construct($controller, $action){
            $this->controller = str_replace('\\', DS, $controller);
            $this->action = $action;
            $this->ViewData = [];
            global $k3_config;
            $this->config = $k3_config;
            require TEMPLATE_DIR . DS . '_ViewStart.phphtml';
        }
        
        public function render(){
            if($this->layout != null){
                require_once k3_ROOT . str_replace('/', DS, $this->layout);
            }else{
                $this->renderBody();
            }
        }
        
        public function renderSection($name, $file = false){
            if($file){
                if(isset($this->fileSection[$name])){
                    #require_once k3_ROOT . DS . 'App' . DS . 'Template' . DS . $this->fileSection[$name];
                    require k3_ROOT . $this->fileSection[$name];
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
            $this->layout = null;
            $this->bodyContent = $content;
            return $this;
        }
        
        public function renderBody(){
            echo $this->bodyContent;
        }
        
        public function RenderTemplate($action = null, $controller = null){
            $c = $controller == null ? $this->controller : $controller;
            $a = $action == null ? $this->action : $action;
            
            ob_start();
            
            require VIEW_DIR . DS . $c . DS . $a . '.phphtml';
            
            $this->bodyContent = ob_get_contents();
            
            ob_end_clean();
            return $this;
        }

        public function RenderPartial($action = null, $controller = null){
            $this->layout = null;
            ob_start();
            if($controller == null || $action == null){
                    require VIEW_DIR . DS . $this->controller . DS . $this->action . '.phphtml';
            }else{
                    require VIEW_DIR . DS . $controller . DS . $action . '.phphtml';
            }
            $this->bodyContent = ob_get_contents();
            ob_end_clean();
            return $this;
        }
        public function RenderJson($obj){
            $this->layout = null;
            $this->bodyContent = json_encode($obj, JSON_UNESCAPED_UNICODE);
            return $this;
        }
        
        #Render Action
        public function Action($action, $controller = null){
            $childrouter = new Router('');
            $childrouter->setAction($action);
            $childrouter->setController($controller == null ? $this->controller : $controller);
            
            ob_start();
            $view = $childrouter->dispatch();
            if($view){
                $view->render();
            }
            $resultofchildroute = ob_get_contents();
            ob_end_clean();
            
            echo $resultofchildroute;
        }
    }