<?php
include "db_connect.php"; // 連線檔案，請確保這檔案已經正確連接 SQL Server

// 接收 POST 資料
$name = $_POST['name'] ?? '';
$email = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$password_check = $_POST['password_check'] ?? '';
$phone = $_POST['phone'] ?? '';

// 基本驗證
if (empty($name) || empty($email) || empty($password) || empty($password_check)) {
    die("請填寫所有欄位");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(htmlspecialchars("錯誤：Email 格式不正確。", ENT_QUOTES, 'UTF-8'));
}

if ($password !== $password_check) {
    die("兩次輸入的密碼不一致！");
}

// 檢查是否已註冊過（email 重複）
$check_sql = "SELECT id FROM users WHERE email = ?";
$check_stmt = sqlsrv_query($conn, $check_sql, array($email));

if ($check_stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

if (sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
    die("此 Email 已被註冊，請使用其他帳號。");
}

// 密碼加密
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 寫入資料庫
$insert_sql = "INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)";
$params = array($name, $phone, $email, $hashed_password);
$stmt = sqlsrv_query($conn, $insert_sql, $params);

if ($stmt === false) {
    die("註冊失敗：" . print_r(sqlsrv_errors(), true));
} else {
    // 只輸出 SweetAlert2 的程式碼，不帶 style 或 head
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
        title: '註冊成功！',
        text: '歡迎加入我們 🎉',
        confirmButtonText: '前往登入',
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
}


sqlsrv_close($conn);
?>


