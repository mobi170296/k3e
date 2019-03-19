<?php
    header('content-type: text/event-stream');
    header('cache-control: no-cache');
    
    $time = date('r');
    echo "data: The server time is: {$time}\n\n";
    flush();