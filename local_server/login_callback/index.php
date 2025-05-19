<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
// LINE API 設定
$LINE_CHANNEL_ID = $LINE_LOGIN_ID;
$LINE_CHANNEL_SECRET = $LINE_LOGIN_SECRET;
$REDIRECT_URI = $LINE_LOGIN_REDIRECT_URI;

// 獲取 URL 參數
$code = $_GET['code'] ?? null;
$state = $_GET['state'] ?? null;

// 驗證 state
if ($state !== "fsaafjwri20ttga0hwpjisg0t5") {
    http_response_code(400);
    echo "Bad request";
    exit;
}

// 獲取 access token
$token_url = 'https://api.line.me/oauth2/v2.1/token';
$data = [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $REDIRECT_URI,
    'client_id' => $LINE_CHANNEL_ID,
    'client_secret' => $LINE_CHANNEL_SECRET,
];

// 使用 cURL 發送 POST 請求
$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
$response = curl_exec($ch);
curl_close($ch);

// 解碼 JSON 資料
$token_data = json_decode($response, true);
if (!isset($token_data['access_token'])) {
    echo "Failed to get access token";
    exit;
}

// 獲取用戶資訊
$access_token = $token_data['access_token'];
$user_info_url = 'https://api.line.me/v2/profile';

$ch = curl_init($user_info_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $access_token"
]);
$user_info_response = curl_exec($ch);
curl_close($ch);

// 解碼用戶資訊
$user_info = json_decode($user_info_response, true);
if (!$user_info) {
    echo "Failed to get user info";
    exit;
}

// 渲染 HTML 表單
?>
<!DOCTYPE html>
<html lang="zh-tw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Redirect</title>
</head>
<body>
    <form id="myForm" action="/profile/entrance/index.php" method="post">
        <?php foreach ($user_info as $key => $value): ?>
            <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
        <?php endforeach; ?>
        <input type="submit" value="若無反應請按此鈕跳轉">
    </form>
</body>
<script type="text/javascript">
    document.getElementById('myForm').submit();
</script>
</html>
