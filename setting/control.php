<?php
// Priority
$_phpPath = $_SERVER['DOCUMENT_ROOT']."/";
// Include User Config
include_once $_phpPath."setting/config.php";


// Configuration
$_q = "'";
$_p = '"';
$_url = "http".($_useHTTPs == 1 ? "s" : "")."://" . $_SERVER['SERVER_NAME']."/";
$_fullUrl = "http".($_useHTTPs == 1 ? "s" : "")."://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$_documentPath = $_SERVER['REQUEST_URI'];

// Page Setting
$_copyRight = "Copyright ".$_sitePublisher." &copy; ". $_siteCopyrightYear;

// PHP FUNCTION
	// PHP Library Include
	if ($_usePhpLibrary == 1){
		include_once $_phpPath."setting/php_library.php";
	}
	// Trigger Package: SQL, SMTP, Email
	if ($_useTrigger == 1){
		include_once $_phpPath."setting/trigger/trigger.php";
	}
		// Connecting MySQL
		if ($_useMysql == 1){
			include_once $_phpPath."setting/mysql_config.php";
		}
	
	if ($_useGzipCompress == 1){
		if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start('ob_gzhandler'); else ob_start(); 	
	}
	/*
	if ($turnofferror_s == 0){
		error_reporting(0);	
	}
	if ($usesession_s == 1){
		session_start();
	}*/
?>