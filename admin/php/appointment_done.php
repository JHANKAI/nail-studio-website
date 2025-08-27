<?php
session_start();
include "db_connect.php";

// 檢查是否登入 & 是否為管理員
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    die("未授權訪問");
}

$appointment_id = $_GET['id'] ?? null;

if (!$appointment_id) {
    die("缺少預約 ID");
}

$sql = "UPDATE appointments SET status = '已完成' WHERE id = ?";
$params = [$appointment_id];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("更新失敗：" . print_r(sqlsrv_errors(), true));
}else{
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
        title: '該項服務已完成!',
        confirmButtonText: '查詢預約記錄',
        customClass: {
            popup: 'my-popup-class',
            title: 'my-title-class',
            confirmButton: 'my-confirm-button'
        }
    }).then(() => {
        window.location.href = 'select_appointment.php';
    });
    </script>
    </body>
    </html>
    ";
}


sqlsrv_close($conn);

?>
