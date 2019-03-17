<?php
    $this->layout = 'error_page.php';
    $this->TemplateData['title'] = 'Lỗi';
?>

<?php
    echo '<div style="color: red; font-weight: bold; text-align: center;">'.$this->ViewData['error'].'</div>';
?>