<?php
session_start();
include "./php/db_connect.php";

// 定義轉義函數
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// 檢查登入
$user_logged_in = isset($_SESSION['user_id']);
if (!$user_logged_in) {
    header("Location: login_form.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$appointment_id = $_GET['id'] ?? null;

if (!$appointment_id || !$user_id) {
    die("參數錯誤或未登入");
}

// 查詢預約資料
$sql = "SELECT a.id, a.appointment_time, a.service_id, u.name AS user_name
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        WHERE a.id = ? AND a.user_id = ?";
$params = [$appointment_id, $user_id];
$query = sqlsrv_query($conn, $sql, $params);

if ($query === false) {
    die("查詢錯誤：" . print_r(sqlsrv_errors(), true));
}

$appointment = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
if ($appointment === false) {
    die("找不到預約資料");
}

// 處理時間格式
if ($appointment['appointment_time'] instanceof DateTime) {
    $appointment_date = $appointment['appointment_time']->format('Y-m-d');
    $appointment_time = $appointment['appointment_time']->format('H:i');
} else {
    $appointment_date = '';
    $appointment_time = '';
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
                    <?php if ($user_logged_in && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <li><a href="./admin/admin.php">後台管理</a></li>
                    <?php endif; ?>
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
                <div class="input_data">
                <h2>修改預約</h2><br>

                <form action="./php/update_appointment.php" method="post">
                    <input type="hidden" name="id" value="<?= h($appointment['id']) ?>" />

                    <p>預約人：<?= h($appointment['user_name']) ?></p><br><br>

                    <p>
                        <label for="date">預約日期：</label>
                        <input class="input_2" type="date" name="date" id="date" 
                            value="<?= h($appointment_date) ?>" required>
                    </p><br><br>

                    <p>
                        <label for="time">預約時段：</label>
                        <select name="time" id="time" required>
                            <option value="">--請選擇想預約的時段--</option>
                            <?php
                            $times = ['12:00', '14:00', '16:00', '18:00', '20:00'];
                            foreach ($times as $time) {
                                $selected = ($time === $appointment_time) ? 'selected' : '';
                                echo "<option value=\"$time\" $selected>$time</option>";
                            }
                            ?>
                        </select>
                    </p>
                    <br/><br/>

                    <label for="service_id">服務項目：</label>
                    <select id="service_id" name="service_id" required>
                        <?php
                        $services_stmt = sqlsrv_query($conn, "SELECT id, name FROM services");
                        while ($service = sqlsrv_fetch_array($services_stmt, SQLSRV_FETCH_ASSOC)) {
                            $selected = ($service['id'] == $appointment['service_id']) ? 'selected' : '';
                            echo '<option value="' . h($service['id']) . '" ' . $selected . '>' . h($service['name']) . '</option>';
                        }
                        ?>
                    </select><br><br>

                    <div style="text-align: center;">
                        <button type="submit">更新預約</button><br><br>
                    </div>
                </form>

                <p><a href="./php/select_appointment.php">回預約列表</a></p>

                </div>
            </div>
        </main>

        <footer>
            <p>Copyright &copy; 2025 美甲工作室 All Rights Reserved.</p>
        </footer>
    </div>
</body>
</html>
