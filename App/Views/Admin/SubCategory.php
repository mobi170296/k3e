<?php
    $this->layout = 'layout.php';
    $this->TemplateData['title'] = 'Quản lý danh mục phụ ngành hàng';
?>
<div id="admin-wrapper" class="clearfix">
    <div class="left-menu">
    <div>
        <a href="/Admin/MainCategory">Quản lý danh mục chính</a>
    </div>
    <div>
        <a href="/Admin/SubCategory" class="active">Quản lý danh mục phụ</a>
    </div>
</div>
    <div class="right-content">
        Nội dung quản lý danh mục phụ
    </div>
</div>