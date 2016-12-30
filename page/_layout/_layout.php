<?php require_once $_SERVER['DOCUMENT_ROOT']."/setting/config.php"; ?>
<?php
class Page(){
	function SystemPath($thisPagePath){
		return $_phpPath.$thisPagePath;
	}
	function Name($thisPageName){
		return $thisPageName;
	}
	function Meta($thisPageMeta){
		return $thisPageMeta;
	}
	function Heading($thisPageHeading){
		return $thisPageHeading;
	}
	function SubHeading($thisPageSubHeading){
		return $thisPageSubHeading;
	}
	function Controller(){
		
	}
}
?>