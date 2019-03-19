<div>
    <form name="maincategory" action="/ajax/MainCategory/Add" onsubmit="return false;">
        Bạn có muốn xóa danh mục <b><?php echo $this->ViewData['maincategory']->name; ?></b>?
        <div class="u-p10-0">
            <button class="btn btn-error" type="submit" name="del" value="del">Xóa</button>
        </div>
    </form>
</div>