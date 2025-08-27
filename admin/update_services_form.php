<?php
session_start();
include "../php/db_connect.php";

// 確認是否為管理員
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

// 取得服務資料
$sql = "SELECT * FROM services";
$query = sqlsrv_query($conn, $sql);
if ($query === false) {
    die("查詢不到服務項目".print_r(sqlsrv_errors(), true));
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
    <div class="wrapper">
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
            <div class="box2">
                <h2>我們提供的服務</h2><br>
                <div class="table-container">
                    <table class="card">
                        <thead class="card__title">
                            <th>服務項目</th>
                            <th>敘述</th>
                            <th>價格</th>
                            <th>作業時間</th>
                            <th>修改</th>
                        </thead>
                        <tbody>
                            <?php while($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)): ?>
                                <tr class='item'>
                                    <form action="./php/update_service.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <td><input class="input_4" type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"></td>
                                        <td><input class="input_4" type="text" name="description" value="<?= htmlspecialchars($row['description']) ?>"></td>
                                        <td><input class="input_4" type="number" name="price" value="<?= htmlspecialchars(intval($row['price'])) ?>" step="1"></td>
                                        <td><input class="input_4" type="number" name="duration" value="<?= htmlspecialchars($row['duration']) ?>"></td>
                                        <td><button class="input_4" type="submit">儲存</button></td>
                                    </form>
                                </tr>
                            <?php endwhile; ?>
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
