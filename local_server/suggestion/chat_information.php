<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
?>
<?php
if($_SESSION['login'] != 1){
    $trig = urlencode($_GET['trig']);
    $echo = urlencode($_GET['echo']);
    $_SESSION['login_redirect'] = "/suggestion/chat_information.php?trig=$trig&echo=$echo";
    echo("<script>document.location.href=\"https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=2005653179&redirect_uri=https://ntu-bime-linebot.onrender.com/login_callback?authenticator=line&state=fsaafjwri20ttga0hwpjisg0t5&scope=openid%20profile&nonce=helloWorld&prompt=consent&max_age=3600&ui_locales=zh-TW&bot_prompt=normal\";</script>");
    return;
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['trig'] == null || $_POST['echo'] == null){
        http_response_code(400);
        echo("Some crucial information isn't given!");
        return;
    }
    $trig = urlencode($_POST['trig']);
    $echo = urlencode($_POST['echo']);
    // 创建连接
    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed...: " . $conn->connect_error);
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['login'] = $row['status_code'];
        if($_SESSION['login'] == 2){
            echo("<script>document.location.href=\"/profile/set/index.php?attr=real_name\";</script>");
        }else if($row['notify_access_token'] == NULL){
            echo("<script>document.location.href=\"/profile/set/index.php?attr=line_notify\";</script>");
        }else{
            if(isset($_SESSION["login_redirect"])){
                echo("<script>document.location.href=\"".$_SESSION['login_redirect']."\";</script>");
            }else{
                echo("<script>document.location.href=\"/index.html\";</script>");
            }
        }
    }
    // Insert into bime_line_api_chat_suggestion
    $currentDateTime = date('Y-m-d H:i:s');
    $uid = $_SESSION['userId'];
    $sql = "INSERT INTO bime_line_api_chat_suggestion (chat_trigger, chat_response, suggest_time, uid) VALUES ('$trig', '$echo', '$currentDateTime', '$uid')";
    $result = $conn->query($sql);
    echo("資料輸入成功，感謝您寶貴的建議！<br/>點擊右上角關閉按鈕即可關閉頁面");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat suggestion</title>
</head>
<body>
    <h3 style="color: red;">注意：請勿在此表單傳送不雅言語或帳號、密碼等個人資訊</h3>
    <form action="chat_information.php" method="post">
        輸入您傳送的訊息：<input type="text" name="trig" <?php echo("value='".$_GET['trig']."'");?>><br/>
        輸入您想要讓Line bot 回答的訊息(或者告訴我們應該怎麼回覆也可以)：<br/>
<textarea name="echo" style="padding: 10px; min-width: 300px; min-height: 400px;">
<?php echo($_GET['echo']); ?>
</textarea><br/>
        <input type="submit" value="送出！">
    </form>
    <p style="
        color: #676767;
    ">
        在這邊，您所輸入的內容會傳送到我們的後台，我們將會察看並檢查您所輸入的內容，最後「悄悄地」更新聊天機器人上<br/>
        因此想要知道模型是否更新，您可以常常跟此Line bot聊天，檢查是否有更新哦！
    </p>
</body>
</html>
