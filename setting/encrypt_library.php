<?php
function GenerateRandomString($length = 64) {
	$characters = '0123456789abcdefABCDEF';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function EncryptKey($decryptkey){
	return strrev(bin2hex(base64_encode($decryptkey)));
}

function DecryptKey($encryptKey){
	return base64_decode(hex2bin(strrev($encryptKey)));
}

function CreatePrivateKey(){
	$privateKey = pack('H*', GenerateRandomString()); 
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$key = $privateKey."|".$iv_size."|".$iv."|+";
	$encryptKey = EncryptKey($key);
	
	return $encryptKey;
}


function CreatingHash($string){
	// Reverse
	$encryptStep[0] = strrev($string);
	// Bin2Hex
	$encryptStep[1] = bin2hex($encryptStep[0]);
	// Base 64 Encode 
	$encryptStep[2] = base64_encode($encryptStep[1]);
	// MD5 Encode  
	$encryptStep[3] = md5($encryptStep[2]); 
	// Hash512
	$encryptStep[4] = hash('sha512', $encryptStep[3]);
	// Finish Here
	return $encryptFinish = $encryptStep[4];
}
function CreatingSalt($string){
	// Create Salt
	$makeSalt[0] = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB), MCRYPT_DEV_URANDOM);
	$makeSalt[1] = '$6$'.substr(str_shuffle("!@#$%^&*()./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz012345‌​6789".$makeSalt[0]), 0, 25); 
	$makeSalt[2] = crypt($string,$makeSalt[1]);
	// Encrypt Salt
	$makeSalt[3] = bin2hex(strrev(base64_encode(strrev(base64_encode($makeSalt[2])))));
	// Finish Here
	return $makeSaltFinish = $makeSalt[3];
}
function ValidPassword($hashPassword,$salt){
	return (password_verify($hashPassword,base64_decode(strrev(base64_decode(strrev(hex2bin($salt)))))))?true:false;
}


function encryptData($decrypted, $privateKey, $password) { 
// Build a 256-bit $key which is a SHA256 hash of $salt and $password.
$key = hash('SHA256', $password . $privateKey, true);
// Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
// Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
// We're done!
return $iv_base64 . $encrypted;
} 

function decryptData($encrypted, $privateKey, $password ) {
// Build a 256-bit $key which is a SHA256 hash of $salt and $password.
$key = hash('SHA256', $password . $privateKey, true);
// Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
$iv = base64_decode(substr($encrypted, 0, 22) . '==');
// Remove $iv from $encrypted.
$encrypted = substr($encrypted, 22);
// Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
// Retrieve $hash which is the last 32 characters of $decrypted.
$hash = substr($decrypted, -32);
// Remove the last 32 characters from $decrypted.
$decrypted = substr($decrypted, 0, -32);
// Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
if (md5($decrypted) != $hash) return false;
// Yay!
return $decrypted;
}

?>