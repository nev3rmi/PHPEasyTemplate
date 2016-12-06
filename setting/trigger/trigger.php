<?php
include_once $_phpPath."/setting/trigger/sqlstore.php"; // asdjnoqlinfkjvnu!Q@#EDASnkjnasdfkjn!@#E|+
include_once $_phpPath."/setting/trigger/emailstore.php"; // asdjnoqlinfkjvnu!Q@#EDASnkjnasdfkjn!@#E|++
include_once $_phpPath."/setting/trigger/smtpstore.php"; // asdjnoqlinfkjvnu!Q@#EDASnkjnasdfkjn!@#E|+++
include_once $_phpPath."/setting/installer/privatekey.php"; // Get Private Key

if ($_privateKey != NULL){
	$decryptPrivateKey = base64_decode(hex2bin(strrev($_privateKey)));
	$explodeKey = explode('|',$decryptPrivateKey);
	$selectedKey = array();
	foreach($explodeKey AS $key)
		{
		   if(strlen($key) > 1)
			  array_push($selectedKey,$key);
		}
}else{
	echo "<br><span color='red'>Cannot Define Private Key</span>";
	echo "<br><a href='".$_url."setting/installer/'>Please create the private key!</a>";	
	exit();	
}

if ($_sqlEncryptString != NULL){
function decryptSQLString($encryptSQLString, $iv_size, $privateKey){
	$ciphertext_dec = base64_decode($encryptSQLString);
    $iv_dec = substr($ciphertext_dec, 0, $iv_size);
    $ciphertext_dec = substr($ciphertext_dec, $iv_size);
    $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey,
                                    $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
    return $plaintext_dec;
}

$sqlPackage = decryptSQLString($_sqlEncryptString, $selectedKey[1], $selectedKey[0]);
}else{
	echo "<br><span color='red'>Cannot Define SQL Connect Package</span>";
	echo "<br><a href='".$_url."setting/installer/'>Please config your database!</a>";	
	exit();
}
?>