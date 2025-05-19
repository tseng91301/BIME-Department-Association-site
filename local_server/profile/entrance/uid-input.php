<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
?>
<?php
$uid = escapeshellcmd($_POST['line-uid']);
$_SESSION['userId'] = $uid;

// 创建连接
$conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
// 检查连接
if ($conn->connect_error) {
    die("Connection failed...: " . $conn->connect_error);
}
echo "Connect successfully";

// 查询数据库
$sql = "SELECT * FROM bime_linebot_users where uid='$uid'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['login'] = $row['status_code'];
    $_SESSION['displayName'] = $row['username'];
    $_SESSION['pictureUrl'] = $row['profile_img'];
    if($_SESSION['login'] == 2){
        echo("<script>document.location.href=\"/profile/set/index.php?attr=real_name\";</script>");
        return;
    }
    echo("<script>document.location.href=\"/index.html\";</script>");
} else {
    echo("<script>alert('請先使用手機Line登入再使用此登入方式');</script>");
    echo("<script>document.location.href=\"/index.html\";</script>");
}
?>