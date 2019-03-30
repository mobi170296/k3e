<?php
    define('k3_ROOT', dirname(__DIR__));
    define('DS', DIRECTORY_SEPARATOR);
    define('CONTROLLER_NS', 'App\Controllers');
    define('MODEL_NS', '\App\Controllers');
    define('VIEW_DIR', k3_ROOT . DS .  'App' . DS . 'Views');
    define('TEMPLATE_DIR', k3_ROOT . DS . 'App' . DS . 'Template');
    define('PUBLIC_UPLOAD_IMAGE_DIR', k3_ROOT . DS . 'public' . DS . 'upload' . DS . 'images');
    define('PUBLIC_UPLOAD_IMAGE_PATH', '/public/upload/images');
    
    require_once k3_ROOT . DS . 'Config' . DS . 'config.php';
    
    require_once k3_ROOT . DS . 'App' . DS . 'Define' . DS . 'define.php';
    
    require_once k3_ROOT . DS . 'Core' . DS . 'autoload.php';
    
    #require file này ở PHP CLI