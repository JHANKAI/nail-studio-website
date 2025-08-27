<?php
session_start();
include "db_connect.php";

// 驗證登入和管理員
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$price = $_POST['price'] ?? '';
$duration = $_POST['duration'] ?? '';

if (!$id || $name === '' || $description === '' || $price === '' || $duration === '') {
    die("請填寫所有欄位");
}

if (!ctype_digit($price)) {
    die("價格必須為整數");
}



$sql = "UPDATE services SET name = ?, description = ?, price = ?, duration = ? WHERE id = ?";
$params = [$name, $description, $price, $duration, $id];
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
        title: '已更新該項服務!',
        confirmButtonText: '查詢現有服務',
        customClass: {
            popup: 'my-popup-class',
            title: 'my-title-class',
            confirmButton: 'my-confirm-button'
        }
    }).then(() => {
        window.location.href = '../services.php';
    });
    </script>
    </body>
    </html>
    ";
}

sqlsrv_close($conn);

?>
