<?php
    $this->layout = 'layout.php';
    $this->ViewData['title'] = 'Đăng nhập - K3e';
?>
<div>
    Bạn đã nhập thông tin <?php echo $this->ViewData['username'] . ', ' . $this->ViewData['password']; ?>
</div>
<form action="" method="get">
    <div>
        <label>Tên đăng nhập<br/>
            <input type="text" name="username" placeholder="Tên đăng nhập"/>
        </label>
    </div>
    <div>
        <label>Mật khẩu<br/>
            <input type="password" name="password" placeholder="Mật khẩu"/>
        </label>
    </div>
    <input type="submit" name="action" value="Đăng nhập"/>
</form>