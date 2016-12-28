<?php
class custom_pdfjs {
	function init() {
		$this->localeSection = "Custom Actions: PDF Viewer";
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("PDF Viewer", $this->localeSection),
			"iconCls" => 'fa fa-fw fa-file-pdf-o',
			"extensions" => array("pdf"),
			"popup" => true,
			"requiredUserPerms" => array("download")
		);
	}
	function run() {
		require($this->path."/display.php");
	}
	function download() {
		\FileRun::checkPerms("download");
		$rs = \FileRun\Utils\Downloads::sendFileToBrowser($this->data['filePath']);
		if ($rs && ($rs == "unknown" || $rs == "final")) {
			\FileRun\Log::add(false, "preview", array(
				"relative_path" => $this->data['relativePath'],
				"full_path" => $this->data['filePath'],
				"method" => "PDF Viewer"
			));
		}
	}
}