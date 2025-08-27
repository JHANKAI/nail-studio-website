<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <title>SweetAlert2 灰色系淺色背景示範</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
.my-popup-class {
  border-radius: 15px !important;
  font-family: 'Arial', sans-serif !important;
  box-shadow: 0 0 20px rgba(150, 150, 150, 0.3) !important; /* 淡灰色陰影 */
  background-color: #f5f5f5 !important; /* 非常淺的灰色背景 */
  color: #4a4a4a !important; /* 中深灰字體 */
}

.my-title-class {
  color: #6e6e6e !important; /* 中灰標題 */
  font-size: 28px !important;
  font-weight: bold !important;
}

.my-confirm-button {
  background-color: #a0a0a0 !important; /* 中灰按鈕 */
  font-size: 18px !important;
  border: none !important;
  border-radius: 10px !important;
  padding: 10px 30px !important;
  color: #fff !important; /* 按鈕字白 */
  transition: background-color 0.3s ease;
  cursor: pointer;
}

.my-confirm-button:hover {
  background-color: #c0c0c0 !important; /* 按鈕hover變淺灰 */
}




    </style>
</head>
<body>
<?php
echo "
<script>
Swal.fire({
    icon: 'info',
    title: '註冊成功！',
    text: '歡迎加入我們 🎉',
    confirmButtonText: '前往登入',
    customClass: {
      popup: 'my-popup-class',
      title: 'my-title-class',
      confirmButton: 'my-confirm-button'
    }
}).then(() => {
    window.location.href = 'login_form.php';
});
</script>
";
?>
</body>
</html>
