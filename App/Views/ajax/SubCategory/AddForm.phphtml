<div>
    <form name="subcategory" action="/Ajax/SubCategory/Add" onsubmit="return false;">
        <table>
            <tr>
                <td>Chọn danh mục chính</td>
                <td>
                    <?php
                        echo '<select name="maincategory_id">';
                        foreach($this->ViewData['maincategorylist'] as $maincategory){
                            echo '<option value="' . $maincategory->id . '">'.$maincategory->name.'</option>';
                        }
                        echo '</select>';
                    ?>
                    
                </td>
            </tr>
            <tr>
                <td>Tên danh mục phụ</td>
                <td>
                    <input type="text" size="60" name="name" placeholder="Tên danh mục phụ" value=""/>
                    <p class="invalid-data"></p>
                </td>
            </tr>
            <tr>
                <td>Liên kết danh mục</td>
                <td>
                    <input type="text" size="60" name="link" placeholder="Liên kết" value=""/>
                    <p class="invalid-data"></p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button class="btn btn-allow" name="add" value="">Thêm danh mục phụ</button></td>
            </tr>
        </table>
    </form>
</div>