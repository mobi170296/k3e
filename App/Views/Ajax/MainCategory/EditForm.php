<div>
    <form name="maincategory" action="/ajax/MainCategory/Edit/<?php echo $this->ViewData['maincategory']->id; ?>" onsubmit="return false;">
        <table>
            <tr>
                <td>Tên danh mục</td>
                <td>
                    <input type="text" size="60" name="name" placeholder="Tên danh mục chính" value="<?php echo $this->ViewData['maincategory']->name; ?>"/>
                    <p class="invalid-data"></p>
                </td>
            </tr>
            <tr>
                <td>Đường link</td>
                <td>
                    <input type="text" size="60" name="link" placeholder="Đường dẫn liên kết" value="<?php echo $this->ViewData['maincategory']->link; ?>"/>
                    <p class="invalid-data"></p>
                </td>
            </tr>
            <tr>
                <td><input type="hidden" name="id" value="<?php echo $this->ViewData['maincategory']->id; ?>"/></td>
                <td>
                    <button type="submit" name="edit" class="btn btn-success">Lưu thay đổi</button></td>
            </tr>
        </table>
    </form>
</div>