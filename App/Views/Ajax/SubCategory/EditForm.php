<div>
    <form action="/Ajax/SubCategory/Edit/<?php echo $this->ViewData['subcategory']->id; ?>" onsubmit="return false;">
        <table>
            <tr>
                <td>Chọn danh mục chính</td>
                <td>
                    <?php
                        echo '<select name="maincategory_id">';
                        foreach($this->ViewData['maincategorylist'] as $maincategory){
                            echo '<option value="' . $maincategory->id . '" '.($this->ViewData['subcategory']->maincategory->id===$maincategory->id?'selected':'').'>'.$maincategory->name.'</option>';
                        }
                        echo '</select>';
                    ?>
                    
                </td>
            </tr>
            <tr>
                <td>Tên danh mục phụ</td>
                <td>
                    <input type="text" size="60" name="name" placeholder="Tên danh mục phụ" value="<?php echo $this->ViewData['subcategory']->name; ?>"/>
                </td>
            </tr>
            <tr>
                <td>Liên kết danh mục</td>
                <td>
                    <input type="text" size="60" name="link" placeholder="Liên kết" value="<?php echo $this->ViewData['subcategory']->link; ?>"/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button class="btn btn-allow" name="add" value="">Thêm danh mục phụ</button></td>
            </tr>
        </table>
    </form>
</div>