<div id="container">
    <div id="header">
        <div id="top-bar">
            <?php
                if($this->user->isLogin()){
                    echo '<a href="/User/Logout">Đăng xuất</a> ';
                }else{
                    echo '<a href="/User/Login">Đăng nhập</a> ';
                    echo '<a href="/User/Register">Đăng ký</a> ';
                }
            ?>
        </div>
    </div>
    <div id="control-bar-wrapper">
        <div id="control-bar">
            <div id="logo">
                <a href="/">
                    <span>Kakaka</span>
                </a>
                <div id="menu-wrapper">
                    <div id="menu">
                        <?php
                            $result = $this->dbcon->select('*')->from('maincategory')->execute();
                            while($row = $result->fetch_assoc()){
                                $mcate = new App\Models\MainCategoryModel($this->dbcon);
                                $mcate->id = $row['id'];
                                $mcate->loadFromDB();
                                echo '<div class="menu l1">';
                                echo '<a href="'.$mcate->link.'">' . $mcate->name . '</a>';
                                echo '<div class="menu l2">';
                                foreach($mcate->subcategory as $subcategory){
                                    echo '<a>'.$subcategory->name.'</a>';
                                }
                                echo '</div>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
            </div>

            <div id="search-bar">
                <form>
                    <input type="text" name="query" placeholder="Search query" size="75" autocomplete="off"/><button>Tìm kiếm</button>
                </form>
            </div>
            <?php
                if($this->user->isLogin()){
                    echo '<div id="account-control">';
                    echo "<div id=\"account-name\">{$this->user->lastname} {$this->user->firstname}</div>";
                    echo '<div id="account-options">';
                    echo '<a href="/User/Info">Thông tin tài khoản</a>';
                    if($this->user->haveRole(ADMIN_PRIV)){
                        echo '<a href="/Admin/Index">Quản trị hệ thống</a>';
                    }
                    echo '<a href="/User/Shop">Cửa hàng</a>';
                    echo '<a href="/User/Orders">Đơn hàng</a>';
                    echo '<a>Địa chỉ vận chuyển</a>';
                    echo '<a>Bài đánh giá</a>';
                    echo '<a href="/User/Logout">Đăng xuất</a>';
                    echo'</div>';
                    echo '</div>';
                }
            ?>
            <div id="scart-wrapper">
                <span id="scart-icon"></span>
                <div id="scart-list">
                    <div class="scart-item"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="body-wrapper">