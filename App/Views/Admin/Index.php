<?php
    $this->layout = 'layout.php';
    $this->TemplateData['title'] = 'Quản trị hệ thống';
?>

<div id="admin-wrapper" class="clearfix">
    <div class="left-menu">
    <div>
        <a href="/Admin/MainCategory" class="active">Quản lý danh mục chính</a>
    </div>
    <div>
        <a href="/Admin/SubCategory">Quản lý danh mục phụ</a>
    </div>
    <div>
        <a href="/Admin/AccountInfo">Thông tin tài khoản</a>
    </div>
    <div>
        <a href="/Admin/Orders">Đơn hàng của tôi</a>
    </div>
</div>
    <div class="right-content">
        Nội dung quản lý danh mục chính
    </div>
</div>