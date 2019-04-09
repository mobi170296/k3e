<?php
    namespace Core;
    class Router{
        public $routeTable;
        public $path;
        public $query;
        public $params = [];
        public function __construct($querystring){
            #k3e_route={path}&querystring
            parse_str($querystring, $this->query);
            $this->path = isset($this->query['k3e_route']) ? rtrim($this->query['k3e_route'], '/') : '';
            unset($this->query['k3e_route']);
            unset($_GET['k3e_route']);
            unset($_REQUEST['k3e_route']);
        }
        
        public function setController($controller){
            $this->params['controller'] = $controller;
        }
        
        public function setAction($action){
            $this->params['action'] = $action;
        }
        
        public function setQuery($query){
            \array_merge($this->query, $query);
        }
        
        public function mapRoute($pattern, $defaultParams = []){
            $pattern = preg_replace('/\//', '\/', $pattern);
            $pattern = preg_replace('/\{(\w+)\}/', '(?<$1>\w+)', $pattern);
            #$pattern = preg_replace('/\{(\w+):"(.+)"\}/', '(?<$1>$2)', $pattern);
            $pattern = preg_replace('/\{(\w+):"([^"]+)"\}/', '(?<$1>$2)', $pattern);
            $this->routeTable[] = ['pattern' => $pattern, 'params' => $defaultParams];
        }
        
        public function match(){
            $this->params = [];
            $params = null;
            if(is_array($this->routeTable)){
                $total_route = count($this->routeTable);
                for($i=0; $i<$total_route; $i++){
                    if(preg_match('/^' . $this->routeTable[$i]['pattern'] . '$/', $this->path, $params)){
                        if(isset($params['controller'])){
                            $params['controller'] = str_replace('/', '\\', $params['controller']);
                        }
                        #Lay param mac dinh
                        $this->params = array_merge($this->params, $this->routeTable[$i]['params']);
                        #Lay duoc tu path bo di nhung index thua la number
                        foreach($params as $k => $v){
                            if(is_numeric($k)){
                                unset($params[$k]);
                            }
                        }
                        $this->params = array_merge($this->params, $params);
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
                    foreach($_REQUEST as $k => $v){
                        $controller->request->$k = $v;
                    }
                    foreach($_GET as $k => $v){
                        $controller->get->$k = $v;
                    }
                    foreach($_POST as $k => $v){
                        $controller->post->$k = $v;
                    }
                    foreach($_FILES as $k => $v){
                        $controller->files->$k = $v;
                    }
                    $controller->method = $_SERVER['REQUEST_METHOD'];
                    if(method_exists($controller, $actionname)){
                        $method = (new \ReflectionClass($controller))->getMethod($actionname);
                        if(!$method->isPublic()){
                            echo '<b style="color: red">Action not exists</b>';
                            exit;
                        }
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
                                #$args[$parametername] = $parameter->isOptional() ? $parameter->getDefaultValue() : null;
                                $args[$parametername] = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
                                if(isset($this->query[$parametername])){
                                    $args[$parametername] = $this->query[$parametername];
                                }
                                #$args[$parametername] = isset($this->query[$parametername]) ? $this->query[$parametername] : null;
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