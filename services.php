<!-- index.php -->
<?php
session_start(); // 啟用 session 機制
include "./php/db_connect.php";

// 檢查是否有登入過
$user_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '訪客';

$services = [];


$sql = "select * FROM services";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false){
    die("查詢錯誤：".print_r(sqlsrv_errors(), true));
}
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
    $services[] = $row;
}


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
            <div class="box2">
                <h2>我們提供的服務</h2><br>
                <div class="table-container">
                    <table class="card">
                        <thead class="card__title">
                            <tr>
                                <th>服務名稱</th>
                                <th>說明</th>
                                <th>價格</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                                <tr class='item'>
                                    <td><?= htmlspecialchars($service['name']) ?></td>
                                    <td><?= htmlspecialchars($service['description']) ?></td>
                                    <td>NT$ <?= htmlspecialchars(intval($service['price'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>    
            </div> 
        </main>

        <footer>
            <p>Copyright &copy; 2025 美甲工作室 All Rights Reserved.</p>
        </footer>
    </div>      
</body>
</html>