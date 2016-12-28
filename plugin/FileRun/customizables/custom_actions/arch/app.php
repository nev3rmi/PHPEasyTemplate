<?php
class custom_arch {
	function init() {
		$this->localeSection = "Custom Actions: Archive Explorer";
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Archive Explorer", $this->localeSection),
			'iconCls' => 'fa fa-fw fa-file-archive-o',
			'useWith' => array('arch'),
			"popup" => true,
			'width' => 500, 'height' => 400,
			"requiredUserPerms" => array("download")
		);
	}
	function run() {
		$arch = ArchUtil::init($this->data['filePath']);
		if (!$arch) {
			exit("This type of archives is not supported by the current server configuration.");
		}
		$arch->open();
		$list = $arch->getTOC(100);
		if (!is_array($list)) {
			exit("Failed to read archive contents!");
		}
		$arch->close();
		$count = $arch->itemsCount;

		require($this->path."/display.php");
		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "Archive Explorer"
		));
	}
}