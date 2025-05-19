<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
if(!isset($_SESSION['admin_login'])){
    $_SESSION['admin_login_redirect'] = "https://blessed-dogfish-morally.ngrok-free.app/admin_page/reservation/maker_space/checkin.php?rid=$rid";
    echo('<script>window.location.href = "/admin_page/login.php";</script>');
    return;
}
?>
<?php
// POST the set operation's data
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $rid = $_GET['rid'];
    if($rid == null){
        http_response_code(400);
        echo("Data incomplete");
        return;
    }
    $rid = escapeshellcmd($rid);
    // Open mysql connection
    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed...: " . $conn->connect_error);
    }

    $comm = "UPDATE bime_maker_space_reservation SET status=6 WHERE rid='$rid'";
    // 执行插入操作并检查是否成功
    if ($conn->query($comm) === TRUE) {
        echo("<script>alert('Checked in!');</script>");
        echo("<script>window.close();</script>");
    } else {
        echo("<script>alert('Task Failed... ".$conn->error."');</script>");
        echo("<script>window.close();</script>");
    }
    return;
}
?>