<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
?>
<?php
if($_SESSION['login'] != 1){
    http_response_code(403);
    echo("Invalid login information!");
    return;
}
?>
<?php
// 创建连接
$conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
// 检查连接
if ($conn->connect_error) {
    die("Connection failed...: " . $conn->connect_error);
}

$userId = $_SESSION['userId'];

//check whether user has notify_access_token
$sql = "SELECT * FROM bime_linebot_users WHERE uid='$userId'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $notify_access_token = $row['notify_access_token'];
    if($notify_access_token == null){
        echo("<script>alert('您未訂閱Line Notify 功能');</script>");
    }else{
        // Attach Line server to revoke the notification access
        $token_url = "https://notify-api.line.me/api/revoke";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $notify_access_token"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // 设置请求方法为 POST
        $data = [];
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // 将数据编码为 URL 查询字符串
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        } else {
            echo("Message sent successfully!");
            echo("<script>window.close();</script>");
        }
        curl_close($ch);

        // Remove the notify_access_token of the user from database
        $sql = "UPDATE bime_linebot_users SET notify_access_token=NULL WHERE uid='$userId'";
        $result = $conn->query($sql);
        
        echo("<script>alert('已成功取消訂閱Line Notify 通知');</script>");
    }
    echo("<script>window.close();</script>");
}
?>