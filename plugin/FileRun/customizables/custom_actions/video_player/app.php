<?php
class custom_video_player {
	function init() {
		$this->localeSection = "Custom Actions: Video Player";
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Video Player", $this->localeSection),
			'iconCls' => 'fa fa-fw fa-play-circle-o',
			'useWith' => array('wvideo'),
			"popup" => true,
			"requiredUserPerms" => array("download")
		);
	}
	function run() {
		global $config;
		\FileRun::checkPerms("download");
		$ext = \FM::getExtension($this->data['fileName']);
		$handlers = array(
			'flv' => 'flv',
			'm4v' => 'flv',
			'mpg' => 'mpg',
			'swf' => 'swf',
			'wmv' => 'wmv',
			'mov' => 'html5',
			'ogv' => 'html5',
			'mp4' => 'html5',
			'mkv' => 'html5',
			'webm' => 'html5'
		);
		$handle = $handlers[$ext];
		if (!$handle) {
			exit('The file type is not supported by this player.');
		}
		require(gluePath($this->path, $handle, "/display.php"));
	}

	function stream() {
		\FileRun::checkPerms("download");
		\FileRun\Utils\Downloads::sendFileToBrowser($this->data['filePath']);
		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "Video Player"
		));
		exit();
	}
}