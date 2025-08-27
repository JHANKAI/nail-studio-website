<?php
session_start();
include "db_connect.php";

// 取得登入者資訊
$user_id = $_SESSION['user_id'] ?? null;
$is_admin = $_SESSION['is_admin'] ?? 0;

// 接收 POST 資料
$appointment_id = $_POST['id'] ?? null;
$date = $_POST['date'] ?? null;
$time = $_POST['time'] ?? null;
$service_id = $_POST['service_id'] ?? null;

// 檢查必要欄位
if (!$user_id) die("未登入");
if (!$appointment_id) die("缺少預約 ID");
if (!$date || !$time) die("缺少預約日期或時段");
if (!$service_id) die("缺少服務項目");

// 合併時間並轉換格式
$datetime_str = $date . ' ' . $time;
$datetime = DateTime::createFromFormat('Y-m-d H:i', $datetime_str);
if (!$datetime) {
    die("時間格式錯誤");
}
$appointment_time = $datetime->format('Y-m-d H:i:s');

// 根據是否為管理員選擇 SQL 條件
if ($is_admin == 1) {
    // 管理員可更新任何預約
    $sql = "UPDATE appointments SET appointment_time = ?, service_id = ? WHERE id = ?";
    $params = [$appointment_time, $service_id, $appointment_id];
} else {
    // 一般使用者只能更新自己的預約
    $sql = "UPDATE appointments SET appointment_time = ?, service_id = ? WHERE id = ? AND user_id = ?";
    $params = [$appointment_time, $service_id, $appointment_id, $user_id];
}

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("更新失敗：" . print_r(sqlsrv_errors(), true));
} else {
    echo "
    <html>
    <head>
      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      <link rel='stylesheet' href='../../style.css'>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: 'success',
        title: '預約已變更！',
        confirmButtonText: '前往查詢預約記錄',
        customClass: {
            popup: 'my-popup-class',
            title: 'my-title-class',
            confirmButton: 'my-confirm-button'
        }
    }).then(() => {
        window.location.href = '../appointment_search_form.php';
    });
    </script>
    </body>
    </html>";
}

sqlsrv_close($conn);
?>
