<?php
session_start();
include "db_connect.php";

// 確保使用者是管理員（可選的保護機制）
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

// 確保是 POST 請求
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 圖片上傳處理
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../images/gallery/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $tmpName = $_FILES['image']['tmp_name'];
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_', true) . '.' . $ext;  // 避免檔名衝突
        $targetPath = $uploadDir . $filename;

        // 確認圖片為有效格式
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $imageInfo = getimagesize($tmpName);
        $mime = $imageInfo['mime'];
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
            die("只支援 JPG、PNG、GIF 圖片格式！");
        }


        if (!move_uploaded_file($tmpName, $targetPath)) {
            die("圖片上傳失敗！");
        }

        chmod($targetPath, 0644); // 設定權限

        // 處理描述
        $description = trim($_POST['description'] ?? '');

        // 儲存路徑與描述到資料庫
        $sql = "INSERT INTO galleries (image_url, description) VALUES (?, ?)";
        $params = [$filename, $description];
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die("新增失敗：" . print_r(sqlsrv_errors(), true));
        }

        // ✅ 新增成功訊息
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
            title: '作品已上傳成功！',
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
    } else {
        die("請選擇一張圖片！");
    }
} else {
    echo "非法請求方式";
}

sqlsrv_close($conn);
?>
