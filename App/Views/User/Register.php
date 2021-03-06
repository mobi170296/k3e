<?php
    $this->layout = 'layout.php';
    $this->TemplateData['title'] = 'Đăng ký tài khoản';
?>
<div id="main-body">
    <div class="tfw">
        <div class="tf">
            <div class="tf-header">Đăng ký tài khoản</div>
            <div class="tf-content">
                <?php
                    if($this->ViewData['action']!=null){
                        echo '<div class="message-box error">'.$this->ViewData['error'].'</div>';
                    }
                ?>
                <form action="" method="post">
                    <table>
                        <tr>
                            <td><label>Tên tài khoản</label></td>
                            <td>
                                <input type="text" name="username" value="<?php echo isset($this->ViewData['model'])?$this->ViewData['model']->username:''; ?>" placeholder="Tên tài khoản" size="30"/>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Mật khẩu</label></td>
                            <td>
                                <input type="password" name="password[]" value="<?php echo isset($this->ViewData['model'])? $this->ViewData['model']->password[0]:''; ?>"  placeholder="Mật khẩu" size="30"/>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Nhập lại mật khẩu</label></td>
                            <td>
                                <input type="password" name="password[]" value="<?php echo isset($this->ViewData['model'])?$this->ViewData['model']->password[1]:'' ?>"  placeholder="Nhập lại mật khẩu" size="30"/>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Họ</label></td>
                            <td>
                                <input type="text" name="lastname" value="<?php echo isset($this->ViewData['model'])?$this->ViewData['model']->lastname:'' ?>"  placeholder="Họ" size="30"/>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Tên</label></td>
                            <td>
                                <input type="text" name="firstname" value="<?php echo isset($this->ViewData['model'])?$this->ViewData['model']->firstname:'' ?>"  placeholder="Tên" size="30"/>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Số điện thoại</label></td>
                            <td>
                                <input type="text" name="phone" value="<?php echo isset($this->ViewData['model'])?$this->ViewData['model']->phone:''; ?>"  placeholder="Số điện thoại" size="30"/>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Email</label></td>
                            <td>
                                <input type="text" name="email" value="<?php echo isset($this->ViewData['model'])?$this->ViewData['model']->email:''; ?>"  placeholder="Email" size="30"/>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Giới tính</label></td>
                            <td>
                                <label><input type="radio" name="gender" value="1" <?php echo isset($this->ViewData['model'])?($this->ViewData['model']->gender==='0'?'':'checked="checked"'):'checked="checked"'; ?>/> Nam</label>
                                <label><input type="radio" name="gender" value="0" <?php echo isset($this->ViewData['model'])&&$this->ViewData['model']->gender==='0'?'checked="checked"':''; ?>/> Nữ</label>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Ngày tháng năm sinh</label></td>
                            <td>
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
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input class="btn-active" type="submit" name="action" value="Đăng ký"/></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>