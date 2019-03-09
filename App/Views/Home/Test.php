<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Template</title>
        <style>
            div#main-menu{
                font-family: segoe ui;
                display: inline-block;
            }
            div#main-menu ul{
                list-style-type: none;
                margin: 0px;
                padding: 0px;
                border: 1px solid rgb(121,178,89);
            }
            div#main-menu ul.main-cate{
                position: relative;
            }
            div#main-menu a{
                display: inline-block;
                text-decoration: none;
            }
            div#main-menu ul.main-cate > li > a{
                display: block;
                background: #fefefe;
                padding: 5px 20px 5px 30px;
                color: #777;
                position: relative;
            }
            div#main-menu ul.main-cate > li:hover > a::after{
                content: '';
                display: inline-block;
                position: absolute;
                top: 0px;
                left: 100%;
                margin-left: 1px;
                border-style: solid;
                border-width: 15px;
                border-color: transparent transparent transparent rgb(121,178,89);
            }
            div#main-menu ul.main-cate > li:hover > a{
                background: rgb(121,178,89);
                color: #fff;
            }
            div#main-menu ul.sub-cate{
                position: absolute;
                top: 0px;
                left: 100%;
                display: block;
                width: 600px;
                height: 100%;
                border: 1px solid rgb(121,178,89);
                display: none;
                padding: 10px 20px;
                box-sizing: border-box;
                font-size: 11pt;
            }
            div#main-menu ul.main-cate > li:hover ul.sub-cate{
                display: block;
            }
            div#main-menu ul.sub-cate a{
                color: #444;
            }
            div#main-menu ul.sub-cate a:hover{
                color: rgb(121,178,89);
            }
        </style>
    </head>
    <body>
        <div id="main-menu">
            <ul class="main-cate">
                <?php
                    foreach($this->ViewData['categorylist'] as $maincategory){
                        echo '<li>';
                        echo '<a href="'.$maincategory->link.'">'.$maincategory->name.'</a>';
                        echo '<ul class="sub-cate">';
                        foreach($maincategory->subcategory as $subcategory){
                            echo '<li><a href="'.$subcategory->link.'">'.$subcategory->name.'</a></li>';
                        }
                        echo '</ul>';
                        echo '</li>';
                    }
                ?>
                <!--
                <li>
                    <a href="#">Điện tử</a>
                    <ul class="sub-cate">
                        <li>Điện thoại</li>
                        <li>Máy tính bảng</li>
                        <li>Máy tính cá nhân</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Điện lạnh</a>
                    <ul class="sub-cate">
                        <li>Tủ lạnh</li>
                        <li>Máy lạnh</li>
                        <li>Quạt gió</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Văn phòng phẩm</a>
                    <ul class="sub-cate">
                        <li>Bút, viết</li>
                        <li>Vở</li>
                        <li>Sách giáo khoa</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Điện thoại - Máy tính</a>
                    <ul class="sub-cate">
                        <li>IPhone</li>
                        <li>Smartphone</li>
                        <li>Điện thoại phổ thông</li>
                        <li>iPad</li>
                        <li>IPhone cũ, IPhone lock</li>
                        <li>Sony Xperia</li>
                        <li>Máy tính bảng Android</li>
                        <li>LG</li>
                        <li>HTC</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Thời trang nữ</a>
                    <ul class="sub-cate">
                        <li>IPhone</li>
                        <li>Smartphone</li>
                        <li>Điện thoại phổ thông</li>
                        <li>iPad</li>
                        <li>IPhone cũ, IPhone lock</li>
                        <li>Sony Xperia</li>
                        <li>Máy tính bảng Android</li>
                        <li>LG</li>
                        <li>HTC</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Thời trang nam</a>
                    <ul class="sub-cate">
                        <li>IPhone</li>
                        <li>Smartphone</li>
                        <li>Điện thoại phổ thông</li>
                        <li>iPad</li>
                        <li>IPhone cũ, IPhone lock</li>
                        <li>Sony Xperia</li>
                        <li>Máy tính bảng Android</li>
                        <li>LG</li>
                        <li>HTC</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Giày dép - Túi xách</a>
                    <ul class="sub-cate">
                        <li>IPhone</li>
                        <li>Smartphone</li>
                        <li>Điện thoại phổ thông</li>
                        <li>iPad</li>
                        <li>IPhone cũ, IPhone lock</li>
                        <li>Sony Xperia</li>
                        <li>Máy tính bảng Android</li>
                        <li>LG</li>
                        <li>HTC</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Phụ kiện công nghệ</a>
                    <ul class="sub-cate">
                        <li>IPhone</li>
                        <li>Smartphone</li>
                        <li>Điện thoại phổ thông</li>
                        <li>iPad</li>
                        <li>IPhone cũ, IPhone lock</li>
                        <li>Sony Xperia</li>
                        <li>Máy tính bảng Android</li>
                        <li>LG</li>
                        <li>HTC</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Mẹ bé - Đồ chơi</a>
                    <ul class="sub-cate">
                        <li>IPhone</li>
                        <li>Smartphone</li>
                        <li>Điện thoại phổ thông</li>
                        <li>iPad</li>
                        <li>IPhone cũ, IPhone lock</li>
                        <li>Sony Xperia</li>
                        <li>Máy tính bảng Android</li>
                        <li>LG</li>
                        <li>HTC</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Đồng hồ - Phụ kiện</a>
                    <ul class="sub-cate">
                        <li>IPhone</li>
                        <li>Smartphone</li>
                        <li>Điện thoại phổ thông</li>
                        <li>iPad</li>
                        <li>IPhone cũ, IPhone lock</li>
                        <li>Sony Xperia</li>
                        <li>Máy tính bảng Android</li>
                        <li>LG</li>
                        <li>HTC</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Nhà cửa - Bách hóa</a>
                    <ul class="sub-cate">
                        <li>IPhone</li>
                        <li>Smartphone</li>
                        <li>Điện thoại phổ thông</li>
                        <li>iPad</li>
                        <li>IPhone cũ, IPhone lock</li>
                        <li>Sony Xperia</li>
                        <li>Máy tính bảng Android</li>
                        <li>LG</li>
                        <li>HTC</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Sức khỏe - Sắc đẹp</a>
                    <ul class="sub-cate">
                        <li>IPhone</li>
                        <li>Smartphone</li>
                        <li>Điện thoại phổ thông</li>
                        <li>iPad</li>
                        <li>IPhone cũ, IPhone lock</li>
                        <li>Sony Xperia</li>
                        <li>Máy tính bảng Android</li>
                        <li>LG</li>
                        <li>HTC</li>
                    </ul>
                </li>
                <li>
                    <a href="#">Tivi - Giải trí - Máy lạnh</a>
                    <ul class="sub-cate">
                        <li>IPhone</li>
                        <li>Smartphone</li>
                        <li>Điện thoại phổ thông</li>
                        <li>iPad</li>
                        <li>IPhone cũ, IPhone lock</li>
                        <li>Sony Xperia</li>
                        <li>Máy tính bảng Android</li>
                        <li>LG</li>
                        <li>HTC</li>
                    </ul>
                </li>
                -->
            </ul>
        </div>
    </body>
</html>