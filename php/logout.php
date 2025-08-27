<?php
session_start();
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// 跳出提示視窗並導回登入頁
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
    title: '已登出！',
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

?>
