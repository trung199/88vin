<?php
error_reporting(0);
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Ho_Chi_Minh");

// parse all header
$all_header = getallheaders();
$list_header = array();
foreach ($all_header as $key => $value) {
	if ($key == "Host" || $key == "Accept-Encoding" || $key == "Connection") continue;
	$list_header[] = "$key: $value";
}
$list_header[] = "Connection: close";


function get_config($gate) {
	$system_config = json_decode(file_get_contents("haipham2020.json"), true);
	$gate_config = $system_config[$gate];
	return $gate_config;
}

function send_telegram($message)
{
    $url = 'https://api.telegram.org/bot1352939330:AAGZ7KmtKyTzwSZ9IxIUUsb9dZ__jUckmbA/sendMessage?chat_id=-1001347441671&text='.urlencode($message);
    file_get_contents($url);
}

function send_telegram_transfer($message)
{
    $url = 'https://api.telegram.org/bot1352939330:AAGZ7KmtKyTzwSZ9IxIUUsb9dZ__jUckmbA/sendMessage?chat_id=-1001410475965&text='.urlencode($message);
    file_get_contents($url);
}
?>