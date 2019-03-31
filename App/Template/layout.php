<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title><?php echo $this->TemplateData['title'] . ' - ' . $this->config['APP_NAME']; ?></title>
        <link rel="shortcut icon" href="/images/icons/icon.png"/>
        <link rel="stylesheet" href="/styles/main.css"/>
        <link rel="stylesheet" href="/styles/sub.css"/>
        <link rel="stylesheet" href="/styles/toast.css"/>
        <script type="text/javascript" src="/scripts/core.js"></script>
        <script type="text/javascript" src="/scripts/toast.js"></script>
        <script type="text/javascript" src="/scripts/mj.js"></script>
        <script type="text/javascript" src="/scripts/xhr.js"></script>
        <?php
            $this->renderSection('head-script');
        ?>
    </head>
    <body>
        <?php
            require_once __DIR__ . DS . 'header.php';
            $this->renderBody();
            require_once __DIR__ . DS. 'footer.php';
            $this->Action('Info', 'Home');
        ?>
		<?php
			$this->renderSection('upload_file', true);
		?>
    </body>
</html>