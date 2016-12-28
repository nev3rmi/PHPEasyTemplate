<?php
class custom_office_web_viewer {

	var $online = true;

	function init() {
		$proto = isSSL() ? "https" : "http";
		$this->URL = $proto."://view.officeapps.live.com/op/view.aspx";
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Office Web Viewer", 'Custom Actions: Office Web Viewer'),
			"icon" => 'images/icons/office.png',
			"extensions" => array(
				"doc", "docx", "docm", "dotm", "dotx",
				"xls", "xlsx", "xlsb", "xls", "xlsm",
				"ppt", "pptx", "ppsx", "pps", "pptm", "potm", "ppam", "potx", "ppsm"
			),
			"popup" => true,
			"requiredUserPerms" => array("download")
		);
	}
	function run() {
	$url = $this->weblinks->getOneTimeDownloadLink($this->data['filePath']);
	if (!$url) {
		echo "Failed to setup weblink";
		exit();
	}
	\FileRun\Log::add(false, "preview", array(
		"relative_path" => $this->data['relativePath'],
		"full_path" => $this->data['filePath'],
		"method" => "Office Web Viewer"
	));
?>
<html>
<head>
	<title></title>
	<style>
		body {
			border: 0px;
			margin: 0px;
			padding: 0px;
			overflow:hidden;
		}
	</style>
</head>

<body>
<iframe scrolling="no" width="100%" height="100%" border="0" src="<?php echo $this->URL?>?src=<?php echo urlencode($url)?>">
</iframe> 
</body>
</html>
<?php
	}
}