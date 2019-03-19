<div>
    <form name="maincategory" action="/ajax/MainCategory/Del/<?php echo $this->ViewData['maincategory']->id; ?>" onsubmit="return false;">
        Bạn có muốn xóa danh mục <b><?php echo $this->ViewData['maincategory']->name; ?></b>?
        <div class="u-p10-0">
            <button class="btn btn-error" type="submit" name="del" value="del">Xóa danh mục</button>
        </div>
    </form>
</div>