<?php
    ob_start();
    
    print_r($_SERVER);
    
    $serverinfo = ob_get_contents();
    
    ob_end_clean();
    
    $serverinfo = preg_replace('/\n/', '<br/>', $serverinfo);
    
    
    echo $serverinfo;