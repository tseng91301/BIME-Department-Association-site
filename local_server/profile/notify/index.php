<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
session_start();
?>
<?php
if($_SESSION['login'] != 1){
    echo("Not logged in");
    $_SESSION['login_redirect'] = "/profile/notify/index.php";
    echo("<script>document.location.href=\"https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=2005653179&redirect_uri=https://ntu-bime-linebot.onrender.com/login_callback?authenticator=line&state=fsaafjwri20ttga0hwpjisg0t5&scope=openid%20profile&nonce=helloWorld&prompt=consent&max_age=3600&ui_locales=zh-TW&bot_prompt=normal\";</script>");
    return;
}else{
    if($_SESSION['login_redirect'] == '/profile/notify/index.php'){
        unset($_SESSION['login_redirect']);
    }
}
?>

<?php
if(isset($_GET['code'])){

    // Check whether code is valid
    $code = escapeshellcmd($_GET['code']);
    $state = escapeshellcmd($_GET['state']);
    if($state != "fsdfdsgwrgwgrehs"){
        http_response_code(403);
        echo("Invalid state!");
        return;
    }

    // Get user information from Line server
    $token_url = 'https://notify-bot.line.me/oauth/token';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true); // 设置请求方法为 POST
    $data = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => 'https://ntu-bime-linebot.onrender.com/profile/notify/index.php',
        'client_id' => 'KPvbmdv1KmZTpd4u2uGDP3',
        'client_secret' => 'joPvAs5YFAVoDInFg4AliaQo1xUw9RiJCsom7R0wnpQ'
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // 将数据编码为 URL 查询字符串
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        // Store user notify access token into database
        // 处理响应
        $user_info = json_decode($response, true);
        if($user_info['access_token'] != null){
            // 创建连接
            $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
            // 检查连接
            if ($conn->connect_error) {
                die("Connection failed...: " . $conn->connect_error);
            }

            // Insert notify id into table
            $sql = "UPDATE bime_linebot_users SET notify_access_token='".$user_info['access_token']."' WHERE uid='".$_SESSION['userId']."'";
            $result = $conn->query($sql);

            echo("<script>alert('已成功訂閱通知！未來您將會在Line Notify接收到來自NTU BIME的最新消息。若收不到，請使用手機重新操作。')</script>");
        }
        
    }
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/themify-icons.css" rel="stylesheet">
    <link href="/module/css/global-style.css" rel="stylesheet">  
    <!-- <link href="/info/style.css" rel="stylesheet"> -->
    <title>通知管理</title>
