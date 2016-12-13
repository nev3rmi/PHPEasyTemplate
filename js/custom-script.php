<?php
if (!empty($_SESSION['id'])){
	echo '<script type="text/javascript" defer>$(function(){loadLoginNavigationBar();});</script>';			
}else{
	echo '<script type="text/javascript" defer>$(function(){loadBeforeLoginNavigationBar();});</script>';	
}
?>
