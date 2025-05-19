<?php
$servername = "localhost";
$username = "bime";
$password = "BIME2024aewBHDBzrag493903gvag9u835gg834";
$dbname = "bime_line_api_user";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("Connection failed...: " . $conn->connect_error);
}
echo "Connect successfully";

// 查询数据库
$sql = "SELECT id, firstname, lastname FROM mytable";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // 输出数据
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
    }
} else {
    echo "0 result";
}
$conn->close();
?>
