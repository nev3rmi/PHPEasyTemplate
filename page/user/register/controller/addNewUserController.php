<?php include_once realpath($_SERVER["DOCUMENT_ROOT"])."/setting/control.php"; ?>
<?php include_once $_phpPath."page/user/register/partialview/addNewUserPartialView.php"; ?>
<?php
$email = $_POST['registerEmail'];
$password = $_POST['registerPassword'];
$retypePassword = $_POST['registerRetypePassword'];
$termAndCondition = $_POST['registerRuleAccepted'];

// Regex Value
if (!RegexCheck($_regexMail,$email)){
	SetRegisterDialogToError("Invalid type of email");
	exit();
};
if (!RegexCheck($_regexPassword,$password)){
	SetRegisterDialogToError("Invalid type of password");
	exit();
};
if (!RegexCheck($_regexPassword,$retypePassword)){
	SetRegisterDialogToError("Invalid type of retype password");
	exit();
};
if ($termAndCondition != "on"){
	SetRegisterDialogToError("You must agree term before sign up");
	exit();
};
// 

// Create Private Key
if (false == false){
	SetRegisterDialogToError("Cannot create Private Key");
}

// Need to run independently
?>