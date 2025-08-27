<?php
session_start(); // 啟用 session

include "./php/db_connect.php"; // 資料庫連線設定

// 檢查使用者是否已登入
$user_logged_in = isset($_SESSION['user_id']);
if (!$user_logged_in) {
    header("Location: login_form.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT appointments.appointment_time, services.name, appointments.status
    FROM appointments
    JOIN services ON appointments.service_id = services.id
    WHERE (appointments.status = '已完成' OR appointments.status = '取消')
    AND appointments.user_id = ?
    ORDER BY appointments.appointment_time DESC
";

$params = [$user_id];
$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false){
    die("查詢錯誤: " . print_r(sqlsrv_errors(), true));
}

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
                <h2>歷史預約記錄查詢結果</h2><br>
                <div class="table-container">    
                    <table class="card">
                        <thead class="card__title">
                            <tr>
                                <th>預約日期</th>
                                <th>服務項目</th>
                                <th>狀態</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $has_result = false; // 預設沒資料

                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                $has_result = true;
                                $dateStr = ($row['appointment_time'] instanceof DateTime)
                                    ? $row['appointment_time']->format('Y-m-d H:i')
                                    : '未知時間';

                                echo "<tr class='item'>";
                                echo "<td>" . htmlspecialchars($dateStr) . "</td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                echo "</tr>";
                            }

                            if (!$has_result) {
                                echo "<tr><td colspan='3'>目前沒有歷史預約紀錄</td></tr>";
                            }

                            // 查詢完成後再關閉連線
                            sqlsrv_close($conn);
                            ?>
                        </tbody>
                    </table>
                </div> 
            </div>
        </main>

        <footer>
            <p>Copyright &copy; 2025 美甲工作室 All Rights Reserved.</p>
        </footer>
    </main>  
</body>
</html>
