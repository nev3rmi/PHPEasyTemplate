<?php include_once realpath($_SERVER["DOCUMENT_ROOT"])."/setting/control.php"; ?>
<script>
var registerDialog = new BootstrapDialog();
registerDialog.setType(BootstrapDialog.TYPE_PRIMARY);
registerDialog.setMessage('Please wait ...');
registerDialog.setTitle('Processing');
registerDialog.open();
</script>

<?php
$email = $_POST['registerEmail'];
$password = $_POST['registerPassword'];
$retypePassword = $_POST['registerRetypePassword'];
$termAndCondition = $_POST['registerRuleAccepted'];
// Regex again

// Create Private key

// On success
/*sleep(3);
*/
echo "
<script>
	registerDialog.setType(BootstrapDialog.TYPE_DANGER);
	registerDialog.setTitle('ERROR');
	registerDialog.setMessage('Cannot create private key');
</script>";
exit();
?>