<?php
session_start();
include "./php/db_connect.php";

// 必須先登入
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_logged_in = isset($_SESSION['user_id']);


// 從資料庫撈使用者資料
$sql = "select name, email, phone FROM users WHERE id = ? ";
$params = array($user_id);
$stmt = sqlsrv_query($conn, $sql, $params);
$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

sqlsrv_close($conn);
?>


<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"/>
    <title>美甲工作室</title>
</head>
<body>
    <div class="wrapper">
        <div class="box">
        <header>
            <h1>美甲工作室</h1>
            <nav>
                <ul>
                    <li><a href="index.php">首頁</a></li>
                    
                    <li>
                        <a href="#">服務項目</a>
                        <ul>
                            <li><a href="services.php">價目表</a></li>
                            <li><a href="./php/gallery_list.php">作品集</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">預約管理</a>
                        <ul>
                            <li><a href="./booking_form.php">新增預約</a></li>
                            <li><a href="./appointment_search_form.php">查詢預約</a></li>
                        </ul>
                    </li>
                    

                    <li>
                        <a href="#">會員專區</a>
                        <ul>
                            <li><a href="profile_form.php">修改個人資料</a></li>
                            <li><a href="password_reset_form.php">修改密碼</a></li>
                            <li><a href="appointment_history.php">歷史預約</a></li>
                        </ul>
                    </li>
                    <!-- ✅ 新增：管理員看到後台連結 -->
                    <?php if ($user_logged_in && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <li><a href="./admin/admin.php">後台管理</a></li>
                        
                    <?php endif; ?>

                    <!-- 登出 / 登入 -->
                    <?php if ($user_logged_in):?> 
                        <li><a href="./php/logout.php">登出</a></li>
                    <?php else: ?>
                        <li><a href="login_form.php">登入</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>
        </div>  

        <main>
            <form method="post" action="./php/update_profile.php">
            <div class="box2">
                <div class="input_data">
                    <h2>修改個人資料</h2><br>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br>
                    <input type="email" name="username" value="<?= htmlspecialchars($user['email']) ?>" required><br>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required><br>
                    <button type="submit">更新</button>
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