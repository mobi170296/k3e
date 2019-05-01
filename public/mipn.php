<?php

	$secure_secret = 'A3EFDFABA8653DF2342E8DAC29B51AF0';
	
	$str = '';
	
	foreach($_GET as $key => $value){
		if(substr($key, 0, 4) === 'vpc_' && strlen($value) > 0 && $key !== 'vpc_SecureHash'){
			$str .= $key . '='. $value . '&';
			echo $key . '<br/>';
		}
	}
	
	$str = rtrim($str, '&');
	
	$myhash = strtoupper( hash_hmac('sha256', $str, pack('H*', $secure_secret)));
	
	if($myhash === $_GET['vpc_SecureHash']){{
		echo 'OK';
	}}else{
		echo 'NOT OK';
	}