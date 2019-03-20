<?php
    function code($n){
        $l = mb_strlen($n);
        $t = 0;
        for($i=0;$i<$l;$i++){
            $t+=ord($n[$i]);
        }
        return $t%10;
    }
    #
    #   /img{code}/{year}/{month}/{day}/{filename}
    #   filename={uniqid()}_{md5(tmp_name)}_{rand()}{extension}
    #   rand (3!)
        
    function filename($ufile){
        
    }
?>

<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="photo"/>
    <button type="submit" name="send">Send</button>
</form>