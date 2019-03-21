<?php
    $this->layout = 'layout.php';
    $this->TemplateData['title'] = 'Quản lý danh mục chính ngành hàng';
?>
    <script>
        "use strict"
        function chkMainCategoryData(f){
            var n=f['name'];
            var l=f['link'];
            var nv=n.val();
            var lv=l.val();
            var err=0;
            if(nv.length>200||nv.length===0){
                n.next().text('Độ dài tên danh mục chính không hợp lệ!');
                err|=1;
            }else{
                n.next().text('');
            }
            if(lv.length>1024||lv.length===0){
                l.next().text('Độ dài liên kết không hợp lệ!');
                err|=1;
            }else{
                l.next().text('');
            }
            if(err){
                return false;
            }else{
                return true;
            }
        }
        function ajaxSubmitForm(f){
            var fd=new FormData(f);
            window.$AJAX.create().url(f.action).success(function(e){
                window.result = JSON.parse(this.response);
                if(result.error){
                    window.$Toast.makeError(result.message, 5000);
                }else{
                    window.$Toast.makeSuccess(result.message, 5000);
                    window.location.reload();
                }
                window.$Modal.hide();
            }).error(function(e){
                window.$Toast.makeError('Đã xảy ra lỗi không mong muốn. Vui lòng kiểm tra lại kết nối mạng', 5000);
                window.$Modal.hide();
            }).post(fd);
            window.$Modal.waiting();
        }
    </script>
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
                <button class="btn btn-allow modal-add btn-add-i">Thêm danh mục chính</button>
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
                            echo '<td><button class="btn btn-success modal-edit btn-edit-i" data-id="'.$maincategory->id.'">Sửa</button> <button class="btn btn-error modal-del btn-del-i" data-id="'.$maincategory->id.'">Xóa</div></td>';
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
        window.$Modal.waiting().show();
        window.$AJAX.create().url('/ajax/MainCategory/AddForm').sync(true).success(function(e){
            window.$Modal.title('Thêm danh mục chính').html(this.response).show();
            $('div.modal form[name="maincategory"] button[name="add"]').on('click', function(e){
                if(chkMainCategoryData(this.form)){
                    ajaxSubmitForm(this.form);
                }
            });
        }).error(function(e){
            window.$Toast.makeError('Không thể tải dữ liệu', 5000);
            window.$Modal.hide();
        }).get(null);
    });
    
    $('button.modal-edit').on('click', function(e){
        window.$Modal.waiting().show();
        window.$AJAX.create().url('/ajax/MainCategory/EditForm/'+$(this).data('id')).success(function(e){
            window.$Modal.title('Sửa danh mục chính').html(this.response).show();
            $('div.modal form[name="maincategory"] button[name="edit"]').on('click', function(e){
                if(chkMainCategoryData(this.form)){
                    ajaxSubmitForm(this.form);
                }
            });
        }).error(function(e){
            window.$Toast.makeError('Không thể tải dữ liệu', 5000);
            window.$Modal.hide();
        }).get(null);
    });
    
    $('button.modal-del').on('click', function(e){
        window.$Modal.waiting().show();
        window.$AJAX.create().url('/ajax/MainCategory/DelForm/'+$(this).data('id')).success(function(e){
            window.$Modal.title('Xóa danh mục chính').html(this.response).show();
            $('form[name="maincategory"]').on('submit', function(e){
                return false;
            });
            $('div.modal form[name="maincategory"] button[name="del"]').on('click', function(e){
                ajaxSubmitForm(this.form);
            });
        }).error(function(e){
            window.$Toast.makeError('Không thể tải dữ liệu', 5000);
            window.$Modal.hide();
        }).get(null);
    });
</script>