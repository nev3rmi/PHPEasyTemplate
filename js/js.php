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
// TODO: Need re check some value get wrong
for ($x = 0; $x < $_countGetPath; $x++){
	// Get All Public Inheritent File
	$_combineLine[$x] = $_combineLine[$x - 1].$_getPath[$x].'/';
	foreach (priorityKey(getAllFileInFolderWithType($_phpPath.'js'.$_combineLine[$x], 'js'), "jquery-3.1.1.min.js") as $jsFile){
		echo('<script type="text/javascript" src="'.$_url.'js'.$_combineLine[$x].$jsFile.'"></script>');
		//consoleData($jsFile);
	}
	// Include all .php except this file
	foreach (getAllFileInFolderWithType($_phpPath.'js'.$_combineLine[$x], 'php') as $jsFile){
		//echo('<script type="text/javascript" src="'.$_url.'js'.$_combineLine[$x].$jsFile.'"></script>');
		if (basename(__FILE__, '.') != $jsFile){ 
			//consoleData($jsFile);
			include_once $_phpPath.'js'.$_combineLine[$x].$jsFile;
		}
	}
	// Get Private File
	if ($x == ($_countGetPath - 1)){
		//consoleData($_phpPath.'js'.$_combineLine[$x].'private');
		foreach (getAllFileInFolderWithType($_phpPath.'js'.$_combineLine[$x].'private', 'js') as $jsFile){
			echo('<script type="text/javascript" src="'.$_url.'js'.$_combineLine[$x].'private/'.$jsFile.'"></script>');
		}
		foreach (getAllFileInFolderWithType($_phpPath.'js'.$_combineLine[$x].'private', 'php') as $jsFile){
			if (basename(__FILE__, '.') != $jsFile){ 
				//consoleData($jsFile);
				include_once $_phpPath.'js'.$_combineLine[$x].'private/'.$jsFile;
			}
		}
	}
}
?>
<script src="//fast.eager.io/kcBx5lOVwX.js"></script>
