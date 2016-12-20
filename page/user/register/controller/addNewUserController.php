<?php include_once realpath($_SERVER["DOCUMENT_ROOT"])."/setting/control.php"; ?>
<?php include_once $_phpPath."page/user/register/partialview/addNewUserPartialView.php"; ?>
<?php
$email = $_POST['registerEmail'];
$password = $_POST['registerPassword'];
$retypePassword = $_POST['registerRetypePassword'];
$termAndCondition = $_POST['registerRuleAccepted'];

// Regex Value

// Create Private Key
if (false == false){
		SetRegisterDialogToError("Cannot create Private Key");
}
?>