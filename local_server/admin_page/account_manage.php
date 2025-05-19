<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
if(!isset($_SESSION['admin_login'])){
    echo('<script>window.location.href = "/admin_page/login.php";</script>');
    return;
}
?>
<?php
// POST the set operation's data
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $set_id = $_POST['u'];
    $set_opr = $_POST['opr'];
    if($set_id == null || $set_opr == null){
        http_response_code(400);
        echo("Data incomplete");
        return;
    }
    $username = $_POST['username'];
    $real_name = $_POST['real_name'];
    $stu_id = $_POST['stu_id'];
    $status = $_POST['status'];
    $notify_access_token = $_POST['notify_access_token'];
    // Open mysql connection
    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed...: " . $conn->connect_error);
    }

    $comm = "UPDATE bime_linebot_users SET username='$username',real_name='$real_name',stu_id='$stu_id',status_code=$status,notify_access_token='$notify_access_token' WHERE id=$set_id";
    // 执行插入操作并检查是否成功
    if ($conn->query($comm) === TRUE) {
        echo("<script>alert('Success!');</script>");
        echo("<script>window.close();</script>");
    } else {
        echo("<script>alert('Task Failed... ".$conn->error."');</script>");
        echo("<script>window.close();</script>");
    }
    return;
}
?>
<?php

$set_id = $_GET['u'];
$set_opr = $_GET['opr'];
if($set_id == null || $set_opr == null){
    http_response_code(400);
    echo("Data incomplete");
    return;
}

// Open mysql connection
$conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
// 检查连接
if ($conn->connect_error) {
    die("Connection failed...: " . $conn->connect_error);
}

$need_opr = 0;
if($set_opr == 'verify'){
    $comm = "UPDATE bime_linebot_users SET status_code=1 WHERE id=$set_id";
    $need_opr = 1;
}
if($set_opr == 'suspend'){
    $comm = "UPDATE bime_linebot_users SET status_code=3 WHERE id=$set_id";
    $need_opr = 1;
}
if($set_opr == 'del'){
    $comm = "DELETE FROM bime_linebot_users WHERE id=$set_id";
    $need_opr = 1;
}
if($need_opr){
    // 执行插入操作并检查是否成功
    if ($conn->query($comm) === TRUE) {
        echo("<script>alert('Success!');</script>");
        echo("<script>window.close();</script>");
    } else {
        echo("<script>alert('Task Failed... ".$conn->error."');</script>");
        echo("<script>window.close();</script>");
    }
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management</title>
</head>
<?php
$sql = "SELECT * FROM bime_linebot_users WHERE id=$set_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $cnt = 0;
    $row = $result->fetch_assoc();
}else{
    echo("<script>alert('未找到相符id的使用者')</script>");
    echo("<script>window.close();</script>");
}
?>
<body>
    <form action="account_manage.php" method="post">
        <p>id: <?php echo($row['id']); ?></p>
        <p>Line uid: <?php echo($row['uid']); ?></p>
        <p>使用者名稱: <input type="text" name="username" value="<?php echo($row['username']); ?>"></p>
        <p>使用者真實姓名: <input type="text" name="real_name" value="<?php echo($row['real_name']); ?>"></p>
        <p>使用者學號: <input type="text" name="stu_id" value="<?php echo($row['stu_id']); ?>"></p>
        <p>狀態: <input type="number" name="status" value="<?php echo($row['status_code']); ?>"></p>
        <p>使用者通知權杖: <input type="text" name="notify_access_token" value="<?php echo($row['notify_access_token']); ?>"></p>
        <input type="hidden" name="u" value="<?php echo($set_id); ?>">
        <input type="hidden" name="opr" value="set">
        <input type="submit" value="修改完畢">
    </form>
</body>
</html>