<?php
include "get_config.php";

// lấy config
$gate = isset($_GET["gate"]) ? $_GET["gate"] : "G88";
$gate_config = get_config($gate);

//bóc tách dữ liệu
$raw_content = file_get_contents("php://input");
if ($gate_config['disable_change_password']) {
	die('{"c":-1023,"m":""}');
}

// change password
echo change_password($raw_content, $list_header);

function change_password($raw_content, $list_header)
{
	global $gate_config;
	$url = "https://id.".$gate_config["domain"]."/api/Account/ChangePassword";
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