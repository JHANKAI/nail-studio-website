<?php
session_start(); // 啟動 session，存取或創建 session 資料
include "db_connect.php";

// 取得使用者輸入
$email = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)){
    die("請輸入帳號與密碼");
}

// 從資料庫撈出該帳號的密碼,使用者ID,is_admin
$sql = "SELECT id, password, is_admin FROM users WHERE email = ?";
$parms = array($email);
$stmt = sqlsrv_query($conn, $sql, $parms);

if ($stmt === false){
    die(print_r(sqlsrv_errors(), true));
}

// 取出資料
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if ($row === null){
    die("帳號不存在");
}

// 使用者資料
$stored_password = $row['password'];
$user_id = $row['id'];
$is_admin = $row['is_admin']; // ✅ 抓出 is_admin 欄位

// 用 password_verify 驗證密碼是否正確
if (password_verify($password, $stored_password)) {
    // 登入成功，設定 session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $email;
    $_SESSION['is_admin'] = $is_admin; 
    
    // 導向到預約頁或主頁
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
        title: '登入成功！',
        confirmButtonText: '前往預約',
        customClass: {
            popup: 'my-popup-class',
            title: 'my-title-class',
            confirmButton: 'my-confirm-button'
        }
    }).then(() => {
        window.location.href = '../booking_form.php';
    });
    </script>
    </body>
    </html>
";
    exit();
} else {
    // 密碼錯誤跳視窗並回登入頁
    echo "
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <link rel='stylesheet' href='../style.css'>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: 'error',
        title: '密碼錯誤，登入失敗！',
        confirmButtonText: '確認',
        customClass: {
            popup: 'my-popup-class',
            title: 'my-title-class',
            confirmButton: 'my-confirm-button'
        }
    }).then(() => {
        window.location.href = '../login_form.php';
    });
    </script>
    </body>
    </html>
";
    exit();
}

sqlsrv_close($conn);
?>




