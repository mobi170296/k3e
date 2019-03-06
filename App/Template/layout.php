<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $this->ViewData['title']; ?></title>
        <?php
            $this->renderSection('head-script');
        ?>
    </head>
    <body>
        <?php
            $this->renderBody();
        ?>
    </body>
</html>