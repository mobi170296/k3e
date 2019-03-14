<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title><?php echo $this->ViewData['title'] . ' - ' . $this->config['APP_NAME']; ?></title>
        <link rel="shortcut icon" href="/images/icons/icon.png"/>
        <link rel="stylesheet" href="/styles/main.css"/>
        <script type="text/javascript" src="/scripts/core.js"></script>
        <?php
            $this->renderSection('head-script');
        ?>
    </head>
    <body>
        <?php
            require_once __DIR__ . DS . 'header.php';
            $this->renderBody();
            require_once __DIR__ . DS. 'footer.php';
        ?>
    </body>
</html>