<?php
    $this->layout = 'layout.php';
    $this->ViewData['title'] = 'Đăng ký tài khoản - k3E';
?>

<h3>Tạo tài khoản</h3>

<form action="" method="post">
    <div>
        <label>Tên tài khoản<br/><input type="text" name="username" placeholder="Tên tài khoản" />
        </label>
    </div>
    <div>
        <label>Mật khẩu<br/><input type="password" name="password[]" placeholder="Mật khẩu"/>
    </div>
    <div>
        <label>Nhập lại mật khẩu<br/><input type="password" name="password[]" placeholder="Nhập lại mật khẩu"/></label>
    </div>
    <div>
        <label>Số điện thoại<br/><input type="text" name="phone" placeholder="Số điện thoại"/></label>
    </div>
    <div>
        <label>Email<br/><input type="text" name="email" placeholder="Email"/></label>
    </div>
    <div>
        <div>Giới tính</div>
        <label><input type="radio" name="gender" value="1" checked="checked"/> Nam</label>
        <label><input type="radio" name="gender" value="0"/> Nữ</label>
    </div>
    <div>
        <label>Ngày tháng năm sinh</label><br/>
        <select name="year">
            <option value="0">Năm</option>
            <?php
                for($i = date("Y"); $i>=1900; $i--){
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
            ?>
        </select>
        <select name="month">
            <option value="0">Tháng</option>
            <?php
                for($i=1; $i<=12; $i++){
                    echo "<option value=\"$i\">$i</option>";
                }
            ?>
        </select>
        <select name="day">
            <option value="0">Ngày</option>
            <?php
                for($i=1; $i<=31; $i++){
                    echo "<option value=\"$i\">$i</option>";
                }
            ?>
        </select>
    </div>
    <div>
        <input type="reset" name="action" value="Nhập lại"/>
        <input type="submit" name="action" value="Đăng ký"/>
    </div>
</form>