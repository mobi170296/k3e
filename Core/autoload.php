<?php
    spl_autoload_register(function($classname){
        $path = k3_ROOT . DS . str_replace('\\', DS, ltrim($classname, '\\')) . '.class.php';
        if(file_exists($path)){
            require $path;
        }
    });