<?php
if ($sqlPackage != NULL){
	// TODO: Need to implement multiple DB Connect As Well
	// Seperate the Package
	$explodeSQLKey = explode('|',$sqlPackage);
	$selectedSQLKey = array();
	foreach($explodeSQLKey AS $key)
		{
		   if(strlen($key) > 1)
			  array_push($selectedSQLKey,$key);
		}
	
	// Globals Variable
	$GLOBALS['hostname'] = $selectedSQLKey[0];
	$GLOBALS['username'] = $selectedSQLKey[1];
	$GLOBALS['password'] = $selectedSQLKey[2];
	$GLOBALS['database'] = $selectedSQLKey[3];
	
	// Function to connect to DB
	function ConnectDB($hostname, $username, $password, $database){
		$_db = new mysqli($hostname, $username, $password, $database);
		if($_db->connect_error){
     		die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
		}
		return $_db;
	}
}else{
	consoleData("Please insert database information");
	echo "<br><a href='".$_url."setting/installer/'>Please config your database!</a>";	
	exit();
}
?>