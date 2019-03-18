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
            <div>
                <button class="btn btn-allow modal-add">Thêm danh mục chính</button>
            </div>
            <div class="u-p10-0">
                <?php
                    if(count($this->ViewData['maincategorylist'])){
                        echo '<table class="data-table maincategory">';
                        echo '<tr>';
                        echo '<th>ID</th>';
                        echo '<th>Tên danh mục</th>';
                        echo '<th>Liên kết</th>';
                        echo '<th>Thao tác</th>';
                        echo '</tr>';
                        foreach($this->ViewData['maincategorylist'] as $maincategory){
                            echo '<tr>';
                            echo "<td>{$maincategory->id}</td>";
                            echo "<td>{$maincategory->name}</td>";
                            echo "<td>{$maincategory->link}</td>";
                            echo '<td><button class="btn btn-success">Sửa</button> <button class="btn btn-error">Xóa</div></td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }else{
                        echo 'Danh sách danh mục sản phẩm chính hiện tại rỗng';
                    }
                ?>
            </div>
        </div>
    </div>
<script>
    $('button.modal-add').on('click', function(e){
        AJAX.create().url('/ajax/MainCategory/AddForm').sync(true).success(function(e){
            Modal.title('Thêm danh mục chính').html(this.response).show();
        }).get(null);
    });
</script>