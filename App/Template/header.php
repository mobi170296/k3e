<div id="container">
    <div id="header">
        <div id="top-bar">
            <a href="/User/Login">Đăng nhập</a>
            <a href="/User/Register">Đăng ký</a>
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
                    <input type="text" name="query" placeholder="Search query" size="60" autocomplete="off"/><button>Search</button>
                </form>
            </div>
            <div id="account-control">
                <div id="account-name">Trịnh Văn Linh</div>
                <div id="account-options">
                    <a>Thông tin tài khoản</a>
                    <a>Đơn hàng</a>
                    <a>Đăng xuất</a>
                </div>
            </div>
        </div>
    </div>