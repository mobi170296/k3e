<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $this->ViewData['title']; ?></title>
        <link rel="shortcut icon" href="/images/icons/icon.png"/>
        <link rel="stylesheet" href="/styles/main.css"/>
        <?php
            $this->renderSection('head-script');
        ?>
    </head>
    <body>
        <div id="main-container">
            <div id="header">
                <div id="header-control">
                    <a href="/User/Register">Đăng ký</a> <a href="/User/Login">Đăng nhập</a>
                </div>
            </div>
            <?php
                $this->renderBody();
            ?>
        </div>
    </body>
</html>