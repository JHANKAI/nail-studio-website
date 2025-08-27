<?php
session_start();
include "../php/db_connect.php";

// 確認是否為已登入的管理員
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

// 檢查是否有傳入 id 且為數字
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("錯誤：未指定正確的使用者 ID。");
}

$user_id = intval($_GET['id']);

// 查詢該使用者資料
$sql = "SELECT id, name, phone, email, is_admin FROM users WHERE id = ?";
$params = [$user_id];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("查詢失敗：" . print_r(sqlsrv_errors(), true));
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$user) {
    die("找不到該使用者資料");
}

?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css"/>
    <title>美甲工作室後台</title>
</head>
<body>
    <div class="box">
    <header>
        <h1>美甲工作室後台</h1>
        <nav>
            <ul>
                <li><a href="../index.php">首頁</a></li>
 
                <li>
                    <a href="#">使用者管理</a>
                    <ul>
                        <li><a href="insert_user_form.php">新增使用者</a></li>
                        <li><a href="./php/select_users.php">查詢使用者</a></li>
                    </ul>
                </li>

                <li>
                    <a href="#">預約管理</a>
                    <ul>
                        <li><a href="appointment_search_form.php">查詢預約</a></li>
                        <li><a href="appointment_history.php">歷史預約</a></li>
                    </ul>
                </li>
                
                <li>
                    <a href="#">服務管理</a>
                    <ul>
                        <li><a href="insert_services_form.php">新增服務</a></li>
                        <li><a href="update_services_form.php">修改服務</a></li>
                        <li><a href="services.php">查詢服務</a></li>
                    </ul>
                </li>

                <li>
                    <a href="#">作品管理</a>
                    <ul>
                        <li><a href="insert_gallery_form.php">新增作品</a></li>
                        <li><a href="./php/gallery_list.php">查詢作品</a></li>
                    </ul>
                </li>

                <li><a href="../php/logout.php">登出</a></li>

            </ul>
        </nav>
    </header>
    </div> 

    <main>
        <form action="./php/update_user.php" method="POST">
            <div class="box2">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>" />
                    <div class="input_data">
                        <div>
                        <label for="name">姓名：</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required /><br />
                        </div><br />
                        
                        <div>
                        <label for="phone">電話：</label>
                        <input type="tel" id="phone" name="phone" pattern="09\d{8}" value="<?= htmlspecialchars($user['phone']) ?>" required/>
                        </div><br />

                        <div>
                        <label for="email">電子郵件：</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required /><br />
                        </div><br />
                        


                        <div>
                        <label>管理員身份：</label>

                        <input type="radio" id="admin_yes" name="is_admin" value="1" <?= $user['is_admin'] == 1 ? 'checked' : '' ?> />
                        <label for="admin_yes">是</label>
                        <input type="radio" id="admin_no" name="is_admin" value="0" <?= $user['is_admin'] == 0 ? 'checked' : '' ?> />
                        <label for="admin_no">否</label><br /><br />
                        </div>                       

                        <button type="submit">更新使用者資料</button>
                    </div>
            </div>
        </form>

    </main>

    <footer>
        <p>Copyright &copy; 2025 美甲工作室 All Rights Reserved.</p>
    </footer>
</body>
</html>
