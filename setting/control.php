<?php
//Control Panel
	//0-None, 1-Use
	$_useMysql = 1; 
	$_usePhpLibrary = 0; 
	$_useGzipCompress = 1;
	$_useDebug = 1; // 0 - Off, 1 - Simple, 2 - All
	$_useSession = 1;
	$_useHTTPs = 0;

// Configuration
$_q = "'";
$_p = '"';
$_url = "http".($_useHTTPs == 1 ? "s" : "")."://" . $_SERVER['SERVER_NAME']."/";
$_fullUrl = "http".($_useHTTPs == 1 ? "s" : "")."://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$_phpPath = $_SERVER["DOCUMENT_ROOT"]."/";

// Include User Config
include_once $_phpPath."setting/config.php";

// Page Setting
$_copyRight = "Copyright ".$_sitePublisher." &copy; ". $_siteCopyrightYear;

// PHP FUNCTION
	// PHP Library Include
	/*if ($phplibraryuse_s == 1){
		include_once $_phpPath."setting/php_library.php";
	}*/
	// Connecting MySQL
	if ($_useMysql == 1){
		include_once $_phpPath."setting/mysql_config.php";
	}
	
	/*if ($gzipcompress_s == 1){
		if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start('ob_gzhandler'); else ob_start(); 	
	}
	if ($turnofferror_s == 0){
		error_reporting(0);	
	}
	if ($usesession_s == 1){
		session_start();
	}*/
?>