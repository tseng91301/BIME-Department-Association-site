<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
$key = $_SERVER['HTTP_TRANSFER_SECRET_KEY'];
if($key != $HTTP_TRANSFER_SECRET_KEY) {
    http_response_code(400);
    echo("Invalid Request!");
    return;
}

$uid = $_POST["uid"];
$type = $_POST["type"];
if($type == "text") {
    $response_txt = $_POST["data"];
    $response = [
        "type" => "text",
        "data" => $response_txt."\nUID=".$uid
    ];
    echo(json_encode($response));
}
?>
