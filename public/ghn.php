<?php
    require 'utest.php';
    
    use Library\VanChuyen\GHN\GHNRequest;
    use Library\VanChuyen\GHN\GHNCreateOrderParameter;
    use Library\VanChuyen\GHN\GHNServiceParameter;
    use Library\VanChuyen\GHN\GHNFeeParameter;
    
    $ghn = new GHNRequest();
    
    $ghnservices = new GHNServiceParameter(1572, 2011, 10000, 1, 1, 1);
    
    $services = $ghn->getServices($ghnservices);
    
    echo 'Có ' . count($services) . ' dịch vụ vận chuyển có sẵn';
    
    $servicefee = $services[0]->ServiceFee;
    
    $minid = 0;
    
    for($id = 0; $id < count($services); $id++){
        if($servicefee > $services[$id]->ServiceFee){
            $servicefee = $services[$id]->ServiceFee;
            $minid = $id;
        }
    }
    
    
    
    $serviceid = $services[$minid]->ServiceID;
    
    $clientName = 'Nguyễn Văn Tuấn';
    $clientPhone = '0773336666';
    $clientAddress = 'hẻm 201';
    $customerName = 'Trịnh Văn Linh';
    $customerPhone = '1111';
    $customerAddress = 'ấp nhất';
    $serviceId = $serviceid;
    $weight = 500;
    $length = 50;
    $width = 30;
    $height = 10;
    $insuranceFee = 9e6;
    $codAmount = 9e6 + 60e3;
    $note = 'Hàng dễ vỡ';
    $content = 'Mắt kính thời trang, Máy tính bảng, Nhẫn Kim Cương!';
    
    $ghnservices = new GHNServiceParameter(1572, 2011, $weight, $length, $width, $height);
    
    $ghnfee = new GHNFeeParameter(1572, 2011, $serviceid, $weight, $length, $width, $height, 10000);
    
    print_r($ghn->calculateFee($ghnfee));
    
    $ghncreateorder = new GHNCreateOrderParameter(1572, 2011, $clientName, $clientPhone, $clientAddress, $customerName, $customerPhone, $customerAddress, $serviceId, $weight, $length, $width, $height, $insuranceFee, $codAmount, $note, $content);
    
    $orderinfo = $ghn->createOrder($ghncreateorder);
    
    print_r($orderinfo);