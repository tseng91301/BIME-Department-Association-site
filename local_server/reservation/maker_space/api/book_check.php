<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
if(!isset($_SESSION['login'])) {
    $_SESSION['login_redirect'] = "/reservation/maker_space/book_check.html";
    echo('{"result":"error", "code":"[E0]"}');
    return;
}
?>
<?php
// 创建连接
$conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
// 检查连接
if ($conn->connect_error) {
    die('{"result":"error", "code":"[E3]Connection failed...: ' . $conn->connect_error . '"}');
}
$uid = $_SESSION['userId'];
if($_SERVER['REQUEST_METHOD'] == 'GET') {
    if($_GET['c'] == "progress") {
        // 查询数据库
        $sql = "SELECT * FROM bime_maker_space_reservation where booker_uid='$uid' and (status = 0 or status = 1 or status = 2)";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $output_data = [];
            while($row = $result->fetch_assoc()){
                $output_data[] = $row;
            }
        }
    }else if($_GET['c'] == "history") {
        // 查询数据库
        $sql = "SELECT * FROM bime_maker_space_reservation where booker_uid='$uid' and (status = 3 or status = 4 or status = 5 or status = 6 or status = 7)";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $output_data = [];
            while($row = $result->fetch_assoc()){
                $output_data[] = $row;
            }
        }
    }
}else if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 取得 POST 中的 raw_data
    $raw_data = $_POST['raw_data'] ?? null;
    try {
        // 解析 URL 編碼的字串
        parse_str($raw_data, $parsed_data);
    }catch(Exception $e){
        echo("[E1]");
        return;
    }
    if($parsed_data['o'] == "cancel"){
        if(!isset($parsed_data['rid'])) {
            echo('{"status": "error", "code": "[E4]"}');
            return;
        }
        $rid = escapeshellcmd($parsed_data['rid']);
        $sql = "UPDATE bime_maker_space_reservation SET status = 3 WHERE rid='$rid'";
        $result = $conn->query($sql);
        $output_data = [
            "test" => "Complete"
        ];
    }
}

$output_data_complete = [
    "status" => "success",
    "code" => "[R0]",
    "data" => $output_data
];
echo(json_encode($output_data_complete));
?>