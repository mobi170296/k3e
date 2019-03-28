<?php
    header('content-type: image/jpg');
    $mysql = new Mysqli('localhost', 'root', 'trinhvanlinh', 'test');
    $result = $mysql->query('select * from test');
    $row = $result ->fetch_assoc();
    echo $row['data'];