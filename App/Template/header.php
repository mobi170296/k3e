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
                                echo '<a>' . $mcate->name . '</a>';
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
                    <input type="text" name="query" placeholder="Search query" size="70" autocomplete="off"/><button>Search</button>
                </form>
            </div>
            <?php
                if($this->user->isLogin()){
                    echo <<<ACC_CONTROL
                    <div id="account-control">
                        <div id="account-name">{$this->user->lastname} {$this->user->firstname}</div>
                        <div id="account-options">
                            <a href="/User/Info">Thông tin tài khoản</a>
                            <a href="/User/Shop">Cửa hàng</a>
                            <a href="/User/Orders">Đơn hàng</a>
                            <a>Địa chỉ vận chuyển</a>
                            <a>Bài đánh giá</a>
                            <a href="/User/Logout">Đăng xuất</a>
                        </div>
                    </div>
ACC_CONTROL;
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