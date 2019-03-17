<?php
    $this->layout = 'layout.php';
    $this->TemplateData['title'] = 'Quản lý danh mục chính ngành hàng';
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
            <button class="btn btn-add">Thêm danh mục chính</button>
        </div>
    </div>

<script>
    
</script>