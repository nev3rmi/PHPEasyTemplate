<?php
class custom_webodf {
	function init() {
		$this->localeSection = "Custom Actions: OpenDocument Viewer";
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("OpenDocument Viewer", $this->localeSection),
			'iconCls' => 'fa fa-fw fa-file-text-o',
			"extensions" => array("odt", "ods", "odp"),
			"popup" => true,
			"requiredUserPerms" => array("download")
		);
	}
	function run() {
		require($this->path."/display.php");
	}
	function download() {
		\FileRun::checkPerms("download");
		\FileRun\Utils\Downloads::sendFileToBrowser($this->data['filePath']);
		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "OpenDocument Viewer"
		));
	}
}