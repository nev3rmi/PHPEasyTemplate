<?php
foreach (getAllFileInFolderWithType($_phpPath.'css', 'css') as $cssFile){
	echo '<link rel="stylesheet" href="'.$_url.'css/'.$cssFile.'">';
}
?>