<?php
session_start(); // 啟用 session 機制
include "./php/db_connect.php"; // 你的 SQL Server 連接檔案

// 檢查是否有登入過
$user_logged_in = isset($_SESSION['user_id']);

if (!$user_logged_in) {
    header("Location: login_form.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 取得服務列表
$sql = "select id,name,price FROM services";
$stmt = sqlsrv_query($conn,$sql);
$services = [];

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
} else {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $services[] = $row;
    }
}

sqlsrv_close($conn);
?>


<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"/>
    <title>美甲工作室</title>
</head>
<body>
    <div class="wrapper"> 
        <div class="box">
        <header>
            <h1>美甲工作室</h1>
            <nav>
                <ul>
                    <li><a href="index.php">首頁</a></li>

                    <li>
                        <a href="#">服務項目</a>
                        <ul>
                            <li><a href="services.php">價目表</a></li>
                            <li><a href="./php/gallery_list.php">作品集</a></li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="#">預約管理</a>
                        <ul>
                            <li><a href="./booking_form.php">新增預約</a></li>
                            <li><a href="./appointment_search_form.php">查詢預約</a></li>
                        </ul>
                    </li>
                    

                    <li>
                        <a href="#">會員專區</a>
                        <ul>
                            <li><a href="profile_form.php">修改個人資料</a></li>
                            <li><a href="password_reset_form.php">修改密碼</a></li>
                            <li><a href="appointment_history.php">歷史預約</a></li>
                        </ul>
                    </li>
                    <!-- ✅ 新增：管理員看到後台連結 -->
                    <?php if ($user_logged_in && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <li><a href="./admin/admin.php">後台管理</a></li>
                        
                    <?php endif; ?>

                    <!-- 登出 / 登入 -->
                    <?php if ($user_logged_in):?> 
                        <li><a href="./php/logout.php">登出</a></li>
                    <?php else: ?>
                        <li><a href="login_form.php">登入</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>
        </div>  


        <main>
            <div class="box2">
            <div class="form-container">
                <div style="text-align: center;">
                    <h2>新增預約</h2>
                </div>
                
                <form method="post" action="./php/book_appointment.php">
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                        const dateInput = document.getElementById('date');
                        const today = new Date().toISOString().split('T')[0];
                        dateInput.min = today;
                        });
                    </script>

                    <p>
                        <label for="date">預約時間</label>
                        <input class="input_2" type="date" name="date" id="date" required>
                        <select name="time" required>
                            <option value="">--請選擇想預約的時段--</option>
                            <option value="12:00">12:00</option>
                            <option value="14:00">14:00</option>
                            <option value="16:00">16:00</option>
                            <option value="18:00">18:00</option>
                            <option value="20:00">20:00</option>
                        </select>	
                    </p>
            
            
                    <p>請選擇想要的款式</p>
                            <div class="option-container">
                                <?php foreach($services as $service): ?>
                                <label class="option-item">
                                    <input type="radio" name="service_id" value="<?= $service['id'] ?>" required/>
                                    <!-- 假設圖片命名規則是用服務名稱，記得確認資料庫名稱與圖片名稱一致 -->
                                    <!-- <img src="./images/<?= htmlspecialchars($service['name']) ?>.jpg" alt="<?= htmlspecialchars($service['name']) ?>"> -->
                                    <img src="./images/<?= $service['id'] ?>.jpg" alt="<?= htmlspecialchars($service['name']) ?>">

                                    <div><?= htmlspecialchars($service['name']) ?> / <?= number_format($service['price']) ?>元</div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                    <p>
                        <label>備註</label>
                        <textarea name="note" rows="5"></textarea>
                    </p>	
                    <div style="text-align: center;">
                        <button type="submit">確定預約</button>
                    </div>
                </form>
            </div>
            </div>
        </main>

        <footer>
            <p>Copyright &copy; 2025 美甲工作室 All Rights Reserved.</p>
        </footer>
    </div>    
</body>
</html>