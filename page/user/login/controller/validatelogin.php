<?php include_once realpath($_SERVER["DOCUMENT_ROOT"])."/setting/control.php"; ?>
<?php
$email = htmlentities($_POST['userEmail'], ENT_QUOTES, "UTF-8");
$password =  htmlentities($_POST['userPassword'], ENT_QUOTES, "UTF-8");

if (!RegexCheck($regexMail,$email)){
	exit();
}

?>