<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
if(!isset($_SESSION['login'])){
    http_response_code(400);
    echo("Not logged in, abort!");
    return;
}
if(!isset($_GET['id'])){
    http_response_code(400);
    echo("id is not defined!");
    return;
}
?>
<?php
$userId = $_SESSION['userId'];
// 创建连接
$conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
// 检查连接
if ($conn->connect_error) {
    die("Connection failed...: " . $conn->connect_error);
}

$notify_id = $_GET['id'];
// Get notification title
$sql = "SELECT * from bime_linebot_notifications WHERE id=$notify_id";
$result = $conn->query($sql);
if ($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $notify_title = $row['title'];
}
// Checking public notifications
$sql = "SELECT * from bime_linebot_notifications WHERE id=$notify_id AND target_user='all'";
$result = $conn->query($sql);
if ($result->num_rows > 0){
    $notify_content = file_get_contents($notification_root."/".$notify_id.".md");
}else{
    try{
        $access = json_decode(file_get_contents($notification_root."/".$notify_id.".json"), true)['target'];
        if(in_array($_SESSION['userId'], $access)){
            $notify_content = file_get_contents($notification_root."/".$notify_id.".md");
        }else{
            throw new Exception("Not in access userIds", 1);
        }
    }catch(Exception $e){
        http_response_code(400);
        echo("You cannot access this notification!");
        return;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo($notify_title); ?></title>
</head>
<body>
    <textarea style="display: none;" id="notify-content-markdown">
<?php
echo($notify_content);
?>
    </textarea>
    <div id="notify-html-output"></div>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const markdownText = document.getElementById('notify-content-markdown').value;
            const htmlOutput = marked.parse(markdownText);
            document.getElementById('notify-html-output').innerHTML = htmlOutput;
        });
    </script>
</body>
    <style>
        td {
            border-right: 2px groove;
            border-bottom: 2px groove;
            padding: 4px;
        }
    </style>
</html>