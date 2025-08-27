<?php
session_start();
include "db_connect.php";

$user_logged_in = isset($_SESSION['user_id']);
if (!$user_logged_in) {
    header("Location: login_form.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$date = $_POST['date'] ?? '';


// 沒輸入日期，則查詢全部
// 有輸入日期，查詢當天
$sql_for_all = "
    SELECT appointments.id,appointments.appointment_time, services.name, appointments.status
    FROM appointments
    JOIN services ON appointments.service_id = services.id
    WHERE appointments.status = '已預約'
    AND appointments.user_id = ?
    ORDER BY appointments.appointment_time
";

$sql_for_the_day = "
    SELECT appointments.id,appointments.appointment_time, services.name AS service_name, appointments.status
    FROM appointments
    JOIN services ON appointments.service_id = services.id
    WHERE appointments.status = '已預約'
    AND appointments.user_id = ?
    AND appointment_time >= ? AND appointment_time < ?
    ORDER BY appointments.appointment_time
";

if (empty($date)) {
    $params_for_all = [$user_id];
    $stmt_all = sqlsrv_query($conn, $sql_for_all, $params_for_all);
    if ($stmt_all === false){
        die("查詢錯誤: " . print_r(sqlsrv_errors(), true));
    }
} else {
    $start_date = $date . " 00:00:00";
    $dt = new DateTime($date);
    $dt->modify('+1 day');
    $end_date = $dt->format('Y-m-d') . " 00:00:00";

    $params_for_the_day = [$user_id, $start_date, $end_date];
    $stmt_the_day = sqlsrv_query($conn, $sql_for_the_day, $params_for_the_day);
    if ($stmt_the_day === false){
        die("查詢錯誤: " . print_r(sqlsrv_errors(), true));
    }
}
?>




<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../style.css" />
    <title>美甲工作室 - 歷史預約</title>
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
                                <li><a href="gallery_list.php">作品集</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="#">預約管理</a>
                            <ul>
                                <li><a href="../booking_form.php">新增預約</a></li>
                                <li><a href="../appointment_search_form.php">查詢預約</a></li>
                                <li><a href="#">修改預約</a></li>
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
                        <?php if ($user_logged_in): ?>
                            <li><a href="logout.php">登出</a></li>
                        <?php else: ?>
                            <li><a href="login_form.php">登入</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </header>
        </div>

        <main>
            <div class="box2">
                <h2>預約記錄查詢結果</h2><br>
                <div class="table-container">
                    <table class="card">
                        <thead class="card__title">
                            <tr>
                                <th>預約日期</th>
                                <th>服務項目</th>
                                <th>狀態</th>
                                <th>修改預約</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $has_result = false; // 預設沒資料
                            if (empty($date)){
                                while ($row = sqlsrv_fetch_array($stmt_all, SQLSRV_FETCH_ASSOC)) {
                                        $has_result = true;
                                        $dateStr = ($row['appointment_time'] instanceof DateTime)
                                            ? $row['appointment_time']->format('Y-m-d H:i')
                                            : '未知時間';
                                        echo "<tr class='item'>";
                                        echo "<td >" . htmlspecialchars($dateStr) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo "<td><a href='../appointment_update_form.php?id=" . urlencode($row['id']) . "'>修改</a></td>";
                                        echo "</tr>";
                                        }
                            }else{
                                while ($row = sqlsrv_fetch_array($stmt_the_day, SQLSRV_FETCH_ASSOC)) {
                                    $has_result = true;
                                    $dateStr = ($row['appointment_time'] instanceof DateTime)
                                        ? $row['appointment_time']->format('Y-m-d H:i')
                                        : '未知時間';
                                    echo "<tr class='item'>";
                                    echo "<td>" . htmlspecialchars($dateStr) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "<td><a href='../appointment_update_form.php?id=" . urlencode($row['id']) . "'>修改</a></td>";
                                    echo "</tr>";
                                    }
                            }

                            if (!$has_result) {
                                echo "<tr class='item'><td colspan='4'>目前沒有預約紀錄</td></tr>";
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

