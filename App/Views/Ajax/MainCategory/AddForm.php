<div>
    <form name="maincategory" action="/ajax/MainCategory/Add" onsubmit="return false;">
        <table>
            <tr>
                <td>Tên danh mục</td>
                <td>
                    <input type="text" size="60" name="name" placeholder="Tên danh mục chính" value=""/>
                    <p class="invalid-data"></p>
                </td>
            </tr>
            <tr>
                <td>Đường link</td>
                <td>
                    <input type="text" size="60" name="link" placeholder="Đường dẫn liên kết" value=""/>
                    <p class="invalid-data"></p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" name="add" class="btn btn-allow">Thêm danh mục</button></td>
            </tr>
        </table>
    </form>
</div>