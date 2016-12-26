<?php
// Shortcut connect DB
function Db(){
	$_db = ConnectDB($GLOBALS['hostname'],$GLOBALS['username'],$GLOBALS['password'],$GLOBALS['database']);
	return $_db;
}
?>