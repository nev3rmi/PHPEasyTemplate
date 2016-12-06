<?php
class PHPLibrary {
	function regexCheck($regexPattern, $string){
		return (preg_match($regexPattern,$string)?true:false);
	}
	function redirectURL ($redirectInSecond, $toURL){
		return header( "refresh:".$redirectInSecond.";url=".$toURL."" );	
	}
	function consoleData($data) {
		echo (is_array($data)?"<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>":"<script>console.log( 'Debug Objects: " . $data . "' );</script>");
	}
	function creatingHash($string){
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
	function creatingSalt($string){
		// Create Salt
		$makeSalt[0] = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB), MCRYPT_DEV_URANDOM);
		$makeSalt[1] = '$6$'.substr(str_shuffle("!@#$%^&*()./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz012345‌​6789".$makeSalt[0]), 0, 25); 
		$makeSalt[2] = crypt($string,$makeSalt[1]);
		// Encrypt Salt
		$makeSalt[3] = bin2hex(strrev(base64_encode(strrev(base64_encode($makeSalt[2])))));
		// Finish Here
		return $makeSaltFinish = $makeSalt[3];
	}
	function validPassword($salt,$hashPassword){
		return (password_verify($salt,base64_decode(strrev(base64_decode(strrev(hex2bin($hashPassword)))))))?true:false;
	}
	function getIpUser(){
		return (!empty($_SERVER['HTTP_CLIENT_IP']))?$ip = $_SERVER['HTTP_CLIENT_IP']:(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))?$ip = $_SERVER['HTTP_X_FORWARDED_FOR']:$ip = $_SERVER['REMOTE_ADDR'];
	}
	function connectSQL($connectString){
		
	}
	function fileReader ($fileDirectory){
		if ($openFile = fopen($fileDirectory, "r")){
			$readFile = fread($openFile,filesize($fileDirectory));
			fclose($openFile);
			return $readFile;
		}
			return "Cannot Read File";	
	}
	function fileWriter ($fileDirectory){
		
	}
}
?>
