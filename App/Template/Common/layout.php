<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title><?php echo $this->Data->Title . ' - ' . $this->config['APP_NAME']; ?></title>
        <link rel="shortcut icon" href="/images/icons/icon.png"/>
        <link rel="stylesheet" href="/styles/utility.css"/>
        <link rel="stylesheet" href="/styles/main.css"/>
        <link rel="stylesheet" href="/styles/sub.css"/>
        <link rel="stylesheet" href="/styles/toast.css"/>
        <link rel="stylesheet" href="/styles/form.css"/>
        <link rel="stylesheet" href="/styles/utility.css"/>
        <script type="text/javascript" src="/scripts/module.js"></script>
        <script type="text/javascript" src="/scripts/core.js"></script>
        <script type="text/javascript" src="/scripts/toast.js"></script>
        <script type="text/javascript" src="/scripts/xhr.js"></script>
        <script type="text/javascript" src="/scripts/ktemplate.js"></script>
        <script type="text/javascript" src="/scripts/iktemplate.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
		<?php
			$this->renderSection('upload_file', true);
		?>
    </body>
</html>