</head>
<body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/module/js/size-change.js"></script>
    <div class="floating">
        <span class="ti-close floating-close"></span>
        <div class="floating-content"></div>
        <script type="text/javascript" src="/module/js/dev-login.js"></script>
    </div>
    <div class="page">
        <div id="page-top" class="page-top"></div>
        <div class="page-center" id="page-center">
            <div id="notify-all-page" class="notify-all-page">
                <div class="notify-left-bar">
                    <div class="notify-left-bar-row notify-left-bar-row-not-selected" id="public-notify">
                        <span class="ti-announcement"></span>
                    </div>
                    <div class="notify-left-bar-row notify-left-bar-row-not-selected" id="private-notify">
                        <span class="ti-email"></span>
                    </div>
                    <div class="notify-left-bar-row notify-left-bar-row-not-selected" id="notify-settings">
                        <span class="ti-settings"></span>
                    </div>
                    <div class="notify-left-bar-block"></div>
                </div>
                <div class="notify-center-info" id="notify-center-info">
                    <div id="public-notify-content" class="center-info-content center-info-hidden">
                    <?php
                    $userId = $_SESSION['userId'];
                    // 创建连接
                    $conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
                    // 检查连接
                    if ($conn->connect_error) {
                        die("Connection failed...: " . $conn->connect_error);
                    }

                    // Checking public notifications
                    $sql = "SELECT * from bime_linebot_notifications WHERE target_user='all'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                            echo("<div class='notify-index-cell' onclick='window.location.href=\"notify_detail.php?id=".$row['id']."\"'>");
                            echo("<h1>".$row['title']."</h1>");
                            echo("<p>".$row['detail_line']."</p>");
                            echo("<div class='notify-index-cell-from'>");
                            echo("<span>Add by ".$row['add_by']." at ".$row['add_date']."</span>");
                            echo("</div>");
                            echo("</div>");
                        }
                    }else{
                        echo("<h3>No other notification</h3>");
                    }
                    ?>
                    </div>
                    <div id="private-notify-content" class="center-info-content center-info-hidden">
                    <?php
                    // Checking private notifications
                    $sql = "SELECT * from bime_linebot_notifications WHERE target_user IS NULL";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                            $receive_users_raw = file_get_contents($notification_root."/".$row['id'].".json");
                            $receive_users = json_decode($receive_users_raw, true)['target'];
                            // var_dump($receive_users);
                            // var_dump($userId);
                            if(in_array($userId, $receive_users)){
                                echo("<div class='notify-index-cell' onclick='window.location.href=\"notify_detail.php?id=".$row['id']."\"'>");
                                echo("<h1>".$row['title']."</h1>");
                                echo("<p>".$row['detail_line']."</p>");
                                echo("<div class='notify-index-cell-from'>");
                                echo("<span>Add by ".$row['add_by']." at ".$row['add_date']."</span>");
                                echo("</div>");
                                echo("</div>");
                            }
                        }
                    }else{
                        echo("<h3>No other notification</h3>");
                    }
                    ?>
                    </div>
                    <div id="notify-settings-content" class="center-info-content center-info-hidden">
                        <button id="subscribe-notify-btn" onclick="window.location.href = 'https://notify-bot.line.me/oauth/authorize?response_type=code&client_id=KPvbmdv1KmZTpd4u2uGDP3&redirect_uri=https://ntu-bime-linebot.onrender.com/profile/notify/index.php&scope=notify&state=fsdfdsgwrgwgrehs'" style="background-color: #78ff82;">訂閱Line Notify 通知</button>
                        <button id="test-notify-btn" onclick="window.open('test.php');" style="background-color: #6be4ff;">測試Line Notify 通知</button>
                        <button id="test-notify-btn" onclick="window.open('revoke.php');" style="background-color: #ffa1a1;">取消Line Notify 通知</button>
                    </div>
                </div>
            </div>
        </div><!-- div class="page-center" -->
        <div class="page-footer" id="page-footer"></div>
    </div>
    <script src="/module/js/floating.js"></script>
    <script>
        $("#page-top").load("/module/top.php");
        $("#page-footer").load("/module/foot.html");
    </script>
    <script>
        {
            var default_display_id = 'public-notify';
            $("#"+default_display_id).removeClass('notify-left-bar-row-not-selected');
            $("#"+default_display_id+"-content").removeClass('center-info-hidden');
        }
        
        $(".notify-left-bar-row").click(function(){
            var clickedElementId = this.id;
            $(".notify-left-bar-row").addClass('notify-left-bar-row-not-selected');
            $(this).removeClass('notify-left-bar-row-not-selected');
            var display_id = clickedElementId + '-content';
            $(".center-info-content").addClass('center-info-hidden');
            $("#"+display_id).removeClass('center-info-hidden');
        })
    </script>
    
</body>
<!-- /info/style.css -->
<style>
.notify-all-page {
    display: flex;
    flex-direction: row;
    align-items: stretch;
    justify-content: center;
}

.notify-left-bar {
    min-width: 80px;
    max-width: 110px;
    min-height: 70vh;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-end;
}

.notify-left-bar-row {
    width: 70%;
    min-height: 60px;
    max-height: 100px;
    padding: 10px;
    border-top: 2px inset;
    border-left: 2px inset;
    border-bottom: 2px inset;
    border-top-left-radius: 20px;
    border-bottom-left-radius: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.notify-left-bar-row span {
    font-size: 33px;
}

.notify-left-bar-row-not-selected {
    border-right: 2px inset;
    background-color: rgb(177 177 177)
}

.notify-center-info {
    min-width: 80vw;
    min-height: 80vh;
    background-color: #ffffff;
    border-top: 2px inset;
    border-right: 2px inset;
    border-bottom: 2px inset;
}

.center-info-content {
    display: flex;
    flex-direction: column-reverse;
}

#notify-settings-content button {
    width: 20vw;
    min-width: 150px;
    padding: 10px;
    border-style: inset;
    margin: 20px;
}

.border-right-enable {
    border-right: 2px solid;
}

.center-info-hidden {
    display: none;
}

.notify-index-cell {
    background-color: white;
    border-bottom: 2px inset;
    padding: 20px;
}

.notify-index-cell h1 {
    font-size: 25px;
    font-family: system-ui;
    font-weight: bold;
}

.notify-index-cell p {
    margin-left: 35px;
    font-family: monospace;
    font-size: 15px;
}

.notify-index-cell-from {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin-right: 10px;
}

.notify-index-cell span {
    color: #8c8c8c;
    font-size: 12px;
}
</style>
</html>