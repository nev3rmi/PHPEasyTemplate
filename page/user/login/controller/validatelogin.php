<?php include_once realpath($_SERVER["DOCUMENT_ROOT"])."/setting/control.php"; ?>
<?php
// Not work yet
parse_str($_POST["formValue"], $data);
consoleData($data);
?>