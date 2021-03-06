<?php
    $this->layout = 'layout.php';
    $this->TemplateData['title'] = 'Quản lý danh mục phụ ngành hàng';
?>

<script>
    function checkSubCategoryData(f){
        var err=0;
        var n=f.name;
        var l=f.link;
        var nv=$(n).val();
        var lv=$(l).val();
        if(nv.length>200 || nv.length==0){
            $(n).next().text('Độ dài tên danh mục không hợp lệ');
            err|=1;
        }else{
            $(n).next().text('');
        }
        if(lv.length>1024){
            $(l).next().text('Độ dài liên kết không hợp lệ');
            err|=1;
        }else{
            $(l).next().text('');
        }
        return err===0;
    }
</script>

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
                    echo '<table class="data-table subcategory">';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Tên danh mục phụ</th>';
                    echo '<th>Tên danh mục chính</th>';
                    echo '<th>Liên kết</th>';
                    echo '<th>Thao tác</th>';
                    echo '</tr>';
                    
                    foreach($this->ViewData['subcategorylist'] as $subcategory){
                        echo '<tr>';
                        echo "<td>{$subcategory->id}</td>";
                        echo "<td>{$subcategory->name}</td>";
                        echo "<td>{$subcategory->maincategory->name}</td>";
                        echo "<td>{$subcategory->link}</td>";
                        echo '<td><button class="btn btn-edit-i btn-success">Sửa</button> <button class="btn btn-del-i btn-error">Xóa</button></td>';
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
<script>
    $('.btn.modal-add').on('click', function(e){
        $Modal.waiting().show();
        $AJAX.create().url('/ajax/SubCategory/AddForm').success(function(e){
            $Modal.title('Thêm danh mục phụ').html(this.response).show();
            $('div.modal form[name="subcategory"] button[name="add"]').on('click', function(e){
                if(checkSubCategoryData(this.form)){
                    $AJAX.create().success(function(e){
                        var result = JSON.parse(this.response);
                        if(result.error){
                            window.$Toast.makeError(result.message, 5000);
                        }else{
                            window.$Toast.makeSuccess(result.message, 5000);
                            window.location.reload();
                        }
                        window.$Toast.hide();
                    }).error(function(e){
                        $Toast.makeError('Đã xảy ra lỗi khi giao tiếp với máy chủ', 5000);
                    }).postForm(this.form);
                    window.$Modal.hide();
                }
            });
        }).error(function(e){
            $Toast.makeError('Không thể tải dữ liệu', 10000);
            $Modal.hide();
        }).get();
    });
</script>