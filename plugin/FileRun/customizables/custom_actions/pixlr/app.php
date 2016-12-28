<?php

class custom_pixlr {

	var $online = true;
	var $localeSection = "Custom Actions: Pixlr";

	function init() {
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Pixlr", $this->localeSection),
			'icon' => 'images/icons/pixlr.png',
			"extensions" => array("jpg", "jpeg", "gif", "png", "psd", "bmp", "pxd"),
			"popup" => true, "external" => true,
			"requiredUserPerms" => array("download", "upload"),
			"createNew" => array(
				"title" => \FileRun\Lang::t("Image with Pixlr", $this->localeSection),
				"defaultFileName" => \FileRun\Lang::t("Untitled.png", $this->localeSection)
			)
		);
		$this->outputName = "image";
	}
	function run() {
		global $config;
		$weblinkInfo = $this->weblinks->createForService($this->data['filePath'], false, $this->data['shareInfo']['id']);
		if (!$weblinkInfo) {
			echo "Failed to setup weblink";
			exit();
		}
		$this->data['fileURL'] = $this->weblinks->getURL(array("id_rnd" => $weblinkInfo['id_rnd']));
		$this->data['saveURL'] = $this->weblinks->getSaveURL($weblinkInfo['id_rnd'], false, "pixlr");

		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "Pixlr"
		));

		$proto = isSSL() ? "https" : "http";
		$url = $proto."://apps.pixlr.com/editor/";
		//$url .= "?method=POST";
		$url .= "?image=".urlencode($this->data['fileURL']);
		$url .= "&referrer=".urlencode($config['settings']['app_title']);
		$url .= "&target=".urlencode($this->data['saveURL']);
		$url .= "&title=".urlencode($this->data['fileName']);
		$url .= "&redirect=false";
		$url .= "&locktitle=true";
		$url .= "&locktype=true";
		header('Location: '.$url);
	}
	function createBlankFile() {
		global $myfiles, $fm;
		if (!\FileRun\Perms::check('upload')) {exit();}
		if (file_exists($this->data['filePath'])) {
			jsonOutput(array("rs" => false, "msg" => \FileRun\Lang::t('A file with the specified name already exists. Please try again.', $this->localeSection)));
		}
		$blankFilePath = gluePath($this->path, "blank.png");
		$rs = $myfiles->newFile($fm->dirname($this->data['relativePath']), $this->data['fileName'], $blankFilePath);
		if ($rs) {
			jsonOutput(array("rs" => true, 'path' => $this->data['relativePath'], "filename" => $this->data['fileName'], "msg" => \FileRun\Lang::t("Blank image created successfully", $this->localeSection)));
		} else {
			jsonOutput(array("rs" => false, "msg" => $myfiles->error['msg']));
		}
	}
	function getNewFileContents() {
		return file_get_contents(S::fromHTML($_GET['image']));
	}
}