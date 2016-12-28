<?php
class custom_image_viewer {
	function init() {
		$this->localeSection = "Image Viewer";
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Image Viewer", $this->localeSection),
			'iconCls' => 'fa fa-fw fa-picture-o',
			'useWith' => array('nothing'),
			"popup" => true,
			"requiredUserPerms" => array("download")
		);
	}
	function run() {
		global $config;
		\FileRun::checkPerms("download");
		require(gluePath($this->path, "/display.php"));
	}
}