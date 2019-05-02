<?php
require 'utest.php';

?>


<form action="" method="post">
    <input type="text" name="text" value=""/>
    <input type="submit"/>
</form>


<?php
    if(isset($_POST['text'])){
        echo 'Text: ' . Library\Common\Text::toSeqASCII($_POST['text']);
    }