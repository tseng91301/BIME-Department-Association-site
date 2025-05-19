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
// Insert notify id into table
$sql = "SELECT * FROM bime_linebot_users WHERE uid='$userId'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $notify_access_token = $row['notify_access_token'];
    if($notify_access_token == null){
        http_response_code(403);
        echo("Notification not allowed!");
        return;
    }

    // Send test notification
    $token_url = 'https://notify-api.line.me/api/notify';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $notify_access_token"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true); // 设置请求方法为 POST
    $data = [
        'message' => 'Hello World!'
        // 'message' => 'This is a test message from BIME Line Bot'
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // 将数据编码为 URL 查询字符串
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        echo("Message sent successfully!");
        echo("<script>window.close();</script>");
    }
    curl_close($ch);
}
?>
