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
            #Convert query string to query array
            parse_str($this->query, $this->query);
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
                        $this->params = array_merge($this->params, $params);
//                        foreach($params as $key => $value){
//                            if(is_string($key)){
//                                $this->params[$key] = $value;
//                            }
//                        }
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
            #fetch $_REQUEST (POST + GET) to $this->params
            $paras = [];
            $paras = array_merge($paras, $this->params);
            unset($paras['controller']);
            unset($paras['action']);
            
            $this->query = array_merge($this->query, $paras);
            $this->query = array_merge($this->query, $_REQUEST);
            
            $args = [];
            
            $controllername = isset($this->params['controller']) ? $this->params['controller'] : '';
            $actionname = isset($this->params['action']) ? $this->params['action'] : '';
            
            if(!empty($controllername) && !empty($actionname)){
                $controllername = CONTROLLER_NS . '\\' . $controllername . 'Controller';
                if(class_exists($controllername)){
                    $controller = new $controllername($this->params['controller'], $this->params['action']);
                    if(method_exists($controller, $actionname)){
                        $method = (new \ReflectionClass($controller))->getMethod($actionname);
                        $methodparameters = $method->getParameters();
                        
                        foreach($methodparameters as $parameter){
                            $parametername = $parameter->getName();
                            if($parameter->getType() != null){
                                #Class Parameter
                                $classname = $parameter->getType()->getName();
                                
                                if(!class_exists($classname)){
                                    $args[$parametername] = null;
                                    break;
                                }
                                
                                $obj = new $classname(null);
                                
                                $rclass = new \ReflectionClass($classname);
                                $properties = $rclass->getProperties();
                                
                                foreach($properties as $property){
                                    $obj->{$property->getName()} = isset($this->query[$property->getName()]) ? $this->query[$property->getName()] : null;
                                }
                                
                                $args[$parametername] = $obj;
                            }else{
                                $args[$parametername] = isset($this->query[$parametername]) ? $this->query[$parametername] : null;
                            }
                        }
                        
                        $view = call_user_func_array([$controller, $actionname], $args);
                        return $view;
                    }else{
                        header('HTTP/1.1 404 Not Found');
                        echo '<b style="color: red">Action not found</b>';
                        exit;
                    }
                }else{
                    header('HTTP/1.1 404 Not Found');
                    echo '<b style="color: red">Controller not found</b>';
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