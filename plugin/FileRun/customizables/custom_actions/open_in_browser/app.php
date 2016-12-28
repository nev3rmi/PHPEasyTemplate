<?php
class custom_open_in_browser {
	function init() {
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t('Open in browser', 'Custom Actions'),
			'iconCls' => 'fa fa-fw fa-eye',
			'useWith' => array('nothing'),
			"requiredUserPerms" => array("download")
		);
	}
	function run() {
		\FileRun::checkPerms("download");
		$ext = \FM::getExtension($this->data['fileName']);
		if ($ext == 'pdf') {
			$browserIsiOS = preg_match("/iPhone|Android|iPad|iPod|webOS/", $_SERVER['HTTP_USER_AGENT']);
			$browserIsChrome = (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'chrome') !== false);
			$browserIsFirefox = (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'firefox') !== false);
			$browserIsIE8OrLower = preg_match('/(?i)msie [2-8]/',$_SERVER['HTTP_USER_AGENT']);

			if (!$browserIsChrome && !$browserIsFirefox && !$browserIsIE8OrLower && !$browserIsiOS) {
				$url = '?module=custom_actions&action=pdfjs';
				$url .= "&path=".S::forURL(S::forHTML($this->data['relativePath']));
				siteRedirect($url);
			}
		}
		\FileRun\Utils\Downloads::sendFileToBrowser($this->data['filePath']);
		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "Open in browser"
		));
	}
}