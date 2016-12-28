<?php
if (isset($_GET['oauth2'])) {
	$files = array(
		'roboto/load.css',
		'normalize.css',
		'skeleton.css',
		'oauth2.css'
	);
} else if (isset($_GET['file_request'])) {
	$files = array(
		'roboto/load.css',
		'file_request.css'
	);
} else {
	$files = array(
		'roboto/load.css',
		//'_ext-all.css',
		'ext-all.min.css',
		'ext-filerun.css',
		'../js/ext/ux/ProgressColumn/ProgressColumn.css',
		'font-awesome/css/font-awesome.min.css'
	);
}

if (extension_loaded("zlib") && (ini_get("output_handler") != "ob_gzhandler")) {
	ini_set("zlib.output_compression", 1);
}

header("Content-type: text/css; charset: UTF-8");
header("Cache-control: public");
header("Pragma: cache");
header("Expires: " . gmdate ("D, d M Y H:i:s", time() + 31356000) . " GMT");

foreach ($files as $key => $file) {
	readfile($file);
	echo "\r\n";
}