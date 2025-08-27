<?php
session_start();
include "db_connect.php";

// 確保使用者已登入
if (!isset($_SESSION['user_id'])) {
    die("請先登入");
}
$user_logged_in = isset($_SESSION['user_id']);

// 接收前端 POST 資料（分開接收 date 與 time）
$user_id = $_SESSION['user_id'];
$service_id = $_POST['service_id'] ?? '';
$date = $_POST['date'] ?? '';       // 例如：2025-05-20
$time = $_POST['time'] ?? '';       // 例如：14:30
$note = $_POST['note'] ?? '無';

if (empty($service_id) || empty($date) || empty($time)) {
    die("請完整填寫所有欄位");
}

// 組合成 SQL Server 的 DATETIME 格式，例如：2025-05-20 14:30:00
$appointment_time = $date . ' ' . $time . ':00';



// 驗證時間格式
$dateTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $appointment_time);
if (!$dateTimeObj) {
    die("預約時間格式錯誤");
}
$appointment_time = $dateTimeObj->format('Y-m-d H:i:s');

// 先檢查該時間是否已有預約（因只有一位美甲師）
$sql_check = "SELECT COUNT(*) AS cnt FROM appointments WHERE appointment_time = ?";
$params_check = [$appointment_time];
$stmt_check = sqlsrv_query($conn, $sql_check, $params_check);
if ($stmt_check === false) {
    die("查詢錯誤: " . print_r(sqlsrv_errors(), true));
}
$row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);
if ($row['cnt'] > 0) {
    die("該時段已被預約，請選擇其他時間");
}

// 交易開始
sqlsrv_begin_transaction($conn);

// 沒重複，執行新增預約
$sql = "INSERT INTO appointments (user_id, service_id, appointment_time, note) 
        VALUES (?, ?, ?, ?)";
$params = [$user_id, $service_id, $appointment_time, $note];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    sqlsrv_rollback($conn);
    die("預約失敗：".print_r(sqlsrv_errors(), true));
} else {
    sqlsrv_commit($conn);
    echo "
    <html>
    <head>
      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      <link rel='stylesheet' href='../style.css'>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: 'success',
        title: '預約成功！',
        confirmButtonText: '前往主頁',
        customClass: {
            popup: 'my-popup-class',
            title: 'my-title-class',
            confirmButton: 'my-confirm-button'
        }
    }).then(() => {
        window.location.href = '../index.php';
    });
    </script>
    </body>
    </html>
    ";
}

sqlsrv_close($conn);
?>
