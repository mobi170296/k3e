<?php
    $this->layout = 'layout.php';
    $this->TemplateData['title'] = 'Quản lý danh mục phụ ngành hàng';
?>

<div id="admin-wrapper" class="clearfix">
    <div class="left-menu">
        <div>
            <a href="/Admin/MainCategory" >Quản lý danh mục chính</a>
        </div>
        <div>
            <a href="/Admin/SubCategory" class="active">Quản lý danh mục phụ</a>
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
            <button class="btn btn-allow modal-add btn-add-i">Thêm danh mục phụ</button>
        </div>
        <div class="u-p10-0">
            <?php
                if(count($this->ViewData['subcategorylist'])){
                    echo '<table class="table-data subcategory">';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Tên danh mục phụ</th>';
                    echo '<th>Tên danh mục chính</th>';
                    echo '<th>Liên kết</th>';
                    echo '<th>Thao tác</th>';
                    echo '<tr>';
                    
                    foreach($this->ViewData['subcategorylist'] as $subcategory){
                        echo '<tr>';
                        echo "<td>{$subcategory->id}</td>";
                        echo "<td>{$subcategory->name}</td>";
                        echo "<td>{$subcategory->maincategory->name}</td>";
                        echo '<td><button class="btn btn-edit-i btn-success">Sửa</button> <button class="btn btn-del-i btn-danger">Xóa</button></td>';
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                }else{
                    echo 'Danh sách danh mục phụ sản phẩm hiện tại rỗng';
                }
            ?>
        </div>
    </div>
</div>