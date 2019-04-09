<?php
function getBoolean(){
    try{
        return true;
    } catch (Exception $ex) {

    } finally {
        echo 'finally statement';
    }
}

var_dump(getBoolean());
    
?>