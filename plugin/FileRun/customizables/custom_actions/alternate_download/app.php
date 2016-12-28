<?php

class custom_alternate_download {

	static $localeSection = "Custom Actions: Alternate Download";

	function init() {
		$this->settings = array(
			array(
				'key' => 'config',
				'title' => self::t('Configuration JSON'),
				'large' => true,
				'comment' => self::t('See <a href="%1" target="_blank">this page</a> for more information.', array('http://docs.filerun.com/alternate_downloads'))
			)
		);
		global $config;
		$postURL = $config['url']['root'].'/?module=custom_actions&action=alternate_download&method=run';
		$this->JSconfig = array(
			"title" => self::t("Alternate Download"),
			'iconCls' => 'fa fa-fw fa-download',
			'useWith' => array(
				'img', 'wvideo', 'mp3'
			),
			"fn" => "FR.UI.backgroundPost(false, '".\S::safeJS($postURL)."')",
			"requiredUserPerms" => array("download")
		);
	}

	function isDisabled() {
		return (strlen(self::getSetting('config')) == 0);
	}

	static function getSetting($k) {
		global $settings;
		$key = 'plugins_alternate_download_'.$k;
		return $settings->{$key};
	}

	function run() {
		global $fm;
		\FileRun::checkPerms("download");
		$cfg = self::getSetting('config');
		$this->config = json_decode($cfg, true);
		if (!$this->config) {
			$this->reportError('Failed to decode JSON config!');
		}
		if (!is_array($this->config['paths'])) {
			$this->reportError('The plugin is configured with an invalid JSON set!');
		}
		$parentPath = $fm->dirname($this->data['filePath']);
		$newParentPath = false;
		$newExt = false;
		foreach($this->config['paths'] as $path) {
			if ($fm->inPath($parentPath, $path['normal'])) {
				$newParentPath = $path['alternate'];
				$newExt = $path['extension'];
				$subPath = substr($parentPath, strlen($path['normal']));
				continue;
			}
		}
		if (!$newParentPath) {
			$this->reportNotFound();
		}
		if ($newExt) {
			$fileName = $fm->replaceExtension($this->data['fileName'], $newExt);
		} else {
			$fileName = $this->data['fileName'];
		}
		$newPath = gluePath($newParentPath, $subPath, $fileName);
		if (!is_file($newPath)) {
			$this->reportNotFound();
		}
		$fileSize = $fm->getFileSize($newPath);
		\FileRun\Utils\Downloads::sendHTTPFile($newPath);
		$bytesSentToBrowser = \FileRun\Utils\Downloads::$bytesSentToBrowser;
		$logData = array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"actual_path" => $newPath,
			"file_size" => $fileSize,
			"interface" => "alternate_download",
			'original_path' => $this->data['filePath'],
			"bytes_sent" => $bytesSentToBrowser
		);
		\FileRun\Log::add(false, "download", $logData);
	}

	static function t($text, $vars = false) {
		return \FileRun\Lang::t($text, self::$localeSection, $vars);
	}

	function reportNotFound() {
		return $this->reportError('No alternate download found for the selected file.');
	}

	function reportError($msg) {
		echo '<script>window.parent.FR.UI.feedback(\''.self::t($msg).'\');</script>';
		exit();
	}

}