<?php
////////////////////////////////////////////////////
// Version 1
// Load general css
/*
foreach (getAllFileInFolderWithType($_phpPath.'css', 'css') as $cssFile){
	echo '<link rel="stylesheet" href="'.$_url.'css/'.$cssFile.'">';
}

foreach (getAllFileInFolderWithType($_phpPath.'page'.(empty(substr($_SERVER['REQUEST_URI'],1))?'/index':$_SERVER['REQUEST_URI']).'/view/css', 'css') as $cssFile){
	echo '<link rel="stylesheet" href="'.$_url.'page/'.(empty(substr($_SERVER['REQUEST_URI'],1))?'index':$_SERVER['REQUEST_URI']).'/view/css/'.$cssFile.'">';
}
*/

///////////////////////////////////////////////////
// Version 2
// On Work
// consoleData($_documentPath);
//
// On Test

// Get Private chi co minh no
/*
Vi du:
	css > product
	Tat ca cac file trong product chi co minh no dc xem


*/
// Va tat ca cac Public thi se co may thang khac nua
/*
Vi du:
	css > product > public
	css > product > viewdetail 
	
	view viewdetail se thua huong tu css product > public
	
	neu cay dai` hon
	
	css > product > viewdetail > item
	
	Thi item se thua huong cua
	css > product > public
	css > product > viewdetail > public
	

*/
// Should be in config.php to re-use


for ($x = 0; $x < $_countGetPath; $x++){
	// Get All Public Inheritent File
	$_combineLine[$x] = $_combineLine[$x - 1].$_getPath[$x].'/';
	//consoleData('Path: '.$_phpPath.'css'.$combineLine[$x]);
	//consoleData(getAllFileInFolderWithType($_phpPath.'css'.$combineLine[$x], 'css'));
	foreach (getAllFileInFolderWithType($_phpPath.'css'.$_combineLine[$x], 'css') as $cssFile){
		echo('<link rel="stylesheet" href="'.$_url.'css'.$_combineLine[$x].$cssFile.'">');
	}
	// Get Private File
	if ($x == ($_countGetPath - 1)){
		// consoleData($_phpPath.'css'.$_documentPath.'/private');
		foreach (getAllFileInFolderWithType($_phpPath.'css'.$_combineLine[$x].'private', 'css') as $cssFile){
			echo('<link rel="stylesheet" href="'.$_url.'css'.$_combineLine[$x].'private/'.$cssFile.'">');
		}
	}
}
?>