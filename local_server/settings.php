<?php
    $mysql_servername = "localhost";
    $mysql_username = "bime";
    $mysql_password = "BIME2024aewBHDBzrag493903gvag9u835gg834";
    $mysql_dbname = "bime_line_api_user";

    $notification_root = "/mnt/disk1/linebot_notifications";

    // Line Bot server
    $LINE_CHANNEL_ACCESS_TOKEN = "FDU2AVG0tg4kRzggHK5tzTG/k3AlhNn/Y3kWKxc2txEtlEzZfYMto3f9dbccanHazK013uDudUW06GJF+GPzHiibcdKgh3bmE0gtNrYXp6dSj4Plm2XOCUPzSiEBi66ox/rVr8C0QykFvCSGRcfxTgdB04t89/1O/w1cDnyilFU=";
    $LINE_CHANNEL_SECRET = "95d27ff6231608b08d1d08dc13aca921";
    $LINE_LOGIN_ID = "2005653179";
    $LINE_LOGIN_SECRET = "cdd472be281682433a879a834840949d";
    $LINE_LOGIN_REDIRECT_URI = "https://blessed-dogfish-morally.ngrok-free.app/login_callback?authenticator=line";
    $HTTP_TRANSFER_SECRET_KEY = "ZmFzZ2lmeXF3NHQ5ODR5ZzBxaGd0Z2hvOWd1andodGliZ3NybztlaHRbYWEnXQ==";

    function generateRandomString($length = 20) {
        // 定義可用的字符集合
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
    
        // 使用 random_bytes 生成隨機字節
        $bytes = random_bytes($length);
    
        // 將隨機字節轉換為英數字字符
        for ($i = 0; $i < $length; $i++) {
            $index = ord($bytes[$i]) % $charactersLength;
            $randomString .= $characters[$index];
        }
    
        return $randomString;
    }

    function find_notification_access_token($mysql_proc, $uid) {
        $sql = "SELECT * FROM bime_linebot_users WHERE uid='$uid'";
        $result = $mysql_proc->query($sql);
        $row = $result->fetch_assoc();
        if($row['notify_access_token'] != null) {
            return $row['notify_access_token'];
        }else {
            return "";
        }
    }

    function send_line_text_notification($nid, $text) {
        if($nid == null || $nid == "") {
            return 0;
        }
        // Send message
        $token_url = 'https://notify-api.line.me/api/notify';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $nid"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // 设置请求方法为 POST
        $data = [
            'message' => $text
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // 将数据编码为 URL 查询字符串
        $response = curl_exec($ch);
        $success = 0;
        if (curl_errno($ch)) {
            $success = 0;
        } else {
            $success = 1;
        }
        curl_close($ch);
        return $success;
    }
?>