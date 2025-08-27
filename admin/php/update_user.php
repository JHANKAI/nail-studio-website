<?php
session_start();
include "../php/db_connect.php";

// 確認是否為已登入的管理員
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

// 檢查是否為 POST 請求
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $is_admin = isset($_POST['is_admin']) && $_POST['is_admin'] == '1' ? 1 : 0;

    // 驗證欄位
    if ($user_id <= 0 || $name === '' || $email === '') {
        die(htmlspecialchars("錯誤：請完整填寫所有必要欄位。", ENT_QUOTES, 'UTF-8'));
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(htmlspecialchars("錯誤：Email 格式不正確。", ENT_QUOTES, 'UTF-8'));
    }

    // 檢查是否已註冊過（email 重複）
    $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $check_params = [$email, $user_id];
    $check_stmt = sqlsrv_query($conn, $check_sql, $check_params);

    if ($check_stmt === false) {
        die(htmlspecialchars(print_r(sqlsrv_errors(), true), ENT_QUOTES, 'UTF-8'));
    }

    if (sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
        die(htmlspecialchars("此 Email 已被註冊，請使用其他帳號。", ENT_QUOTES, 'UTF-8'));
    }

    // 安全機制：防止自己降權
    if ($_SESSION['user_id'] == $user_id && $is_admin == 0) {
        die(htmlspecialchars("錯誤：不能將自己的管理員身份改為一般使用者。", ENT_QUOTES, 'UTF-8'));
    }

    // 更新資料
    $sql = "UPDATE users SET name = ?, phone = ?, email = ?, is_admin = ? WHERE id = ?";
    $params = [$name, $phone, $email, $is_admin, $user_id];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(htmlspecialchars("更新失敗：" . print_r(sqlsrv_errors(), true), ENT_QUOTES, 'UTF-8'));
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
        title: '使用者資料已成功更新!',
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
