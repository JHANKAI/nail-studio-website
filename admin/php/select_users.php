<?php
session_start();
include "db_connect.php";

// 確認是否為管理員
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

// 取得搜尋關鍵字（name/email）
$keyword = trim($_GET['keyword'] ?? '');

// SQL 查詢語句（使用參數化查詢避免 SQL Injection）
$sql = "SELECT id, name, phone, email, is_admin, created_at FROM users";
$params = [];

if ($keyword !== '') {
    $sql .= " WHERE name LIKE ? OR email LIKE ?";
    $keyword_param = "%" . $keyword . "%";
    $params = [$keyword_param, $keyword_param];
}

$sql .= " ORDER BY created_at DESC";

$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
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
            <h2 class="visually-hidden">使用者查詢</h2>

            <form method="GET" action="">
                <input class="input_2" type="text" name="keyword" placeholder="搜尋姓名或Email" value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit">搜尋</button>
            </form>

            <div class="box2">
                <h2>使用者清單</h2><br>
                <div class="table-container">
                    <table class="card">
                        <thead class="card__title">
                            <tr>
                                <th>姓名</th>
                                <th>電話</th>
                                <th>Email</th>
                                <th>身分</th>
                                <th>註冊時間</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hasResults = false;
                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): 
                                $hasResults = true;
                            ?>
                                <tr class='item'>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= $row['is_admin'] ? '管理員' : '一般使用者' ?></td>
                                    <td><?= $row['created_at']->format('Y-m-d H:i') ?></td>
                                    <td>
                                        <a href="../update_users_form.php?id=<?= $row['id'] ?>">編輯</a> |
                                        <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('確定要刪除此使用者嗎？')">刪除</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if (!$hasResults): ?>
                                <tr class='item'>
                                    <td colspan="6" style="text-align: center;">查無使用者資料</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </main>

        <footer>
            <p>Copyright &copy; 2025 美甲工作室 All Rights Reserved.</p>
        </footer>
    </div>
</body>
</html>
