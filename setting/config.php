<?php
//0-None, 1-Use
$_useTrigger = 1; // This include config for SMTP, Email, SQL
	$_useMysql = 1; 
$_usePhpLibrary = 1; 
$_useGzipCompress = 1;
$_useDebug = 1; // 0 - Off, 1 - Simple, 2 - All
$_useSession = 1;
$_useHTTPs = 1; // Learn how to make a free SSL: http://www.selfsignedcertificate.com/
$_useFileManager = 1;

// System Variable
$_siteName = "PHP Easy";
$_sitePublisher = "NeV3RmI";
$_siteCopyrightYear = "2013 - 2016";
$_siteVersion = "2.0.0.0";

// General Meta Variable


//Regex Library
$_regexMail = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
$_regexPassword = "";


// Connect DB String
$_dbHostName = $selectedSQLKey[0];	
$_dbUsername = $selectedSQLKey[1];	
$_dbPassword = $selectedSQLKey[2];
$_dbDatabase = $selectedSQLKey[3];

?>