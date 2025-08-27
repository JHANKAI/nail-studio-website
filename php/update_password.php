<?php
include "db_connect.php";


// 接收 POST 資料
$email = $_POST['username'] ?? '';
$password_reset = $_POST['password_reset'] ?? '';
$password_reset_check = $_POST['password_reset_check'] ?? '';

// 基本驗證
if (empty($email) || empty($password_reset) || empty($password_reset_check)){
    die("請填寫所有欄位");
}

if ($password_reset !== $password_reset_check) {
    die("兩次輸入的密碼不一致！");
}

// 檢查email是否存在
$sql = "select id FROM users WHERE email = ? ";
$param = array($email);
$stmt = sqlsrv_query($conn, $sql, $param);

if ($stmt === false){
    die("查詢錯誤：".print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if ($row === null){
    die('帳號不存在');
}


// 密碼加密
$hashed_password = password_hash($password_reset, PASSWORD_DEFAULT);

// 更新密碼
$update_sql = "UPDATE users SET password = ? WHERE email = ? ";
$update_params = array($hashed_password, $email);
$update_stmt = sqlsrv_query($conn, $update_sql, $update_params);

if ($update_stmt === false){
    die('密碼更新失敗'. print_r(sqlsrv_errors(), true));
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
        title: '密碼已變更！',
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
    exit();
}

sqlsrv_close($conn);
?>
