<?php

class custom_kml_viewer {

	var $online = true;

	function init() {
		$this->URL = 'https://earth.google.com/kmlpreview/';
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t('Google Earth', 'Custom Actions: Google Earth'),
			'icon' => 'images/icons/gearth.png',
			"extensions" => array("kml", "kmz"),
			"popup" => true,
			"requiredUserPerms" => array("download")
		);
	}

	function run() {
	$data = $this->weblinks->createForService($this->data['filePath'], 2, $this->data['shareInfo']['id']);
	$url = $this->weblinks->getURL(array(
			"id_rnd" => $data['id_rnd'],
			"filename" => $this->data['fileName']
		)
	);
	if (!$url) {
		echo "Failed to setup weblink";
		exit();
	}

	\FileRun\Log::add(false, "preview", array(
		"relative_path" => $this->data['relativePath'],
		"full_path" => $this->data['filePath'],
		"method" => "google-earth"
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
<iframe scrolling="no" width="100%" height="100%" border="0" src="<?php echo $this->URL?>#url=<?php echo urlencode($url)?>">
</iframe> 
</body>
</html>
<?php
	}
}