<?php
class custom_audio_player {
	function init() {
		$this->localeSection = "Custom Actions: Audio Player";
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Audio Player", $this->localeSection),
			'iconCls' => 'fa fa-fw fa-music',
			'useWith' => array('mp3'),
			"popup" => true,
			'width' => 400, 'height' => 400,
			"requiredUserPerms" => array("download")
		);
	}
	function run() {
		global $config, $settings, $fm;

		$folderRelativePath = \FM::dirname($this->data['relativePath']);
		$folderPath = \FM::dirname($this->data['filePath']);
		$ext = \FM::getExtension($this->data['fileName']);

		$audioFiles = array();
		$currentIndex = 0;

		if ($ext == "m3u" || $ext == "m3u8") {
			$lines = file($this->data['filePath']);
			foreach ($lines as $line) {
				if (substr($line, 0, 5) == "http:") {
					$audioFiles[] = array($line, $line);
				}
			}
		} else if ($ext == "pls") {
			$lines = file($this->data['filePath']);
			foreach ($lines as $line) {
				if (substr($line, 0, 4) == "File") {
					$pos = strpos($line, "=");
					$url = substr($line, $pos+1);
					$audioFiles[] = array($url, $url);
				}
			}
		} else {
			if (isset($config['app']['audioplayer']['playlist']) && !$config['app']['audioplayer']['playlist']) {
				$url = $config['url']['root'];
				$url .= "/?module=custom_actions&action=audio_player&method=stream";
				$url .= "&path=" . S::forURL(gluePath($folderRelativePath, $this->data['fileName']));
				$audioFiles[] = array($url, $this->data['fileName']);
				$currentIndex = 0;
			} else {
				$list = \FileRun\Files\Utils::listFolder($folderPath, false, false, false, true);
				if (is_array($list) && sizeof($list) > 0) {
					$i = 0;
					foreach ($list as $fileName) {
						$info = $fm->fileTypeInfo($fileName);
						if ($info['type'] == 'mp3') {
							$url = $config['url']['root'];
							$url .= "/?module=custom_actions&action=audio_player&method=stream";
							$url .= "&path=" . S::forURL(gluePath($folderRelativePath, $fileName));
							$audioFiles[$i] = array($url, $fileName);
							if ($fileName == $this->data['fileName']) {
								$currentIndex = $i;
							}
							$i++;
						}
					}
				}
			}
		}

		require($this->path."/display.php");
	}

	function stream() {
		\FileRun::checkPerms("download");
		\FileRun\Utils\Downloads::sendFileToBrowser($this->data['filePath']);
		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "Audio Player"
		));
		exit();
	}
}