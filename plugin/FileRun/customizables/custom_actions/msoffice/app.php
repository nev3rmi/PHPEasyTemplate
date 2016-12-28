<?php
class custom_msoffice {

	var $online = true;

	var $ext = array(
		'word' => ["doc", "docx", "docm", "dotm", "dotx", "odt"],
		'excel' => ["xls", "xlsx", "xlsb", "xls", "xlsm", "ods"],
		'powerpoint' => ["ppt", "pptx", "ppsx", "pps", "pptm", "potm", "ppam", "potx", "ppsm", "odp"],
		'project' => ['mpp'],
		'visio' => ['vsd', 'vss', 'vst', 'vdx', 'vsx', 'vtx']
	);

	function init() {
		global $config;
		$postURL = $config['url']['root'].'/?module=custom_actions&action=msoffice&method=run';
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Office", 'Custom Actions: Office'),
			"icon" => 'images/icons/office.png',
			"extensions" => call_user_func_array('array_merge', $this->ext),
			"requiredUserPerms" => array("download"),
			"fn" => "FR.UI.backgroundPost(false, '".\S::safeJS($postURL)."')"
		);
	}
	function run() {
		$data = $this->weblinks->createForService($this->data['filePath'], 1);
		$args = array(
			"id_rnd" => $data['id_rnd'],
			"password" => $data['password']
		);
		//Allowed URIs must conform to the standards proposed in RFC 3987 – Internationalized Resource Identifiers (IRIs)
		//Characters identified as reserved in RFC 3986 should not be percent encoded.
		//Filenames must not contain any of the following characters: \ / : ? < > | " or *.
		$args['filename'] = str_replace('"', '_', $this->data['fileName']);

		$extension = \FM::getExtension($this->data['fileName']);
		$type = false;
		foreach($this->ext as $k => $extList) {
			if (in_array($extension, $extList)) {
				$type = $k;
				break;
			}
		}
		if (!$type) {return false;}
		$url = $this->weblinks->getURLRW($args);
		if (!$url) {
			echo "Failed to setup weblink";
			exit();
		}
		header('Location: ms-'.$type.':ofv|u|'.$url);
		exit();
	}
}