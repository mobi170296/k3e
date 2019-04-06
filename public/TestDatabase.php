<?php

    require 'utest.php';
    
    use Library\Database\Database;
    use Library\Database\DBNumber;
    $connection = new Database();
    
    
    $connection->delete('data', 'id=32');
    