<?php
    $this->layout = 'layout.php';
    $this->ViewData['title'] = 'Đăng nhập';
?>
<div id="main-body">
    <div class="tfw">
        <div class="tf">
            <div class="tf-header">đăng nhập</div>
            <div class="tf-content">
                <?php
                    if(isset($this->ViewData['error'])){
                        echo '<div class="message-box error">';
                        echo '<div>'.$this->ViewData['error'].'</div>';
                        echo '</div>';
                    }
                ?>
                <form action="" method="post">
                    <div class="form-field">
                        <div class="form-field-label">
                            <label>Tên đăng nhập</label>
                        </div>
                        <div class="form-field-control">
                            <input type="text" name="username" placeholder="Tên đăng nhập" size="30"/>
                        </div>
                    </div>
                    <div class="form-field">
                        <div class="form-field-label">
                            <label>Mật khẩu</label>
                        </div>
                        <div class="form-field-control">
                            <input type="password" name="password" placeholder="Mật khẩu" size="30"/>
                        </div>
                    </div>
                    <input class="btn-active" type="submit" name="action" value="Đăng nhập"/>
                </form>
            </div>
        </div>
    </div>
</div>
