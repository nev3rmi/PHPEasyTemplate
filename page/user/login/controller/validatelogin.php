<?php include_once realpath($_SERVER["DOCUMENT_ROOT"])."/setting/control.php"; ?>
<?php
$email = htmlentities($_POST['userEmail'], ENT_QUOTES, "UTF-8");
$password =  htmlentities($_POST['userPassword'], ENT_QUOTES, "UTF-8");

if (!RegexCheck($_regexMail,$email)){
	echo '
	<div class="alert alert-dismissible alert-danger">
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	  <strong>Error:</strong> <a href="#" class="alert-link">Please type correct email.
	</div>
	';
	exit();
}
if (!RegexCheck($_regexPassword,$email)){
	echo '
	<div class="alert alert-dismissible alert-danger">
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	  <strong>Error:</strong> <a href="#" class="alert-link">Please type correct password.
	</div>
	';
	exit();
}

echo '
	<div class="alert alert-dismissible alert-success">
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	  <strong>Success:</strong> You have successful logged!
	</div>';
sleep(3);
echo '<script>loadLoginNavigationBar();</script>';
exit();
?>