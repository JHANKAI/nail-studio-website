<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

// 取得所有作品
$sql = "SELECT * FROM galleries ORDER BY created_at DESC";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die("查詢失敗：" . print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css"/>
    <title>美甲工作室後台</title>
</head>
<body>
    <div class="wrapper"> 
        <div class="box">
        <header>
            <h1>美甲工作室後台</h1>
            <nav>
                <ul>
                    <li><a href="../admin.php">首頁</a></li>
    
                    <li>
                        <a href="#">使用者管理</a>
                        <ul>
                            <li><a href="../insert_user_form.php">新增使用者</a></li>
                            <li><a href="select_users.php">查詢使用者</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">預約管理</a>
                        <ul>
                            <li><a href="../appointment_search_form.php">查詢預約</a></li>
                            <li><a href="../appointment_history.php">歷史預約</a></li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="#">服務管理</a>
                        <ul>
                            <li><a href="../insert_services_form.php">新增服務</a></li>
                            <li><a href="../update_services_form.php">修改服務</a></li>
                            <li><a href="../services.php">查詢服務</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">作品管理</a>
                        <ul>
                            <li><a href="../insert_gallery_form.php">新增作品</a></li>
                            <li><a href="gallery_list.php">查詢作品</a></li>
                        </ul>
                    </li>

                    <li><a href="../../php/logout.php">登出</a></li>

                </ul>
            </nav>
        </header>
        </div> 

        <main>

            <h2 style="text-align: center;">作品集列表</h2>

            <div class="box3">
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                    <div class="card_gallery">
                        <img src="../../images/gallery/<?php echo htmlspecialchars($row['image_url']); ?>" width="200"><br>

                        <form action="delete_gallery.php" method="post" onsubmit="return confirm('確定要刪除這筆作品嗎？');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($row['image_url']); ?>">
                            <button type="submit">刪除</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>

        <footer>
            <p>Copyright &copy; 2025 美甲工作室 All Rights Reserved.</p>
        </footer>
    </div>
</body>
</html>


<?php sqlsrv_close($conn); ?>
