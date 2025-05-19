<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
if(!isset($_SESSION['admin_login'])){
    echo('<script>window.location.href = "/admin_page/login.php";</script>');
    return;
}
?>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!isset($_SESSION['user_cnt'])){
        echo("Data Error");
        return;
    }
    if($_POST['notify_name'] == null){
        return;
    }
    $notify_name = $_POST['notify_name'];
    $notify_info = $_POST['notify_info'];
    if($notify_info == null){
        $have_info = 0;
    }else{
        $have_info = 1;
    }
    $notify_info_line = $_POST['notify_info_line'];
    $notify_rds = generateRandomString();

    // Check if the notification is sent to public
    if(isset($_POST['t-all'])){
        $public_notify = 1;
    }else{
        $public_notify = 0;
    }

    // Store messages into md file and mySQL database
    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed...: " . $conn->connect_error);
        return;
    }

    // insert into database
    $currentDateTime = date('Y-m-d H:i:s');
    $adminId = $_SESSION['adminId'];
    $sql = "INSERT INTO bime_linebot_notifications (rds, title, detail_line, add_date, add_by) VALUES ('$notify_rds', '$notify_name', '$notify_info_line', '$currentDateTime', '$adminId')";
    if ($conn->query($sql) !== TRUE) {
        echo("<script>alert('Error!');</script>");
        return;
    }

    // find the notify id by rds
    $sql = "SELECT * FROM bime_linebot_notifications WHERE rds='$notify_rds'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $notify_id = $row['id'];
    }else{
        echo("<script>alert('Error2!');</script>");
    }

    // insert notification info into file(md file)
    file_put_contents($notification_root.'/'.$notify_id.'.md', $notify_info);
    if($have_info){
        $sql = "UPDATE bime_linebot_notifications SET detail_route='$notify_id',rds=NULL WHERE id='$notify_id'";
        $conn->query($sql);
    }else{
        $sql = "UPDATE bime_linebot_notifications SET detail_route='none',rds=NULL WHERE id='$notify_id'";
        $conn->query($sql);
    }
    if($public_notify){
        $sql = "UPDATE bime_linebot_notifications SET target_user='all' WHERE id='$notify_id'";
        $conn->query($sql);
    }

    // Form Line Notify message
    $line_msg = "\n".$notify_name."\n\n".$notify_info_line;

    $cnt = $_SESSION['user_cnt'];
    $notify_info_dict = array();
    $notify_info_dict['target'] = array();
    for($a=0;$a<$cnt;$a++){
        if(isset($_POST['t-'.$a])){
            $nid = $_POST['t-'.$a];

            // add into target users
            $sql = "SELECT * FROM bime_linebot_users WHERE notify_access_token='$nid'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $notify_info_dict['target'][] = $row['uid'];

            // Send message
            $send_successful = send_line_text_notification($nid, $line_msg);
            if($send_successful) {
                echo("Message sent successfully!");
                echo("<script>window.close();</script>");
            }
        }
    }

    // store target user dict into json file
    if(!$public_notify){
        file_put_contents($notification_root.'/'.$notify_id.'.json', json_encode($notify_info_dict));
    }
    
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>通知管理</title>
</head>
<body>
    <h1>發布通知</h1>
    <form action="notification.php" method="post" target="_blank" id="notify-send-form">
        <p>輸入通知標題：</p>
        <input type="text" required name="notify_name">
        <p>輸入通知內容(以Markdown格式編寫)</p>
        <textarea name="notify_info"></textarea>
        <p>輸入通知內容(用來當作Lint Notify的通知內容)</p>
        <textarea name="notify_info_line"></textarea>
        <p>選擇發布對象：</p>
        <p><input type="checkbox" id="notify-public-target-check" name="t-all">公開消息</p>
        <p><input type="checkbox" id="notify-target-select-all">全選</p>
        <table id="notify-targets">
            <tbody>
                <tr>
                    <td>勾選</td>
                    <td>id</td>
                    <td>使用者名稱</td>
                    <td>使用者真實姓名</td>
                    <td>使用者學號</td>
                    <td>使用者uid</td>
                    <td>狀態</td>
                    <td>使用者通知權杖</td>
                </tr>
                <?php
                    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
                    // 检查连接
                    if ($conn->connect_error) {
                        die("Connection failed...: " . $conn->connect_error);
                    }

                    // 查询数据库
                    $sql = "SELECT * FROM bime_linebot_users";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $cnt = 0;
                        while($row = $result->fetch_assoc()){
                            echo("<tr>");
                            echo('<td><input type="checkbox" name="t-'.$cnt.'" id="t-'.$cnt.'" value="'.$row['notify_access_token'].'"></td>');
                            echo('<td>'.$row['id'].'</td>');
                            echo('<td>'.$row['username'].'</td>');
                            echo('<td>'.$row['real_name'].'</td>');
                            echo('<td>'.$row['stu_id'].'</td>');
                            echo('<td>'.$row['uid'].'</td>');
                            echo('<td>'.$row['status_code'].'</td>');
                            echo('<td>'.$row['notify_access_token'].'</td>');
                            echo('</tr>');
                            $cnt += 1;
                        }
                    }
                    $_SESSION['user_cnt'] = $cnt;
                ?>
                <!-- <tr>
                    <td><input type="checkbox" name="opt[]" id="t-1"></td>
                    <td>dsfdsfd</td>
                    <td>tseng</td>
                    <td>曾敬凱</td>
                    <td>eeqwreoifhougewf7w0w4</td>
                    <td>1</td>
                    <td>sdjkfjafhuiwy9048ihrguioafie</td>
                </tr> -->
                
            </tbody>
        </table>
        <style>
            table td {
                border-left: 2px groove;
                border-bottom: 2px groove;
            }
        </style>
        <input type="submit" value="發送通知">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            var user_num = <?php echo($cnt);?>;
            $(document).ready(function(){
                $("#notify-target-select-all").change(function(){
                    if($(this).is(':checked')){
                        for(var a=0;a<user_num;a++){
                            $("#t-"+(a)).prop('checked', true);
                        }
                    }else{
                        for(var a=0;a<user_num;a++){
                            $("#t-"+(a)).prop('checked', false);
                        }
                    }
                })
            })
        </script>
    </form>
    <a href="/admin_page/index.php">回首頁</a>
</body>
</html>
