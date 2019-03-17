<?php
    $this->layout = 'layout.php';
    $this->TemplateData['title'] = 'Quản lý tài khoản';
?>

<div id="admin-wrapper" class="clearfix">
    <div class="left-menu">
        <div>
            <a href="/Admin/MainCategory">Quản lý danh mục chính</a>
        </div>
        <div>
            <a href="/Admin/SubCategory">Quản lý danh mục phụ</a>
        </div>
        <div>
            <a href="/Admin/AccountInfo" class="active">Thông tin tài khoản</a>
        </div>
        <div>
            <a href="/Admin/Orders">Đơn hàng của tôi</a>
        </div>
    </div>
    <div class="right-content">
        Nội dung quản lý tài khoản
    </div>
</div>
