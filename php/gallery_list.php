<?php
session_start();
include "db_connect.php";

// 檢查是否有登入過
$user_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '訪客';


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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../style.css" />
    <title>美甲工作室 - 歷史預約</title>
</head>
<body>
    <div class="wrapper"> 
        <div class="box">
            <header>
                <h1>美甲工作室</h1>
                <nav>
                    <ul>
                        <li><a href="../index.php">首頁</a></li>

                        <li>
                            <a href="#">服務項目</a>
                            <ul>
                                <li><a href="../services.php">價目表</a></li>
                                <li><a href="gallery_list.php">作品集</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="#">預約管理</a>
                            <ul>
                                <li><a href="../booking_form.php">新增預約</a></li>
                                <li><a href="../appointment_search_form.php">查詢預約</a></li>
                                <li><a href="#">修改預約</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">會員專區</a>
                            <ul>
                                <li><a href="../profile_form.php">修改個人資料</a></li>
                                <li><a href="../password_reset_form.php">修改密碼</a></li>
                                <li><a href="../appointment_history.php">歷史預約</a></li>
                            </ul>
                        </li>
                        <?php if ($user_logged_in): ?>
                            <li><a href="logout.php">登出</a></li>
                        <?php else: ?>
                            <li><a href="../login_form.php">登入</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </header>
        </div>

        <main>

            <h2 style="text-align: center;">作品集列表</h2>

            <div class="box3">
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                    <div class="card_gallery_for_customer">
                        <img src="../images/gallery/<?php echo htmlspecialchars($row['image_url']); ?>" width="200"><br>
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
