<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
?>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!isset($_SESSION['login'])){
        // If user isn't logged in.
        
        $displayName = $_POST['displayName'];
        $userId = $_POST['userId'];
        $pictureUrl = $_POST['pictureUrl'];
        if($userId == NULL || $displayName == NULL){
            http_response_code(400);
            echo("Bad request, missing user");
            return;
        }
        $_SESSION['displayName'] = escapeshellcmd($displayName);
        $_SESSION['userId'] = escapeshellcmd($userId);
        $_SESSION['pictureUrl'] = escapeshellcmd($pictureUrl);
        $_SESSION['login'] = 0;
    }else{
        $displayName = $_SESSION['displayName'];
        $userId = $_SESSION['userId'];
        $pictureUrl = $_SESSION['pictureUrl'];
    }
    // 创建连接
    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed...: " . $conn->connect_error);
    }
    echo "Connect successfully";
    // 查询数据库
    $sql = "SELECT * FROM bime_linebot_users where uid='$userId'";
    $result = $conn->query($sql);

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
    } else {
        $comm = "INSERT INTO bime_linebot_users (uid, username, profile_img, status_code) values ('$userId', '$displayName', '$pictureUrl', 2)";
        $conn->query($comm);
        $_SESSION['login'] = 2;
        echo("<script>document.location.href=\"/profile/set/index.php?attr=real_name\";</script>");
        return;
    }
}
?>