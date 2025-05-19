<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
$userId = $_SESSION['userId'];
$finish_settings = 0;
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if($_GET['attr'] == 'real_name'){
        if($_SESSION['login'] != 2){
            http_response_code(400);
            echo("Unmatched request index!");
            return;
        }
    }
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['attr'] == 'real_name'){
        if($_SESSION['login'] != 2){
            http_response_code(400);
            echo("Unmatched request index!");
            return;
        }
        $real_name = escapeshellcmd($_POST['real_name']);
        $stu_id = escapeshellcmd($_POST['stu_id']);
        if($real_name == "" or $stu_id == ""){
            http_response_code(400);
            echo("Incomplete input!");
            return;
        }

        // 创建连接
        $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
        // 检查连接
        if ($conn->connect_error) {
            die("Connection failed...: " . $conn->connect_error);
        }
        // 查询数据库
        $sql = "UPDATE bime_linebot_users SET real_name='$real_name', stu_id='$stu_id', status_code=1 where uid='$userId'"; // Change to status_code=4 if page finished construction
        $_SESSION['login'] = 4;
        $result = $conn->query($sql);
        $finish_settings = 1;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/module/css/stand-form.css" rel="stylesheet" >
    <title>Settings</title>
</head>
<body>
    <form id="post-redirect" action="/profile/entrance/index.php" method="post"></form>
    <?php
        if($_GET['attr'] == 'real_name'){
            echo('<form action="" method="post">');
            echo("<h3>請輸入真實姓名(用來進行人工審核)</h3>");
            echo('<input type="hidden" name="attr" value="real_name">');
            echo('<input type="text" class="bottom-line-input" name="real_name">');
            echo("<h3>請輸入您的學號(用來進行人工審核)</h3>");
            echo('<input type="text" class="bottom-line-input" name="stu_id"><br/>');
            echo('<input type="submit" class="fullpage-button">');
            echo('</form>');
            return;
        }
        if($_GET['attr'] == 'line_notify'){
            echo('<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>設置Line Notification</title></head><body><h1>請協助我們完成以下動作：</h1><h3>開通 Line Notify</h3><p>我們的Line Bot只能被動處理使用者發送的訊息，因此需要您打開Line Notify權限，這樣若有最新消息，才能在第一時間通知您</p><p>放心，授予Line Notify通知權限並不會洩漏各位的個資，我們十分注重您的隱私權</p><p>之後仍可至「通知設定」將Line Notify權限移除</p><form action="/profile/set/index.php" method="post" id="access-notify-form"></form><div class="button-bar"><a href="/index.html">但是我拒絕 :(</a><button id="access-notify-btn" onclick="access_notify();">前往設定</button></div><script>function access_notify(){window.location.href="https://notify-bot.line.me/oauth/authorize?response_type=code&client_id=KPvbmdv1KmZTpd4u2uGDP3&redirect_uri=https://ntu-bime-linebot.onrender.com/profile/notify/index.php&scope=notify&state=fsdfdsgwrgwgrehs";}</script></body><style>body {padding: 25px;}.button-bar {display: flex;flex-direction: row;justify-content: space-around;align-items: center;margin-top: 100px;}.button-bar a {text-decoration: none;font-size: 14px;color: #787878;}.button-bar button {padding: 10px;font-size: 18px;background-color: #007be7;color: white;border-color: white;border-radius: 20px;font-weight: 600;}</style></html>');
        }
    ?>
    <?php
    if($finish_settings){
        echo("<script>document.getElementById('post-redirect').submit()</script>");
    }
    ?>
</body>
</html>