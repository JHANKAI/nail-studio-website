<?php 
session_start();
include "./db_connect.php";

$user_id = $_SESSION['user_id'];
$update_name = $_POST['name'] ?? '';
$update_email = $_POST['username'] ?? '';
$update_phone = $_POST['phone'] ?? '';

// 基本驗證
if (empty($update_name) || empty($update_email) || empty($update_phone)) {
    die("請完整填寫所有欄位");
}

if (!filter_var($update_email, FILTER_VALIDATE_EMAIL)) {
    die(htmlspecialchars("錯誤：Email 格式不正確。", ENT_QUOTES, 'UTF-8'));
}

// 檢查email 是否註冊過
$sql_check = "SELECT id FROM users WHERE email = ? AND id != ?";
$params_check = [$update_email, $user_id];
$stmt_check = sqlsrv_query($conn, $sql_check, $params_check);


if ($stmt_check === false) {
    die("查詢錯誤: " . print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);
if ($row){
    die("此電子郵件已被其他帳號使用");
}

// 更新資料庫資料
$sql_update = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
$params = array($update_name, $update_email, $update_phone, $user_id);
$stmt = sqlsrv_query($conn, $sql_update ,$params);
if ($stmt === false){
    die("更新失敗".print_r(sqlsrv_errors(), true));
}else{
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
        title: '個人資料已更新！',
        confirmButtonText: '前往首頁',
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