<?php
    define('k3_ROOT', dirname(__DIR__));
    define('DS', DIRECTORY_SEPARATOR);
    define('CONTROLLER_NS', 'App\Controllers');
    define('MODEL_NS', '\App\Controllers');
    
    require_once k3_ROOT . DS . 'Config' . DS . 'config.php';
    
    require_once k3_ROOT . DS . 'Core' . DS . 'autoload.php';
    
    $route = new Core\Router($_SERVER['QUERY_STRING']);
    
    $route->mapRoute('{controller}', ['controller' => 'Home', 'action' => 'Index']);
    $route->mapRoute('{controller}/{action}', ['controller' => 'Home', 'action' => 'Index']);
    $route->mapRoute('{controller}/{action}/{id}', ['controller' => 'Home', 'action' => 'Index', 'id' => '']);
    
    if($route->match()){
        #Call to Action method of Controller class
        $route->dispatch();
        #View is returned by Action of Controller
//        $view->render();
    }else{
        header('HTTP/1.1 404 Not Found');
        echo '<b style="color: red">404 Page</b>';
    }