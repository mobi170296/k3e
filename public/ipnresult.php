<?php

	print_r($_GET);
	
	$paramstr = '';
	
	foreach($_GET as $key => $value){
		strlen($value) > 0 && substr($key, 0, 4) === 'vpc_' && $key !== 'vpc_SecureHash' && ($paramstr .= $key . '='. $value . '&');
	}
	
	$paramstr = rtrim($paramstr, '&');
	
	$securesecret = 'A3EFDFABA8653DF2342E8DAC29B51AF0';
	
	echo strtoupper(hash_hmac('sha256', $paramstr, pack('H*', $securesecret)));