<?php
include_once "/home/s3568988/public_html/setting/config.php";
?>
<?php
function regexCheck($regexPattern, $string){
	return (preg_match($regexPattern,$string)?true:false);
}

function redirectURL ($redirectInSecond, $toURL){
	return header( "refresh:".$redirectInSecond.";url=".$toURL."" );	
}
function debugToConsole( $data ) {
// IN Test
    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    //echo $output;
}
function creatingHash_Nev($a){
		// Reverse
		$encrypt_r_[0] = strrev($a);
		// Bin2Hex
		$encrypt_r_[1] = bin2hex($encrypt_r_[0]);
		// Base 64 Encode 
		$encrypt_r_[2] = base64_encode($encrypt_r_[1]);
		// MD5 Encode  
		$encrypt_r_[3] = md5($encrypt_r_[2]); 
		// Hash512
		$encrypt_r_[4] = hash('sha512', $encrypt_r_[3]);
		// Finish Here
		$encrypt_r_done = $encrypt_r_[4];
		return $encrypt_r_done;
	}
	function creatingSalt($a){
		// Create Salt
		$salt_r_[0] = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB), MCRYPT_DEV_URANDOM);
		$salt_r_[1] = '$6$'.substr(str_shuffle("!@#$%^&*()./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz012345â€Œâ€‹6789".$salt_r_[0]), 0, 25); 
		$salt_r_[2] = crypt($a,$salt_r_[1]);
		// Encrypt Salt
		$salt_r_[3] = bin2hex(strrev(base64_encode(strrev(base64_encode($salt_r_[2])))));
		// Finish Here
		$encrypt_r_done = $salt_r_[3];
		return $encrypt_r_done;
	}
	function validUserandPassin($a,$b){
		if (password_verify($a,base64_decode(strrev(base64_decode(strrev(hex2bin($b))))))){
			return true;	
		}else{
			return false;
		}
	}

?>
<?php
//Regex Library
$regexMail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
$regexPassword = "";

?>