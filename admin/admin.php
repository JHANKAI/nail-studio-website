<?php
session_start();

// 確認是否登入且為管理員
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // 非管理員導向首頁或登入頁
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css"/>
    <title>美甲工作室後台</title>
</head>
<body>
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

    <main class="index_main">
        <section class="news">
            <h2>美甲工作室全新開幕！</h2>
            <p>歡迎來到我們全新開幕的美甲工作室——專屬於妳的指尖藝術殿堂！<br>
                我們提供多元的美甲服務，包含手部凝膠、足部保養、創意彩繪、簡約法式與客製設計，<br>
                讓妳在舒適的環境中，享受專業貼心的呵護。</p>
        </section>

        <section class="shop">
            <img src="../images/info.jpg" alt="美甲作品">

            <div class="info">
            <h2>開幕限定優惠</h2>
            <p>凡首次預約，即享 9 折優惠，再送精美護手霜一瓶！</p>

            <h2>我們的承諾</h2>
            <p> 使用高品質材料，保障健康安全<br>
                專業美甲師團隊，打造專屬妳的風格<br>
                舒適的空間，享受美麗與放鬆的時光
            </p>


            </div>

        </section>

    </main>

    <footer>
        <p>Copyright &copy; 2025 美甲工作室 All Rights Reserved.</p>
    </footer>
</body>
</html>
