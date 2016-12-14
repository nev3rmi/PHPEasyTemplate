<?php
/////////////////////////////////////////////////
// Version 1
// Create priority for jquery.js must be load first
/*foreach (priorityKey(getAllFileInFolderWithType($_phpPath.'js', 'js'), "jquery-3.1.1.min.js") as $jsFile){
	echo '<script type="text/javascript" src="'.$_url.'js/'.$jsFile.'"></script>';
}
foreach (getAllFileInFolderWithType($_phpPath.'page'.(empty(substr($_SERVER['REQUEST_URI'],1))?'/index':$_SERVER['REQUEST_URI']).'/view/js', 'js') as $jsFile){
	echo '<script type="text/javascript" src="'.$_url.'page/'.(empty(substr($_SERVER['REQUEST_URI'],1))?'index':$_SERVER['REQUEST_URI']).'/view/js/'.$jsFile.'"></script>';
}


/*
Need to auto import .php, maybe delete js-top
*/

//////////////////////////////////////////////////
// Version 2
for ($x = 0; $x < $_countGetPath; $x++){
	// Get All Public Inheritent File
	$_combineLine[$x] = $_combineLine[$x - 1].$_getPath[$x].'/';
	foreach (priorityKey(getAllFileInFolderWithType($_phpPath.'js'.$_combineLine[$x], 'js'), "jquery-3.1.1.min.js") as $jsFile){
		echo('<script type="text/javascript" src="'.$_url.'js'.$_combineLine[$x].$jsFile.'"></script>');
	}
}
// Get Private File

foreach (getAllFileInFolderWithType($_phpPath.'js'.$_documentPath.'/private', 'js') as $cssFile){
	echo('<script type="text/javascript" src="'.$_url.'js'.$_documentPath.'/private/'.$cssFile.'"></script>');
}
include_once $_phpPath.'js/custom-script.php';
include_once $_phpPath.'js/variables.php';
?>
<script src="//fast.eager.io/kcBx5lOVwX.js"></script>
