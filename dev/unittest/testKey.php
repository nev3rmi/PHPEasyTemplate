<?php include_once realpath($_SERVER["DOCUMENT_ROOT"])."/setting/control.php"; ?>
<?php
// Bug >
// Test
//echo ValidPassword(CreatingHash('1234'),'4b52555772396d65534e54545774555169566a54686c576573685659555232616b4e6b553552464d734a3055565a6c655364305979776b4d575a6e5774423364617447623177454d475a555a49523255575a6c515252315135306b567a516d5257466a56534a6d5653466c59565248655242544e78466c626f703256735a6c62686c574f334e47576b313255735a455369466a565535454d776c315574356b4d6a566b524f35454d774a4856494e57654e74574f31566c564352565452315450');
//echo ValidPassword(CreatingHash('1234'),CreatingSalt(CreatingHash('1234')));
// Perfect
$privateKey = "2777854766f69707f416f294140567c4c4146396f646445683a545d4830724a767f2677324d68553834657240507266356d4b2539616268563c42723378433c4d4072776c4837323";
$salt = "4b52555772396d65534e54545774555169566a54686c576573685659555232616b4e6b553552464d734a3055565a6c655364305979776b4d575a6e5774423364617447623177454d475a555a49523255575a6c515252315135306b567a516d5257466a56534a6d5653466c59565248655242544e78466c626f703256735a6c62686c574f334e47576b313255735a455369466a565535454d776c315574356b4d6a566b524f35454d774a4856494e57654e74574f31566c564352565452315450";

echo encryptData("Test",$privateKey,$salt);
echo "<br>";
echo decryptData("6avRtDLTCEnFWUt6av6rfQWzBVe9tkgcW90vLdhpsZcPbzcDVpCEIbk56QsJd8v9ZTAm/V+xq7G3Lcr5wAM9Dd",$privateKey,$salt);

?>