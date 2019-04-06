<?php

    //get subcategory relies on maincategory
    $m = new mysqli('localhost', 'root', 'trinhvanlinh', 'k3e_db');
    $result = $m->query('select * from subcategory where maincategory_id=' . $_GET['id']);
    $aresult = [];
    
    while($row = $result->fetch_assoc()){
        $s = new stdClass();
        foreach($row as $k => $v){
            $s->$k = $v;
        }
        $aresult[] = $s;
    }
    
    echo json_encode($aresult);