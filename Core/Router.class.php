<?php
    namespace Core;
    class Router{
        private $routeTable;
        private $path;
        private $query;
        private $params = [];
        public function __construct($querystring){
            $component = parse_url($querystring);
            $path = isset($component['path']) ? rtrim($component['path'], '/') : '';
            $this->path = str_replace('k3e_route=', '', $path);
            $this->query = isset($component['query']) ? $component['query'] : '';
        }
        
        public function mapRoute($pattern, $defaultParams = []){
            $pattern = preg_replace('/\//', '\/', $pattern);
            $pattern = preg_replace('/\{(\w+)\}/', '(?<$1>\w+)', $pattern);
            $pattern = preg_replace('/\{(\w+):"(.+)"\}/', '(?<$1>$2)', $pattern);
            $this->routeTable[] = ['pattern' => $pattern, 'params' => $defaultParams];
        }
        
        public function match(){
            $this->params = [];
            
            if(is_array($this->routeTable)){
                $total_route = count($this->routeTable);
                for($i=0; $i<$total_route; $i++){
                    if(preg_match('/^' . $this->routeTable[$i]['pattern'] . '$/', $this->path, $params)){
                        $this->params = array_merge($this->params, $this->routeTable[$i]['params']);
                        foreach($params as $key => $value){
                            if(is_string($key)){
                                $this->params[$key] = $value;
                            }
                        }
                        return true;
                    }
                }
            }
            return false;
        }
        
        public function getParams(){
            return $this->params;
        }
        
        public function dispatch(){
            $controllername = isset($this->params['controller']) ? $this->params['controller'] : '';
            $actionname = isset($this->params['action']) ? $this->params['action'] : '';
            
            if(!empty($controllername) && !empty($actionname)){
                $controllername = CONTROLLER_NS . '\\' . $controllername . 'Controller';
                if(class_exists($controllername)){
                    $controller = new $controllername();
                    if(method_exists($controller, $actionname)){
                        $controller->$actionname();
                    }else{
                        header('HTTP/1.1 404 Not Found');
                        echo 'Action not found';
                        exit;
                    }
                }else{
                    header('HTTP/1.1 404 Not Found');
                    echo 'Controller not found';
                    exit;
                }
            }else{
                die('Controller/Action not exists');
            }
        }
        
        public function __toString(){
            echo '<div style="white-space: pre-wrap">';
            print_r($this);
            echo '</div>';
            return '';
        }
    }