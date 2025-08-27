<?php
session_start();
include "./php/db_connect.php";

// 確認管理員身份
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}

// 檢查是否有 id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("錯誤：未指定正確的預約 ID。");
}

$id = intval($_GET['id']);

// 讀取該筆預約資料，連接服務名稱與使用者名稱
$sql = "
SELECT appointments.*, users.name AS user_name, services.name AS service_name
FROM appointments
JOIN users ON appointments.user_id = users.id
JOIN services ON appointments.service_id = services.id
WHERE appointments.id = ?
";

$params = [$id];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("查詢失敗：" . print_r(sqlsrv_errors(), true));
}

$appointment = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$appointment) {
    die("找不到該筆預約資料。");
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 處理預約時間，拆成日期和時段
$appointment_date = $appointment['appointment_time'] instanceof DateTime ? $appointment['appointment_time']->format('Y-m-d') : '';
$appointment_time = $appointment['appointment_time'] instanceof DateTime ? $appointment['appointment_time']->format('H:i') : '';
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
                        // 抓所有服務列表做成下拉選單
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

<?php sqlsrv_close($conn); ?>
