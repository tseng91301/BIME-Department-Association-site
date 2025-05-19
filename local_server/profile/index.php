<?php 
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if($_SESSION['login'] == 2){
        echo("<script>document.location.href=\"/profile/set?attr=real_name\";</script>");
        return;
    }
    if(!isset($_SESSION['login']) || $_SESSION['login'] == null){
        $_SESSION['login_redirect'] = '/profile/index.php';
        echo("<script>document.location.href=\"https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=2005653179&redirect_uri=https://ntu-bime-linebot.onrender.com/login_callback?authenticator=line&state=fsaafjwri20ttga0hwpjisg0t5&scope=openid%20profile&nonce=helloWorld&prompt=consent&max_age=3600&ui_locales=zh-TW&bot_prompt=normal\";</script>");
        return;
    }
    if($_SESSION['login_redirect'] == '/profile/index.php'){
        unset($_SESSION['login_redirect']);
    }
}else { // POST
    if(!isset($_SESSION["login"])) {
        http_response_code(400);
        echo("Unauthorized!");
        return;
    }
    $email = base64_encode(htmlentities($_POST["email"]));
    $phone = base64_encode(htmlentities($_POST["phone"]));
    $discord = base64_encode(htmlentities($_POST["discord"]));
    $note = base64_encode(htmlentities($_POST["note"]));
    $address = base64_encode(htmlentities($_POST["address"]));
    $uid = $_SESSION["userId"];

    // 创建连接
    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed...: " . $conn->connect_error);
    }
    // 查询数据库
    $sql = "UPDATE bime_linebot_users SET email='$email', tel='$phone', address='$address', note='$note', discord='$discord' WHERE uid='$uid'";
    $result = $conn->query($sql);
    echo("<script>alert('修改成功！');</script>");
    echo("<script>document.location.href=\"index.php\";</script>");
}
?>

<?php
$uid = $_SESSION["userId"];

// 创建连接
$conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
// 检查连接
if ($conn->connect_error) {
    die("Connection failed...: " . $conn->connect_error);
}
// 查询数据库
$sql = "SELECT * FROM bime_linebot_users where uid='$uid'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $real_name = $row["real_name"];
    $stu_id = $row["stu_id"];
    $email = base64_decode($row["email"]);
    $tel = base64_decode($row["tel"]);
    $discord = base64_decode($row["discord"]);
    $address = base64_decode($row["address"]);
    $note = base64_decode($row["note"]);
} else {
    echo("Data processing error, please login in again.");
    session_destroy();
    echo("<script>document.location.href=\"/index.html\";</script>");
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/themify-icons.css" rel="stylesheet">
    <link href="/module/css/global-style.css" rel="stylesheet">  
    <link href="/info/style.css" rel="stylesheet">
    <title>個人主頁</title>
</head>
<body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/module/js/size-change.js"></script>
    <div class="floating">
        <span class="ti-close floating-close"></span>
        <div class="floating-content"></div>
        <script type="text/javascript" src="/module/js/dev-login.js"></script>
    </div>
    <div class="page">
        <div id="page-top" class="page-top"></div>
        <div class="page-center" id="page-center" style="padding-left: 25px;">
            <h1>我的個人資料</h1>
            <h2>基本資訊：</h2>
            <form id="personal-information-form" class="profile-form ">
                <p>姓名：<input id="personal-information-name-input" value="<?php echo($real_name); ?>" disabled></p>
                <p>學號：<input id="personal-information-stuid-input" value="<?php echo($stu_id);?>" disabled></p>
                <p style="font-size: 12px;
            color: #939393;"><span class="ti-info-alt" style="font-family: 'themify';"></span> 需要修改嗎？請聯絡管理員</p>
            </form>
            <h2>聯絡資料：</h2>
            <form id="personal-contact-form" class="profile-form" name="personal-contact-form-name" action="index.php" method="post">
                <p>Email: <input name="email" id="personal-contact-email" value="<?php echo($email);?>" style="width: 300px;"></p>
                <p>電話號碼：<input name="phone" id="personal-contact-phone" value="<?php echo($tel);?>"></p>
                <p>Discord ID：<input name="discord" id="personal-contact-discord" value="<?php echo($discord);?>"></p>
                <p>聯絡地址(以換行分隔)：</p>
<textarea id="personal-contact-address" name="address">
<?php echo($address); ?>
</textarea>
                <p>Note:</p>
<textarea id="personal-contact-note" name="note">
<?php echo($note); ?>
</textarea>
                <p>Note preview:</p>
<pre style="
    border: 3px solid;
    padding: 15px;
    border-radius: 13px;
    border-color: #2a79ff;
    background-color: #a6deff;
    overflow: overlay;
" id="personal-contact-note-preview">
<?php echo($note); ?>
</pre>
            </form>
            <button id="personal-contact-refresh-btn" class="operating-btn">提交修改</button>
        </div><!-- div class="page-center" -->
        <div class="page-footer" id="page-footer"></div>
    </div>
    <script src="/module/js/floating.js"></script>
    <script>
        $("#page-top").load("/module/top.php");
        $("#page-footer").load("/module/foot.html");
    </script>
    <script>
        $("#personal-contact-refresh-btn").click(function(){
            var cf = confirm("確認提交修改？")
            if(cf) {
                $("#personal-contact-form").submit();
            }
        });

        $("#personal-contact-note").on("input", function(){
            var text = $(this).val();
            $("#personal-contact-note-preview").text(text);
        });
    </script>
</body>
</html>
<style>
    .profile-form {
        border-left: 3px;
        border-left-style: groove;
        padding-left: 25px;
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .profile-form * {
        font-size: 16px;
        font-family: system-ui;
    }

    .profile-form input {
        padding: 5px;
        border-radius: 10px;
        background-color: #fffed1;
    }

    .profile-form input:disabled {
        background-color: #e9e9e9;
    }

    .profile-form textarea {
        padding: 10px;
        border-radius: 10px;
        width: fit-content;
        height: fit-content;
        min-width: 200px;
        min-height: 70px;
        margin-left: 17px;
        background-color: #fffed1;
    }

    .operating-btn {
        padding: 7px;
        background-color: #8cc9ff;
        border: 2px groove;
        border-radius: 10px;
        font-weight: bold;
        margin: 15px;
    }

    .operating-btn:active {
        background-color: #328bff; /* 按下时的背景色 */
        transform: scale(0.95); /* 添加缩小的视觉效果 */
        box-shadow: 0 5px #666; /* 按下时的阴影 */
        color: white;
    }
</style>