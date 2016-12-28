<?php
class custom_contact_sheet {
	function init() {
		$this->config = array(
			'localeSection' => 'Custom Actions: Photo Proof Sheet'
		);
		$this->JSconfig = array(
			'title' => $this->getString('Create photo proof sheet'),
			'iconCls' => 'fa fa-fw fa-th',
			'requiredUserPerms' => array('download'),
			"ajax" => true,
			'width' => 400,
			'height' => 400,
			'multiple' => true,
			'onlyMultiple' => true
		);
	}
	function isDisabled() {
		global $settings;
		return !$settings->thumbnails_imagemagick;
	}
	function getString($s, $v = false) {

		return S::safeJS(\FileRun\Lang::t($s, $this->config['localeSection'], $v));
	}
	function run() {
		global $settings, $fm, $myfiles, $config;
		if (stripos($this->data['relativePath'], '/ROOT/HOME') === false) {
			$this->data['relativePath'] = '/ROOT/HOME';
		}
		if (sizeof($_POST['paths']) < 2) {
			exit('You need to select at least two files');
		}
		$imagemagick_convert = $settings->thumbnails_imagemagick_path;
		$filename = $fm->basename($imagemagick_convert);
		$graphicsMagick = false;
		if (in_array($filename, array('gm', 'gm.exe'))) {
			$graphicsMagick = true;
			$imagemagick_convert = $imagemagick_convert.' convert';
		}

		$cmd = str_replace('convert', 'montage', $imagemagick_convert);
		$cmd .= " -label \"%f\" -font Arial -pointsize 20 -background \"#ffffff\" -fill \"black\" -strip -define jpeg:size=600x500 -geometry 600x500+2+2";

		if (!$graphicsMagick) {
			if ($config['imagemagick_limit_resources']) {
				$cmd .= " -limit area 20mb";
				$cmd .= " -limit disk 500mb";
			}
			if (!$config['imagemagick']['no_auto_orient']) {
				$cmd .= " -auto-orient";
			}
		}

		if (sizeof($_POST['paths']) > 8) {
			$cmd .= ' -tile 2x4';
		} else {
			$cmd .= ' -tile 2x';
		}

		foreach ($_POST['paths'] as $relativePath) {
			$relativePath = S::fromHTML($relativePath);
			if (!\FileRun\Files\Utils::isCleanPath($relativePath)) {
				echo 'Invalid path!';
				exit();
			}
			if (\FileRun\Files\Utils::isSharedPath($relativePath)) {
				$pathInfo = \FileRun\Files\Utils::parsePath($relativePath);
				$shareInfo = \FileRun\Share::getInfoById($pathInfo['share_id']);
				if (!$shareInfo['perms_download']) {
					jsonOutput(array('success' => false, 'msg' => $this->getString('You are not allowed to access the requested file!')));
				}
			}
			$filePath = $myfiles->getUserAbsolutePath($relativePath);
			if (!file_exists($filePath)) {
				jsonOutput(array('success' => false, 'msg' => $this->getString('The file you are trying to process is no longer available!')));
			}
			$filename = $fm->basename($relativePath);
			$ext =  $fm->getExtension($filename);
			if ($this->isSupportedImageFile($ext)) {
				$cmd .= ' "'.$filePath;
				if (in_array($ext, array('tiff', 'tif', 'pdf', 'gif', 'ai', 'eps'))) {
					$cmd .= '[0]';
				}
				$cmd .= '"';
			}
		}
		$outputFilename = 'Contact_sheet_'.time().'.jpg';
		$outputPath = $myfiles->getUserAbsolutePath(gluePath($this->data['relativePath'], $outputFilename));
		$cmd .= ' "'.$outputPath.'"';

		if ($fm->os == "win") {
			$cmd .= "  && exit";
		} else {
			$cmd .= " 2>&1";
		}
		$return_text = array();
		$return_code = 0;
		session_write_close();
		exec($cmd, $return_text, $return_code);
		if ($return_code != 0) {
			jsonOutput(array('success' => false, 'msg' => $this->getString('Action failed: %1 %2', array($return_code, implode(',', $return_text)))));
		} else {
			\FileRun\Paths::addIfNotFound($outputPath);
			jsonOutput(array('success' => true, 'refresh' => 'true', 'highlight' => $outputFilename, 'msg' => $this->getString('Photo proof sheet successfuly created')));
		}
	}

	function isSupportedImageFile($ext) {
		global $settings, $fm;
		$ext = strtolower($ext);
		$typeInfo = $fm->fileTypeInfo(false, $ext);
		if ($typeInfo['type'] == "img") {
			return "gd";
		}
		if ($settings->thumbnails_imagemagick && in_array($ext, explode(",", strtolower($settings->thumbnails_imagemagick_ext)))) {
			return "imagemagick";
		}
		return false;
	}
}