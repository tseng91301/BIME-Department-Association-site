<?php
session_start();
if(!isset($_SESSION['admin_login'])){
    echo('<script>window.location.href = "/admin_page/login.php";</script>');
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理員頁面</title>
</head>
<body>
    <a href="notification.php">通知管理</a>
    <a href="account.php">帳號管理</a>
    <a href="chat_suggestion.php">查看聊天室建議</a>
    <a href="reservation/maker_space/index.php">創客空間預約</a>
</body>
</html>


