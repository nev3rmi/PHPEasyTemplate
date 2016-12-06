<?php
if ($sqlPackage != NULL){
	$explodeSQLKey = explode('|',$sqlPackage);
	$selectedSQLKey = array();
	foreach($explodeSQLKey AS $key)
		{
		   if(strlen($key) > 1)
			  array_push($selectedSQLKey,$key);
		}
	
	$_db = mysqli_connect($selectedSQLKey[0], $selectedSQLKey[1], $selectedSQLKey[2], $selectedSQLKey[3]);
	if (!$_db){
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
		echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}
}else{
	consoleData("Please insert database information");
	echo "<br><a href='".$_url."setting/installer/'>Please config your database!</a>";	
	exit();
}
?>