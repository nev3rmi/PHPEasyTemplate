<?php include_once realpath($_SERVER["DOCUMENT_ROOT"])."/setting/control.php"; ?>
<script>
	// Need to be include
	function SetRegisterDialogToLoading(registerDialog){
		registerDialog.setType(BootstrapDialog.TYPE_PRIMARY);
		registerDialog.setMessage('Please wait ...');
		registerDialog.setTitle('Processing');
		return;
	}
	function OpenRegisterDialog(registerDialog){
		registerDialog.open();
		return;
	}
</script>
<script>
	// Call
	// Loading Processing Modal
	var registerDialog = new BootstrapDialog();
	SetRegisterDialogToLoading(registerDialog);
	OpenRegisterDialog(registerDialog);
</script>
