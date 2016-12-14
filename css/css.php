<?php

// Load general css
foreach (getAllFileInFolderWithType($_phpPath.'css', 'css') as $cssFile){
	echo '<link rel="stylesheet" href="'.$_url.'css/'.$cssFile.'">';
}
// Load specific folder css .'page'.$_SERVER['REQUEST_URI']
// First level get
/* 
Check by get all by count subfolder level, if 3 level check 3 times, get all view folder and go to css folder get file.
*/
// Need to check how many / then loop and find view get all css and js 
foreach (getAllFileInFolderWithType($_phpPath.'page'.(empty(substr($_SERVER['REQUEST_URI'],1))?'/index':$_SERVER['REQUEST_URI']).'/view/css', 'css') as $cssFile){
	echo '<link rel="stylesheet" href="'.$_url.'page/'.(empty(substr($_SERVER['REQUEST_URI'],1))?'index':$_SERVER['REQUEST_URI']).'/view/css/'.$cssFile.'">';
}

///////////////////////////////////////////////////
// Version 2
// On Work
consoleData($_documentPath);
//
// On Test
$getPath = explode("/","/product/viewdetail/test/sub1");
$countGetPath = count($getPath);

for ($x = 1; $x < $countGetPath; $x++){
	consoleData("Path Number: ".$x." ,Path name:".$getPath[$x]);
}


?>