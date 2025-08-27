<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $image_url = $_POST['image_url'] ?? '';

    // 刪除圖片檔案
    $filePath = '../images/gallery/' . $image_url;
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // 刪除資料庫紀錄
    $sql = "DELETE FROM galleries WHERE id = ?";
    $stmt = sqlsrv_query($conn, $sql, [$id]);

    if ($stmt === false) {
        die("刪除失敗：" . print_r(sqlsrv_errors(), true));
    }

    // 顯示刪除成功提示並跳轉
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
        title: '作品刪除成功！',
        confirmButtonText: '查看作品集',
        customClass: {
            popup: 'my-popup-class',
            title: 'my-title-class',
            confirmButton: 'my-confirm-button'
        }
    }).then(() => {
        window.location.href = 'gallery_list.php'; 
    });
    </script>
    </body>
    </html>
    ";

    exit;

} else {
    echo "非法請求方式";
}

sqlsrv_close($conn);
?>
