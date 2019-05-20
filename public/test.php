<?php
    print_r($_GET);
?>

<form action="/api/upload/shopavatar" method="post" enctype="multipart/form-data">
    <input type="file" name="shopavatar"/>
    <button type="submit" name="upload" value="upload">Upload</button>
</form>

<form action="" method="post">
    <input type="text" name="input"/>
    <button type="submit" name="send" value="send">OK</button>
</form>
<?php
    echo $_SERVER['QUERY_STRING'];
    if(isset($_POST['input'])){
        $_POST['input'] = 'ẫ';
        for($i=0; $i<strlen($_POST['input']); $i++){
            echo dechex(ord($_POST['input'][$i])) . ' ' ;
        }
    }