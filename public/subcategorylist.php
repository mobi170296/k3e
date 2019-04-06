
<?php
    $m = new mysqli('localhost', 'root', 'trinhvanlinh', 'k3e_db');
    $result = $m->query("select * from subcategory where maincategory_id = " . $_POST['id']);
    $aresult = [];
    while($row = $result->fetch_assoc()){
        $mcate = new stdClass();
        foreach($row as $k => $v){
            $mcate->$k = $v;
        }
        
        $aresult[] = $mcate;
    }
    
    header('content-type: application/json');
    
    echo json_encode($aresult);