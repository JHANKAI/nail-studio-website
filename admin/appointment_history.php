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
    SELECT appointments.id,users.name AS user_name,appointments.appointment_time, services.name AS service_name, appointments.status
    FROM appointments
    JOIN services ON appointments.service_id = services.id
    JOIN users ON appointments.user_id = users.id
    WHERE (appointments.status = '已完成' OR appointments.status = '取消')
    ORDER BY appointments.appointment_time DESC, users.id
";

$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false){
    die("查詢錯誤: " . print_r(sqlsrv_errors(), true));
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
            <div class="box2">
                <h2>歷史預約記錄查詢結果</h2><br>
                <div class="table-container">
                    <table class="card">
                        <thead class="card__title">
                            <tr>
                                <th>預約人</th>
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
                                echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($dateStr) . "</td>";
                                echo "<td>" . htmlspecialchars($row['service_name']) . "</td>";
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
    </div>
</body>
</html>
