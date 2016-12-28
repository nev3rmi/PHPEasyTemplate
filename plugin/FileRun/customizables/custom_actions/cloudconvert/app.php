<?php
use \CloudConvert\Api;

class custom_cloudconvert {

	var $online = true;
	static $localeSection = "Custom Actions: CloudConvert";

	function init() {
		$this->settings = array(
			array(
				'key' => 'APIKey',
				'title' => self::t('API Key'),
				'comment' => \FileRun\Lang::t('Get it from %1', 'Admin', array('<a href="https://cloudconvert.com/api" target="_blank">https://cloudconvert.com/api</a>'))
			)
		);
		$this->JSconfig = array(
            'nonTouch' => true,
			"title" => self::t("CloudConvert"),
			'icon' => 'images/icons/cloudconvert.png',
			"popup" => true, 'width' => 580, 'height' => 400,
			"requiredUserPerms" => array("download", "upload")
		);
	}

	function isDisabled() {
		return (strlen(self::getSetting('APIKey')) == 0);
	}

	static function getSetting($k) {
		global $settings;
		$key = 'plugins_cloudconvert_'.$k;
		return $settings->{$key};
	}

	static function t($text, $vars = false) {
		return \FileRun\Lang::t($text, self::$localeSection, $vars);
	}

	function run() {
		global $fm;
		\FileRun::checkPerms("download");
		$ext = $fm->getExtension($this->data['fileName']);
		$api = new Api(self::getSetting('APIKey'));
		$rs = $api->get('/conversiontypes', ['inputformat' => \S::forURL($ext)]);
		require($this->path."/display.php");
	}

	function requestConversion() {
		global $fm;
		$ext = $fm->getExtension($this->data['fileName']);
		$targetFormat = S::fromHTML($_POST['format']);
		$api = new Api(self::getSetting('APIKey'));
		try {
			$process = $api->convert([
				'inputformat' => $ext,
				'outputformat' => $targetFormat,
				'input' => 'upload',
				'filename' => $this->data['fileName'],
				'file' => fopen($this->data['filePath'], 'r'),
				'callback' => 'http://_INSERT_PUBLIC_URL_TO_/callback.php'
			]);

		} catch (\CloudConvert\Exceptions\ApiBadRequestException $e) {
			jsonFeedback(false, "Error: " . $e->getMessage());
		} catch (\CloudConvert\Exceptions\ApiConversionFailedException $e) {
			jsonFeedback(false, "Conversion failed, maybe because of a broken input file: " . $e->getMessage());
		}  catch (\CloudConvert\Exceptions\ApiTemporaryUnavailableException $e) {
			jsonFeedback(false, "API temporary unavailable: ".$e->getMessage());
		} catch (Exception $e) {
			jsonFeedback(false, "Error: " . $e->getMessage());
		}
		jsonOutput(array(
			'success' => true,
			'msg' => 'CloudConvert: '. $process->message,
			'url' => $process->url
		));

	}

	function getStatus() {
		global $fm;
		$url = S::fromHTML($_POST['statusURL']);
		if (strtolower(substr($url, 0, 6)) != 'https:') {
			$url = 'https:'.$url;
		}
		$api = new Api(self::getSetting('APIKey'));
		$process = new \CloudConvert\Process($api, $url);
		$process->refresh();

		if ($process->step == 'finished') {
			$tempFile = gluePath($this->data['filePath'].'.'.$process->output->size.'.upload');
			$rs = $process->download($tempFile);

			global $myfiles;
			$fileName = \FM::replaceExtension($this->data['fileName'], $process->output->ext);
			$rs = $myfiles->newFile($fm->dirname($this->data['relativePath']), $fileName, $tempFile, false, true);
			if (!$rs) {
				jsonOutput(array(
					'success' => false,
					'msg' => 'Failed to save the downloaded file',
					'step' => 'error'
				));
			}
			jsonOutput(array(
				'success' => true,
				'msg' => 'Converted file was saved',
				'step' => 'downloaded',
				'newFileName' => $fileName
			));
		}

		jsonOutput(array(
			'success' => false,
			'msg' => 'CloudConvert: '.$process->message,
			'step' => $process->step,
			'percent' => $process->percent,
			'output' => $process->output
		));
	}
}