<?php
    $this->layout = 'layout.php';
    $this->TemplateData['title'] = 'About of Home controller';
    $this->addSection('head-script', <<<SCRIPT
            <script type="text/javascript">
                window.alert("This is head-script");
            </script>
SCRIPT
            );
?>

Đây là nội dung từ About Action of Home Controller