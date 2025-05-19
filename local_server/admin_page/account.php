<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
if(!isset($_SESSION['admin_login'])){
    echo('<script>window.location.href = "/admin_page/login.php";</script>');
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>帳號管理</title>
</head>
<body>
    <table id="notify-targets">
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
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
                        echo('<td><a href="account_manage.php?u='.$row['id'].'&opr=set" target="_blank">設置</a></td>');
                        echo('<td><a href="account_manage.php?u='.$row['id'].'&opr=verify" target="_blank" class="safe">驗證</a></td>');
                        echo('<td><a href="account_manage.php?u='.$row['id'].'&opr=suspend" target="_blank" class="danger">停用</a></td>');
                        echo('<td><a href="account_manage.php?u='.$row['id'].'&opr=del" target="_blank" class="danger">刪除</a></td>');
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
