<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
if(!isset($_SESSION['admin_login'])){
    echo('<script>window.location.href = "/admin_page/login.php";</script>');
    return;
}
?>
<?php
if(isset($_GET['opr']) && isset($_GET['id'])){
    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed...: " . $conn->connect_error);
    }
    $opr = $_GET['opr'];
    $id = escapeshellcmd($_GET['id']);
    if($opr == "del"){
        $sql = "DELETE FROM bime_line_api_chat_suggestion where id=$id";
        $result = $conn->query($sql);
    }else if($opr == "solve"){
        $sql = "UPDATE bime_line_api_chat_suggestion SET status=1 where id=$id";
        $result = $conn->query($sql);
    }else if($opr == "unsolve"){
        $sql = "UPDATE bime_line_api_chat_suggestion SET status=0 where id=$id";
        $result = $conn->query($sql);
    }
    echo("<script>alert('操作成功');</script>");
    echo("<script>window.location.href='chat_suggestion.php'</script>");
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>對話內容建議</title>
</head>
<body>
    <table id="chat-suggestions">
        <span>待處理的請求：</span>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td>id</td>
                <td>傳送的訊息</td>
                <td>希望得到的答覆</td>
                <td>提供的帳號名稱</td>
                <td>提供的帳號真實姓名</td>
                <td>時間</td>
            </tr>
            <?php
                $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
                // 检查连接
                if ($conn->connect_error) {
                    die("Connection failed...: " . $conn->connect_error);
                }

                // 查询数据库
                $sql = "SELECT * FROM bime_line_api_chat_suggestion where status=0";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()){
                        echo("<tr>");
                        echo('<td><a href="chat_suggestion.php?id='.$row['id'].'&opr=solve" class="safe">解決</a></td>');
                        echo('<td><a href="chat_suggestion.php?id='.$row['id'].'&opr=del" class="danger">刪除</a></td>');
                        echo('<td>'.$row['id'].'</td>');
                        echo('<td>'.urldecode($row['chat_trigger']).'</td>');
                        echo('<td>'.urldecode($row['chat_response']).'</td>');

                        // Get user information from uid
                        $uid = $row['uid'];
                        $sql2 = "SELECT * FROM bime_linebot_users where uid='$uid'";
                        $result2 = $conn->query($sql2);
                        if($result2->num_rows > 0){
                            $row2 = $result2->fetch_assoc();
                            $username = $row2['username'];
                            $real_name = $row2['real_name'];
                        }
                        echo('<td>'.$username.'</td>');
                        echo('<td>'.$real_name.'</td>');

                        echo('<td>'.$row['suggest_time'].'</td>');
                        echo('</tr>');
                    }
                }
            ?>
            
        </tbody>
    </table>
    <table id="chat-suggestions-solved">
        <span>已處理的請求：</span>
        <tbody>
            <tr>
                <td></td>
                <td>id</td>
                <td>傳送的訊息</td>
                <td>希望得到的答覆</td>
                <td>提供的帳號名稱</td>
                <td>提供的帳號真實姓名</td>
                <td>時間</td>
            </tr>
            <?php
                $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
                // 检查连接
                if ($conn->connect_error) {
                    die("Connection failed...: " . $conn->connect_error);
                }

                // 查询数据库
                $sql = "SELECT * FROM bime_line_api_chat_suggestion where status=1";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()){
                        echo("<tr>");
                        echo('<td><a href="chat_suggestion.php?id='.$row['id'].'&opr=unsolve" class="danger">撤回</a></td>');
                        echo('<td>'.$row['id'].'</td>');
                        echo('<td>'.urldecode($row['chat_trigger']).'</td>');
                        echo('<td>'.urldecode($row['chat_response']).'</td>');

                        // Get user information from uid
                        $uid = $row['uid'];
                        $sql2 = "SELECT * FROM bime_linebot_users where uid='$uid'";
                        $result2 = $conn->query($sql2);
                        if($result2->num_rows > 0){
                            $row2 = $result2->fetch_assoc();
                            $username = $row2['username'];
                            $real_name = $row2['real_name'];
                        }
                        echo('<td>'.$username.'</td>');
                        echo('<td>'.$real_name.'</td>');

                        echo('<td>'.$row['suggest_time'].'</td>');
                        echo('</tr>');
                    }
                }
            ?>
            
        </tbody>
    </table>
    <style>
        .safe {
            color: green;
        }
        .danger {
            color: red;
        }
        table td {
            border-left: 2px groove;
            border-bottom: 2px groove;
        }
    </style>
    <a href="/admin_page/index.php">回首頁</a>
</body>
</html>