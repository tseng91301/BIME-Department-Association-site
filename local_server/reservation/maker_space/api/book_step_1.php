<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
if(!isset($_SESSION['login'])) {
    $_SESSION['login_redirect'] = "/reservation/maker_space/index.html";
    echo("[E0]");
    return;
}
?>
<?php
// 取得 POST 中的 raw_data
$raw_data = $_POST['raw_data'] ?? null;
try {
    // 解析 URL 編碼的字串
    parse_str($raw_data, $parsed_data);

    // 現在 $parsed_data['reservation_data'] 是 JSON 字串
    if (isset($parsed_data['reservation_data'])) {
        $book_data_obj = json_decode($parsed_data['reservation_data'], true);
    }
}catch(Exception $e){
    echo("[E1]");
}
if(!(isset($book_data_obj['datetime']['start']) && isset($book_data_obj['datetime']['end']))) {
    echo('[E2]');
    return;
}
try {
    $start_time = intval($book_data_obj['datetime']['start'])/1000;
    $end_time = intval($book_data_obj['datetime']['end'])/1000;
    date_default_timezone_set('Asia/Taipei');
    $book_time = time();
    $rid = generateRandomString();
    $uid = $_SESSION['userId'];

    // 创建连接
    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // 检查连接
    if ($conn->connect_error) {
        die("[E3]Connection failed...: " . $conn->connect_error);
    }
    // 查询数据库
    $sql = "SELECT * FROM bime_linebot_users where uid='$uid'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $real_name = $row['real_name'];
        $stu_id = $row['stu_id'];
    }

    $sql = "INSERT INTO bime_maker_space_reservation (rid, booker_uid, booker_real_name, booker_student_id, start_time, end_time, book_time, status) VALUES ('$rid', '$uid', '$real_name', '$stu_id', $start_time, $end_time, $book_time, 2)";
    $conn->query($sql);
    $start_time_s = date('Y-m-d H:i:s', intval($start_time));
    $end_time_s = date('Y-m-d H:i:s', intval($end_time));
    $book_dateTime_s = date('Y-m-d H:i:s', intval($book_time));
    $line_msg = "您完成了生機系原創中心的預約，請等待我們審核通過並安排管理員。處理完成會再通知您~\n\n";
    $line_msg .= "開始時間： $start_time_s\n";
    $line_msg .= "結束時間： $end_time_s\n";
    $line_msg .= "預約時間： $book_dateTime_s";
    send_line_text_notification(find_notification_access_token($conn, $uid), $line_msg);
    echo('[R0]');
    return;
} catch (Exception $e){
    echo('[E3] '.strval($e));
    return;
}
?>