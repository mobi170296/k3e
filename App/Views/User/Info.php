<?php
    $this->layout = 'layout.php';
    $this->TemplateData['title'] = 'Thông tin tài khoản';
?>


<div id="main-body">
    <div class="tfw">
        <div class="tf">
            <div class="tf-header">Thông tin tài khoản</div>
            <div class="tf-content">
                <?php
                    if(isset($this->ViewData['error'])){
                        echo '<div class="message-box error">' . $this->ViewData['error'] . '</div>';
                    }
                    if(isset($this->ViewData['success'])){
                        echo '<div class="message-box success">'. $this->ViewData['success']. '</div>';
                    }
                ?>
                <form action="" method="post">
                    <table>
                        <tr>
                            <td>Tên tài khoản</td>
                            <td><input type="text" name="username" size="30" value="<?php echo $this->user->username; ?>" disabled="disabled"/></td>
                        </tr>
                        
                        <tr>
                            <td>Họ và tên đệm</td>
                            <td><input type="text" name="lastname" size="30" value="<?php echo isset($this->ViewData['user'])?$this->ViewData['user']->lastname:$this->user->lastname; ?>"/></td>
                        </tr>
                        
                        <tr>
                            <td>Tên</td>
                            <td><input type="text" name="firstname" size="30" value="<?php echo isset($this->ViewData['user'])?$this->ViewData['user']->firstname:$this->user->firstname; ?>"/></td>
                        </tr>
                        
                        <tr>
                            <td>Số điện thoại</td>
                            <td><input type="text" name="phone" size="30" value="<?php echo $this->user->phone; ?>" disabled="disabled"/></td>
                        </tr>
                        
                        <tr>
                            <td>Email</td>
                            <td><input type="text" name="email" size="30" value="<?php echo $this->user->email; ?>" disabled="disabled"/></td>
                        </tr>
                        
                        <tr>
                            <td>Địa chỉ</td>
                            <td><input type="text" name="address" size="30" value="<?php echo isset($this->ViewData['user'])?$this->ViewData['user']->address:$this->user->address;?>"/></td>
                        </tr>
                        
                        <tr>
                            <td>Ngày tháng năm sinh</td>
                            <td>
                                <select name="year">
                                    <option value="0">Năm</option>
                                    <?php
                                        for($i=date("Y"); $i>=1900; $i--){
                                            echo '<option value="' . $i . '" '.($this->user->birthday->year==$i?'selected="selected"':'').'>' . $i . '</option>';
                                        }
                                    ?>
                                </select>
                                <select name="month">
                                    <option value="0">Tháng</option>
                                    <?php
                                        for($i=1; $i<=12; $i++){
                                            echo "<option value=\"$i\" ".($this->user->birthday->month==$i?'selected="selected"':'').">$i</option>";
                                        }
                                    ?>
                                </select>
                                <select name="day">
                                    <option value="0">Ngày</option>
                                    <?php
                                        for($i=1; $i<=31; $i++){
                                            echo "<option value=\"$i\" ".($this->user->birthday->day==$i?'selected="selected"':'').">$i</option>";
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Giới tính</td>
                            <td><label><input type="radio" name="gender" value="1" <?php echo $this->user->gender==1?'checked="checked"':''; ?>/> Nam</label> <label><input type="radio" name="gender" value="0" <?php echo $this->user->gender==1?'':'checked="checked"';?>/> Nữ</label></td>
                        </tr>
                        <tr>
                            <td>Số dư tài khoản</td>
                            <td><?php echo $this->user->money .'đ'; ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input class="btn-active" type="submit" name="action" value="Cập nhật thông tin"/></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>