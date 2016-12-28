<?php
/*
	Edit image files with Adobe Creative Cloud
*/
class custom_creative_cloud {
	var $online = true;
	function init() {
		$this->localeSection = "Custom Actions: Creative Cloud";
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Creative Cloud", $this->localeSection),
			"iconCls" => 'fa fa-fw fa-cloud', 'icon' => 'images/icons/creative_cloud.png',
			"extensions" => array("jpg", "jpeg", "png"),
			"popup" => true,
			"requiredUserPerms" => array("download")
		);
		$this->outputName = "imageOutput";
	}
	function run() {
		$weblinkInfo = $this->weblinks->createForService($this->data['filePath'], false, $this->data['shareInfo']['id']);
		if (!$weblinkInfo) {
			echo "Failed to setup weblink";
			exit();
		}
		$this->data['fileURL'] = $this->weblinks->getURL(array("id_rnd" => $weblinkInfo['id_rnd']));
		$this->data['saveURL'] = $this->weblinks->getSaveURL($weblinkInfo['id_rnd'], false, "creative_cloud");
		require($this->path."/display.php");
		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "Creative Cloud"
		));
	}
	
	function getNewFileContents() {
		$fromURL = S::fromHTML($_REQUEST['fromURL']);
		if (!$fromURL) {
			echo 'No URL specified';
			exit();
		}
		return file_get_contents($fromURL);
	}
}