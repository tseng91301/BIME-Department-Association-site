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
    $set_id = $_POST['i'];
    $set_opr = $_POST['opr'];
    if($set_id == null || $set_opr == null){
        http_response_code(400);
        echo("Data incomplete");
        return;
    }
    $start_time = intval($_POST['start_time']);
    $end_time = intval($_POST['end_time']);
    $booker_real_name = $_POST['booker_real_name'];
    $booker_student_id = $_POST['booker_student_id'];
    $status = intval($_POST['status']);
    // Open mysql connection
    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed...: " . $conn->connect_error);
    }

    $comm = "UPDATE bime_maker_space_reservation SET start_time=$start_time,end_time=$end_time,booker_real_name='$booker_real_name',booker_student_id='$booker_student_id',status=$status WHERE id=$set_id";
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

$set_id = $_GET['i'];
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
if($set_opr == 'approve'){
    $sql = "SELECT * FROM bime_maker_space_reservation WHERE id=$set_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $cnt = 0;
        $row = $result->fetch_assoc();
        $booker_uid = $row['booker_uid'];
        $date = date('Y-m-d', intval($row['start_time']));
        $start_time = date('Y-m-d H:i:s', intval($row['start_time']));
        $end_time = date('Y-m-d H:i:s', intval($row['end_time']));
        $book_dateTime = date('Y-m-d H:i:s', intval($row['book_time']));
    }else{
        echo("<script>alert('未找到相符id的預約')</script>");
        echo("<script>window.close();</script>");
        return;
    }
    $line_msg = "您的生機系原創中心的預約已批准，請按時抵達。如欲取消，請在開始時間前60分鐘取消預約\n\n";
    $line_msg .= "開始時間： $start_time\n";
    $line_msg .= "結束時間： $end_time\n";
    $line_msg .= "預約時間： $book_dateTime\n";
    $line_msg .= "如預約未到，我們將會予以停權處分，詳情請參閱「原創中心的預約及使用條款」";
    send_line_text_notification(find_notification_access_token($conn, $booker_uid), $line_msg);
    $comm = "UPDATE bime_maker_space_reservation SET status=0 WHERE id=$set_id";
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
$sql = "SELECT * FROM bime_maker_space_reservation WHERE id=$set_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $cnt = 0;
    $row = $result->fetch_assoc();
    $date = date('Y-m-d', intval($row['start_time']));
    $start_time = date('Y-m-d H:i:s', intval($row['start_time']));
    $end_time = date('Y-m-d H:i:s', intval($row['end_time']));
    $book_dateTime = date('Y-m-d H:i:s', intval($row['book_time']));
}else{
    echo("<script>alert('未找到相符id的預約')</script>");
    echo("<script>window.close();</script>");
    return;
}
?>
<body>
    <form action="edit.php" method="post" id="edit_form">
        <p>id: <?php echo($row['id']); ?></p>
        <p>開始時間 (Time Stamp): <input type="text" name="start_time" id="start_time" value="<?php echo($row['start_time']); ?>">( <?php echo($start_time);?> )</p>
        <p>結束時間 (Time Stamp): <input type="text" name="end_time" id="end_time" value="<?php echo($row['end_time']); ?>">( <?php echo($end_time);?> )</p>
        <p>預約人姓名: <input type="text" name="booker_real_name" value="<?php echo($row['booker_real_name']); ?>"></p>
        <p>預約人學號: <input type="text" name="booker_student_id" value="<?php echo($row['booker_student_id']); ?>"></p>
        <p>預約日期/時間: <?php echo($book_dateTime); ?></p>
        <p>狀態: <input type="number" name="status" value="<?php echo($row['status']); ?>"></p>
        <p>RID: <?php echo($row['rid']); ?></p>
        <input type="hidden" name="i" value="<?php echo($set_id); ?>">
        <input type="hidden" name="opr" value="set">
        <input type="button" value="修改完畢" onclick="ask_submit()">
    </form>
    <pre>
說明：
# status_code
0: 預約成立
1: 正在審核
2: 正在安排管理人員
3: 預約取消
4: 預約被禁止
5: 預約未到
6: Show up
7: 預約被管理員取消
    </pre>
    <h3>日期時間 to Unix time stamp 轉換器</h3>
    <p>輸入日期<input type="date" id="conv_date"></p>
    <p>輸入時間<input type="time" id="conv_time"></p>
    <button id="conv_btn" onclick="conv_ts()">轉換</button>
    <p>Time stamp: <input type="number" disabled id="conv_stamp">　<button onclick="copy_stamp('start_time')">複製到開始時間</button><button onclick="copy_stamp('end_time')">複製到結束時間</button></p>
    <script>
        function conv_ts(){
            var date = document.getElementById('conv_date').valueAsNumber;
            var time = document.getElementById('conv_time').valueAsNumber;
            var dateTime = new Date(date + time);
            // 修正時區偏移
            var timezoneOffset = dateTime.getTimezoneOffset() * 60000; // 本地時區偏移量
            document.getElementById('conv_stamp').value = (dateTime.getTime() + timezoneOffset) / 1000;
        }
        function ask_submit() {
            var confirm_b = confirm("是否提交修改？")
            if(confirm_b) {
                document.getElementById('edit_form').submit();
            }
        }
        function copy_stamp(id) {
            // 選取 input 元素
            var input = document.getElementById("conv_stamp").value;
            document.getElementById(id).value = input;
        }
    </script>
</body>
</html>