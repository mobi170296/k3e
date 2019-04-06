<?php
    require_once 'utest.php';
    
    set_time_limit(600);
    
    use Library\ClientRequest\ClientRequest;
    
    
    # Dest: https://console.ghn.vn/api/v1/apiv3/GetDistricts
    # Token: 5c67d2e694c06b42dc5531a8
    
    $request = new ClientRequest("https://console.ghn.vn/api/v1/apiv3/GetDistricts");
    
    $request->setOption(CURLOPT_RETURNTRANSFER, 1)->setOption(CURLOPT_POST, 1)->setOption(CURLOPT_POSTFIELDS, '{"token":"5c67d2e694c06b42dc5531a8"}');
    
    $list = json_decode($request->execute());
    
    $province = [];
    $pid = [];
    $district = [];
    
    foreach($list->data as $datas){
        if(!in_array($datas->ProvinceID, $pid)){
            $province[] = [$datas->ProvinceID, $datas->ProvinceName];
            $pid[] = $datas->ProvinceID;
        }
        
        $district[] = [$datas->DistrictID, $datas->ProvinceID, $datas->DistrictName];
    }
    
    $m = new mysqli('localhost', 'root', 'trinhvanlinh', 'test');
    
    foreach($province as $pro){
        $m->query('insert into province values(' . $pro[0] . ', \''. $pro[1] .'\')');
    }
    
    foreach($district as $dis){
        $m->query('insert into district values(' . $dis[0] . ', ' . $dis[1] . ', \'' . $dis[2] . '\')');
    }
    
    echo 'OK';