<?php
session_start();
include "../../php/db_connect.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = intval($_POST['price'] ?? 0);
    $duration = intval($_POST['duration'] ?? 0);

    if ($name === '' || $description === '' || $price <= 0 || $duration <= 0) {
        die("請正確填寫所有欄位，價格與時間需為正整數。");
    }

    // ✅ 先新增服務資料，取得 ID
    $sql = "INSERT INTO services (name, description, price, duration) OUTPUT INSERTED.id VALUES (?, ?, ?, ?)";
    $params = [$name, $description, $price, $duration];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("新增失敗：" . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $service_id = $row['id'];

    // ✅ 處理圖片上傳並轉成 id.jpg
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $tmpName = $_FILES['image']['tmp_name'];
        $imageInfo = getimagesize($tmpName);
        if ($imageInfo === false) {
            die("上傳的檔案不是有效的圖片！");
        }

        switch ($imageInfo['mime']) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($tmpName);
                break;
            case 'image/png':
                $image = imagecreatefrompng($tmpName);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($tmpName);
                break;
            default:
                die("只支援 JPG、PNG、GIF 格式的圖片！");
        }

        $targetPath = $uploadDir . $service_id . ".jpg";
        if (!imagejpeg($image, $targetPath, 85)) {
            die("圖片轉換成 JPG 失敗！");
        }
        imagedestroy($image);
        chmod($targetPath, 0644);
    }

    // ✅ 顯示成功提示
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
        title: '已新增新服務!',
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
} else {
    echo "非法請求方式";
}

sqlsrv_close($conn);
?>
