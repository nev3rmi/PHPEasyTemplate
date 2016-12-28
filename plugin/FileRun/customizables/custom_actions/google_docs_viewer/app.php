<?php

class custom_google_docs_viewer {

	var $online = true;

	function init() {
		$this->URL = "https://docs.google.com/viewer";
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Google Docs Viewer", 'Custom Actions: Google Docs Viewer'),
			'icon' => 'images/icons/gdocs.png',
			"extensions" => array(
				"pdf", "ppt", "pptx", "doc", "docx", "xls", "xlsx", "dxf", "ps", "eps", "xps",
				"psd", "tif", "tiff", "bmp", "svg",
				"pages", "ai", "dxf", "ttf"
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
		"method" => "Google Docs Viewer"
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
<iframe scrolling="no" width="100%" height="100%" border="0" src="<?php echo $this->URL?>?url=<?php echo urlencode($url)?>&embedded=true">
</iframe> 
</body>
</html>
<?php
	}
}