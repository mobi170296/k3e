<?php

    $product_image_chosen = $_POST['product_image_chosen'];
    $product_image_temp = $_POST['product_image_temp'];
    
    
    echo 'TEMP (' . count($product_image_temp) . ')<br/>';
    foreach($product_image_temp as $value){
        echo $value . ', ';
    }
    echo '<br/>';
    
    echo 'CHOSEN (' . count($product_image_chosen) . ')<br/>';
    foreach($product_image_chosen as $value){
        echo $value . ', ';
    }
    
    
    print_r($_FILES);