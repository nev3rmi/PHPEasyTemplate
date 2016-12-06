<?php
function consoleData($data) {
		echo (is_array($data)?"<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>":"<script>console.log( 'Debug Objects: " . $data . "' );</script>");
}
function generateRandomString($length = 64) {
	$characters = '0123456789abcdefABCDEF';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
function encryptSQLString($hostname, $username, $password, $database){
	try{
		$string = $hostname."|".$username."|".$password."|".$database."|+";
		// Create Private Key
		$privateKey = pack('H*', generateRandomString()); 
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$encryptKey = strrev(bin2hex(base64_encode($privateKey."|".$iv_size."|+")));
		// Insert to privatekey
		$fileOpen = fopen("./privatekey.php","w");
		(fwrite($fileOpen,'<?php $_privateKey = "'.$encryptKey.'";?>'))?consoleData("Create Private Key Success"):consoleData("Create Private Key Fail");
		fclose($fileOpen);
		$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey,$string, MCRYPT_MODE_CBC, $iv);
		$ciphertext = $iv . $ciphertext;
		$ciphertext_base64 = base64_encode($ciphertext);
		$fileOpen = fopen("../trigger/sqlstore.php","w");
		(fwrite($fileOpen,'<?php $_sqlEncryptString = "'.$ciphertext_base64.'";?>'))?consoleData("Create Encrypt SQL String Success"):consoleData("Create Encrypt SQL String Fail");
		fclose($fileOpen);
		consoleData("SQL String has been encrypted: ".$ciphertext_base64);
	}catch (Exception $e){
		consoleData("Error when encrypt SQL String: ".$e->getMessage());	
	}
	return ;	
}
encryptSQLString($_POST['hostname'],$_POST['username'],$_POST['password'],$_POST['database']);
header("Location: http://".$_SERVER['SERVER_NAME']."/");
?>