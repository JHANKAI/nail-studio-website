<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <title>新增使用者</title>
</head>
<body>








<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css"/>
    <title>美甲工作室後台</title>
</head>
<body>
    <div class="wrapper">
        <div class="box">
        <header>
            <h1>美甲工作室後台</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">首頁</a></li>
    
                    <li>
                        <a href="#">使用者管理</a>
                        <ul>
                            <li><a href="insert_user_form.php">新增使用者</a></li>
                            <li><a href="./php/select_users.php">查詢使用者</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">預約管理</a>
                        <ul>
                            <li><a href="appointment_search_form.php">查詢預約</a></li>
                            <li><a href="appointment_history.php">歷史預約</a></li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="#">服務管理</a>
                        <ul>
                            <li><a href="insert_services_form.php">新增服務</a></li>
                            <li><a href="update_services_form.php">修改服務</a></li>
                            <li><a href="services.php">查詢服務</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">作品管理</a>
                        <ul>
                            <li><a href="insert_gallery_form.php">新增作品</a></li>
                            <li><a href="./php/gallery_list.php">查詢作品</a></li>
                        </ul>
                    </li>

                    <li><a href="../php/logout.php">登出</a></li>

                </ul>
            </nav>
        </header>
        </div> 


        <main>
    
        <form action="./php/insert_user.php" method="POST">

            <div class = "box2">
                <div class = "input_data_2">
                    <h2>新增使用者</h2><br>

                    <div>
                    <label for="name"></label>
                    姓名：<input class="input_3" type="text" id="name" name="name" required />
                    </div><br>

                    <div>
                    電話：<input class="input_3" type="tel" id="phone" name="phone" placeholder="09xxxxxxxx" pattern="09\d{8}"/>
                    </div><br>

                    <div>
                    Email：<input class="input_3" type="email" id="email" name="email" required />
                    </div><br>

                    <div>
            
                    密碼：<input class="input_3" type="password" id="password" name="password" required />
                    </div><br>

                    <div> 
                        <label>管理員身份：</label><br>
                        <div class="radio-inputs">
                            <label class="radio">
                                <input type="radio" name="is_admin" value="1">
                                <span class="name">是</span>
                            </label>
                            <label class="radio">
                                <input type="radio" name="is_admin" value="0" checked>
                                <span class="name">否</span>
                            </label>
                        </div>
                    </div><br><br>

                    <button type="submit">新增使用者</button> 
                </div>
            </div>
        </form>
        </main>

        <footer>
            <p>Copyright &copy; 2025 美甲工作室 All Rights Reserved.</p>
        </footer>
    </div>    
</body>
</html>
