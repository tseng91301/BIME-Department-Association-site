<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
if(!isset($_SESSION['admin_login'])){
    echo('<script>window.location.href = "/admin_page/login.php";</script>');
    return;
}
?>
<?php
$conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
// 检查连接
if ($conn->connect_error) {
    die("Connection failed...: " . $conn->connect_error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>創客空間預約管理</title>
</head>
<body>
    <p>當前的預約：</p>
    <table class="data-table book-record-table">
        <tbody id="book-record-progress-tbody">
            <tr>
                <td>操作</td><td>日期</td><td>開始時間</td><td>結束時間</td><td>預約人姓名</td><td>預約人學號</td><td>預約日期/時間</td><td>狀態</td><td>RID</td>
                <?php
                // 查询数据库
                $sql = "SELECT * FROM bime_maker_space_reservation WHERE (status = 0 or status = 1 or status = 2) ORDER BY start_time ASC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()){
                        $date = date('Y-m-d', intval($row['start_time']));
                        $start_time = date('H:i:s', intval($row['start_time']));
                        $end_time = date('H:i:s', intval($row['end_time']));
                        $book_dateTime = date('Y-m-d H:i:s', intval($row['book_time']));
                        echo("<tr>");
                        echo("<td>");
                        echo('<a href="edit.php?i='.$row['id'].'&opr=edit" target="_blank">編輯</a>');
                        echo('<a href="edit.php?i='.$row['id'].'&opr=approve" target="_blank">批准</a>');
                        echo('</td>');
                        echo("<td>$date</td><td>$start_time</td><td>$end_time</td><td>".$row['booker_real_name']."</td><td>".$row['booker_student_id']."</td><td>$book_dateTime</td><td>".$row['status']."</td><td>".$row['rid']."</td>");
                        echo("</tr>");
                    }
                }
                ?>
            </tr>
        </tbody>
    </table>
    <p>歷史預約：</p>
    <table class="data-table book-record-table">
        <tbody id="book-record-progress-tbody">
            <tr>
                <td>操作</td><td>日期</td><td>開始時間</td><td>結束時間</td><td>預約人姓名</td><td>預約人學號</td><td>預約日期/時間</td><td>狀態</td><td>RID</td>
                <?php
                // 查询数据库
                $sql = "SELECT * FROM bime_maker_space_reservation WHERE (status = 3 or status = 4 or status = 5 or status = 6 or status = 7) ORDER BY start_time ASC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()){
                        $date = date('Y-m-d', intval($row['start_time']));
                        $start_time = date('H:i:s', intval($row['start_time']));
                        $end_time = date('H:i:s', intval($row['end_time']));
                        $book_dateTime = date('Y-m-d H:i:s', intval($row['book_time']));
                        echo("<tr>");
                        echo('<td><a href="edit.php?i='.$row['id'].'&opr=edit" target="_blank">編輯</a></td>');
                        echo("<td>$date</td><td>$start_time</td><td>$end_time</td><td>".$row['booker_real_name']."</td><td>".$row['booker_student_id']."</td><td>$book_dateTime</td><td>".$row['status']."</td><td>".$row['rid']."</td>");
                        echo("</tr>");
                    }
                }
                ?>
            </tr>
        </tbody>
    </table>
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