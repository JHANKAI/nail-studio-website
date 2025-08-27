<?php
session_start();
include "db_connect.php";

// 確認是否為已登入的管理員
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../../index.php");
    exit;
}

// 檢查是否有傳入 id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("錯誤：未指定正確的使用者 ID。");
}

$user_id = intval($_GET['id']);

// 防止自己刪除自己（可選安全機制）
if ($_SESSION['user_id'] == $user_id) {
    die("錯誤：無法刪除自己。");
}

// 執行刪除
$sql = "DELETE FROM users WHERE id = ?";
$params = [$user_id];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("刪除失敗：" . print_r(sqlsrv_errors(), true));
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
        title: '該使用者已刪除!',
        confirmButtonText: '查詢使用者清單',
        customClass: {
            popup: 'my-popup-class',
            title: 'my-title-class',
            confirmButton: 'my-confirm-button'
        }
    }).then(() => {
        window.location.href = 'select_users.php';
    });
    </script>
    </body>
    </html>
    ";
}
sqlsrv_close($conn);
?>
