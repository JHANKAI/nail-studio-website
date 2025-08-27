<?php
session_start();
include "../php/db_connect.php";

// 確認是否為已登入的管理員
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $is_admin = isset($_POST['is_admin']) && $_POST['is_admin'] == '1' ? 1 : 0;

    // 基本驗證
    if ($name === '' || $email === '' || $password === '') {
        die(htmlspecialchars("錯誤：請完整填寫姓名、Email 及密碼。", ENT_QUOTES, 'UTF-8'));
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die(htmlspecialchars("錯誤：Email 格式不正確。", ENT_QUOTES, 'UTF-8'));
    }

    // 檢查 Email 是否已存在
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = sqlsrv_query($conn, $check_sql, [$email]);
    if ($check_stmt === false) {
        die(htmlspecialchars(print_r(sqlsrv_errors(), true), ENT_QUOTES, 'UTF-8'));
    }
    if (sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
        die(htmlspecialchars("此 Email 已被註冊，請使用其他帳號。", ENT_QUOTES, 'UTF-8'));
    }

    // 密碼加密
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 新增使用者
    $sql = "INSERT INTO users (name, phone, email, password, is_admin) VALUES (?, ?, ?, ?, ?)";
    $params = [$name, $phone, $email, $password_hash, $is_admin];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(htmlspecialchars("新增失敗：" . print_r(sqlsrv_errors(), true), ENT_QUOTES, 'UTF-8'));
    }

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
        title: '使用者新增成功!',
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

} else {
    echo htmlspecialchars("非法請求方式", ENT_QUOTES, 'UTF-8');
}

sqlsrv_close($conn);
?>
