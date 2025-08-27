<?php
session_start();
include "db_connect.php";

$user_logged_in = isset($_SESSION['user_id']);

// 確認是否登入且為管理員
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // 非管理員導向首頁或登入頁
    header("Location: index.php");
    exit;
}


$date = $_POST['date'] ?? '';


// 沒輸入日期，則查詢全部
// 有輸入日期，查詢當天
$sql_for_all = "
    SELECT appointments.id,users.name AS user_name,appointments.appointment_time, services.name AS service_name, appointments.status
    FROM appointments
    JOIN services ON appointments.service_id = services.id
    JOIN users ON appointments.user_id = users.id
    WHERE appointments.status = '已預約'
    ORDER BY appointments.appointment_time, users.id
";

$sql_for_the_day = "
    SELECT appointments.id,users.name AS user_name,appointments.appointment_time, services.name AS service_name, appointments.status
    FROM appointments
    JOIN services ON appointments.service_id = services.id
    JOIN users ON appointments.user_id = users.id
    WHERE appointments.status = '已預約'
    AND appointment_time >= ? AND appointment_time < ?
    ORDER BY appointments.appointment_time, users.id
";

if (empty($date)) {
    $stmt_all = sqlsrv_query($conn, $sql_for_all);
    if ($stmt_all === false){
        die("查詢錯誤: " . print_r(sqlsrv_errors(), true));
    }
} else {
    $start_date = $date . " 00:00:00";
    $dt = new DateTime($date);
    $dt->modify('+1 day');
    $end_date = $dt->format('Y-m-d') . " 00:00:00";

    $params_for_the_day = [$start_date, $end_date];
    $stmt_the_day = sqlsrv_query($conn, $sql_for_the_day, $params_for_the_day);
    if ($stmt_the_day === false){
        die("查詢錯誤: " . print_r(sqlsrv_errors(), true));
    }
}
?>




<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css"/>
    <title>美甲工作室後台</title>
</head>
<body>
    <div class="wrapper"> 
        <div class="box">
        <header>
            <h1>美甲工作室後台</h1>
            <nav>
                <ul>
                    <li><a href="../admin.php">首頁</a></li>
    
                    <li>
                        <a href="#">使用者管理</a>
                        <ul>
                            <li><a href="../insert_user_form.php">新增使用者</a></li>
                            <li><a href="select_users.php">查詢使用者</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">預約管理</a>
                        <ul>
                            <li><a href="../appointment_search_form.php">查詢預約</a></li>
                            <li><a href="../appointment_history.php">歷史預約</a></li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="#">服務管理</a>
                        <ul>
                            <li><a href="../insert_services_form.php">新增服務</a></li>
                            <li><a href="../update_services_form.php">修改服務</a></li>
                            <li><a href="../services.php">查詢服務</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">作品管理</a>
                        <ul>
                            <li><a href="../insert_gallery_form.php">新增作品</a></li>
                            <li><a href="gallery_list.php">查詢作品</a></li>
                        </ul>
                    </li>

                    <li><a href="../../php/logout.php">登出</a></li>

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
                                <th>預約人</th>
                                <th>預約日期</th>
                                <th>服務項目</th>
                                <th>狀態</th>
                                <th>修改預約</th>
                                <th>取消預約</th>
                                <th>完成服務</th>
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
                                        echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($dateStr) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['service_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo "<td><a href='../update_appointment_form.php?id=" . urlencode($row['id']) . "'>修改</a></td>";
                                        echo "<td><a href='appointment_delete.php?id={$row['id']}' onclick='return confirm(\"確定要標記為取消嗎？\");'>取消</a></td>";
                                        echo "<td><a href='appointment_done.php?id={$row['id']}' onclick='return confirm(\"確定要標記為已完成嗎？\");'>完成</a></td>";
                                        echo "</tr>";
                                        }
                            }else{
                                while ($row = sqlsrv_fetch_array($stmt_the_day, SQLSRV_FETCH_ASSOC)) {
                                    $has_result = true;
                                    $dateStr = ($row['appointment_time'] instanceof DateTime)
                                        ? $row['appointment_time']->format('Y-m-d H:i')
                                        : '未知時間';
                                    echo "<tr class='item'>";
                                    echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($dateStr) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['service_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "<td><a href='../update_appointment_form.php?id=" . urlencode($row['id']) . "'>修改</a></td>";
                                    echo "<td><a href='appointment_delete.php?id={$row['id']}' onclick='return confirm(\"確定要標記為取消嗎？\");'>取消</a></td>";
                                    echo "<td><a href='appointment_done.php?id={$row['id']}' onclick='return confirm(\"確定要標記為已完成嗎？\");'>完成</a></td>";
                                    echo "</tr>";
                                    }
                            }

                            if (!$has_result) {
                                echo "<tr class='item'><td colspan='7'>目前沒有預約紀錄</td></tr>";
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

