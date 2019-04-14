<?php
    session_start();
    define('k3_ROOT', dirname(__DIR__));
    define('DS', DIRECTORY_SEPARATOR);
    define('CONTROLLER_NS', '\App\Controllers');
    define('MODEL_NS', '\App\Controllers');
    define('VIEW_DIR', k3_ROOT . DS .  'App' . DS . 'Views');
    define('TEMPLATE_DIR', k3_ROOT . DS . 'App' . DS . 'Template');
    define('APP_DIR', k3_ROOT . DS . 'App');
    
    require_once k3_ROOT . DS . 'Config' . DS . 'config.php';
    
    require_once k3_ROOT . DS . 'App' . DS . 'Define' . DS . 'define.php';
    
    require_once k3_ROOT . DS . 'Core' . DS . 'autoload.php';
    
//    #Begin Exception Handler Block
//    function ExceptionHandler(Exception $ex){
//        echo '<b>Đã xảy ra ngoại lệ</b>: <font color="red">' . $ex->getMessage() . '</font>';
//    }
//    set_exception_handler('\ExceptionHandler');
//    #End Exception Handler Block
//    
//    #Begin Error Handler Block
//    function ErrorHandler($errno, $error){
//        echo '<b>Đã xảy ra lỗi:</b> <font color="red">' . $error . '(' . $errno . ')' . '</font>';
//    }
//    set_error_handler('\ErrorHandler');
//    #End Error Handler Block
    
    $route = new Core\Router($_SERVER['QUERY_STRING']);
    
    #print_r($_SERVER);
    
    $route->mapRoute('{controller:"api/\w+"}/{action}', []);
    $route->mapRoute('{controller:"api/\w+"}/{action}/{id}', []);
    $route->mapRoute('Layout/Header', ['controller' => 'Home', 'action' => 'Index']);
    $route->mapRoute('Layout/ControlBar', ['controller' => 'Home', 'action' => 'Index']);
    $route->mapRoute('', ['controller' => 'Home', 'action' => 'Index']);
    $route->mapRoute('{controller:"\w+/\w+"}/{action}', []);
    $route->mapRoute('{controller:"\w+/\w+"}/{action}/{id:"\d+"}', []);
    $route->mapRoute('{controller}', ['controller' => 'Home', 'action' => 'Index']);
    $route->mapRoute('{controller}/{action}', ['controller' => 'Home', 'action' => 'Index']);
    $route->mapRoute('{controller}/{action}/{id}', ['controller' => 'Home', 'action' => 'Index', 'id' => '']);
    
    try{
        if($route->match()){
            #Call to Action method of Controller class
            $view = $route->dispatch();
            #View to render
            if($view){
                $view->render();
            }
        }else{
            throw new Core\RouterException('Controller Not Found!', -1);
        }
    } catch (Core\RouterException $e) {
        $message = $e->getMessage();
        header('HTTP/1.1 404 Not Found');
        require k3_ROOT . DS . 'Core' . DS . '_errortemplate.phphtml';
    } catch (Exception $e){
        echo $e->getMessage();
    }