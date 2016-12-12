<?php
// Create priority for jquery.js must be load first
foreach (priorityKey(getAllFileInFolderWithType($_phpPath.'js', 'js'), "jquery-3.1.1.min.js") as $jsFile){
	echo '<script src="'.$_url.'js/'.$jsFile.'"></script>';
}
foreach (getAllFileInFolderWithType($_phpPath.'page'.(empty(substr($_SERVER['REQUEST_URI'],1))?'/index':$_SERVER['REQUEST_URI']).'/view/js', 'js') as $jsFile){
	echo '<script src="'.$_url.'page/'.(empty(substr($_SERVER['REQUEST_URI'],1))?'index':$_SERVER['REQUEST_URI']).'/view/js/'.$jsFile.'"></script>';
}
?>
<script src="//fast.eager.io/kcBx5lOVwX.js"></script>