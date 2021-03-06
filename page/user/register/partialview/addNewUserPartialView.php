<?php include_once realpath($_SERVER["DOCUMENT_ROOT"])."/setting/control.php"; ?>
<script>
	// Need to be include
	function SetRegisterDialogToLoading(registerDialog){
		registerDialog.setType(BootstrapDialog.TYPE_PRIMARY);
		registerDialog.setMessage('Please wait ...');
		registerDialog.setTitle('PROCESSING');
		return;
	}
	function SetRegisterDialogToError(registerDialog, message){
		registerDialog.setType(BootstrapDialog.TYPE_DANGER);
		registerDialog.setMessage(message);
		registerDialog.setTitle('ERROR');
		return;
	}
	function OpenRegisterDialog(registerDialog){
		registerDialog.open();
		return;
	}
	function SetDialogInGeneral(registerDialog, message, title, type){
		registerDialog.setType('type-'+type);
		registerDialog.setMessage(message);
		registerDialog.setTitle(title);
		return;
	}
</script>
<script>
	// Initial Dialog
	var registerDialog = new BootstrapDialog();
	SetRegisterDialogToLoading(registerDialog);
	OpenRegisterDialog(registerDialog);
</script>
<?php
function SetRegisterDialogToError($message){
	echo "<script>SetRegisterDialogToError(registerDialog, '".$message."');</script>";
}
function SetDialogInGeneral($message, $title, $type){
	echo "<script>SetDialogInGeneral(registerDialog, '".$message."', '".$title."', '".$type."')</script>";
}
?>
