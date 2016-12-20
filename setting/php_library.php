<?php

function RegexCheck($regexPattern, $string){
	return (preg_match($regexPattern,$string)?true:false);
}
function RedirectURL ($redirectInSecond, $toURL){
	return header( "refresh:".$redirectInSecond.";url=".$toURL."" );	
}
function ConsoleData($data) {
	echo (is_array($data)?"<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>":"<script>console.log( 'Debug Objects: " . $data . "' );</script>");
}

function GetIpUser(){
	return (!empty($_SERVER['HTTP_CLIENT_IP']))?$ip = $_SERVER['HTTP_CLIENT_IP']:(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))?$ip = $_SERVER['HTTP_X_FORWARDED_FOR']:$ip = $_SERVER['REMOTE_ADDR'];
}
function ConnectSQL($connectString){
	
}
function FileReader ($fileDirectory){
	if ($openFile = fopen($fileDirectory, "r")){
		$readFile = fread($openFile,filesize($fileDirectory));
		fclose($openFile);
		return $readFile;
	}
		return "Cannot Read File";	
}
function FileWriter ($fileDirectory){
	
}

function GenerateLinkPath($path,$_url){
	$explode = explode("/",$path);
	$countExplode = count($explode);
	$link[0] = $_url;
	for ($x = 1; $x < $countExplode; $x++){
		$link[$x] = $link[$x-1].$explode[$x].'/';
		$result .= '<a href="'.substr($link[$x],0,-1).'">'.ucfirst($explode[$x]).'</a>'.' > ';
	}
	return substr($result,0,-2);
}
function GetAllFileInFolder($path){
	return array_diff(scandir($path),array('..', '.'));	
}
function GetAllFileInFolderWithType($path, $fileType){
	$listOfAllFile = array_diff(scandir($path),array('..', '.'));
	return preg_grep("/^.*\.(".$fileType.")$/", $listOfAllFile);
}
function PriorityKey($array, $value){
    $key = array_search($value, $array);
    if($key){
		unset($array[$key]);
		array_unshift($array, $value);  
    } 
	return $array;
}
function isSSL()
{
	if( !empty( $_SERVER['https'] ) )
		return true;

	if( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
		return true;

	return false;
}

include_once $_phpPath."setting/encrypt_library.php";
?>
