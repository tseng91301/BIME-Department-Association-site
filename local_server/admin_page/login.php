<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $pass1 = $user.'|'.$pass;
    $passhash = hash('sha256', $pass1);

    //mySQL check
    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed...: " . $conn->connect_error);
    }

    // 查询数据库
    $sql = "SELECT * FROM bime_line_api_admins where username='$user' AND passhash='$passhash'";
    // echo($sql);
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $currentDateTime = date('Y-m-d H:i:s');
        $sql = "UPDATE bime_line_api_admins SET last_login='$currentDateTime' WHERE passhash='$passhash'";
        // echo($sql);
        if ($conn->query($sql) !== TRUE) {
            echo("<script>alert('時間戳記錄失敗！')</script>");
        }
        $_SESSION['admin_login'] = 1;
        $_SESSION['adminId'] = $user;
        if(isset($_SESSION["admin_login_redirect"])){
            echo("<script>window.location.href=\"".$_SESSION['admin_login_redirect']."\";</script>");
        }else{
            echo("<script>window.location.href = '/admin_page/index.php'</script>");
        }
        
    } else {
        echo("<script>alert('帳號或密碼有誤');</script>");
        echo("<script>window.location.href = '/admin_page/login.php'</script>");
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin login</title>
</head>
<body>
    <h2>管理員登入</h2>
    <form action="login.php" method="post">
        <p>User: </p>
        <input name="username" type="text">
        <p>Password: </p>
        <input name="password" type="password">
        <input type="submit">
    </form>
</body>
</html>