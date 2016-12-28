<?php
class custom_zamzar {

	var $online = true;
	var $urlBase = 'https://sandbox.zamzar.com/v1';
	static $localeSection = 'Custom Actions: Zamzar';

	function init() {
		$this->settings = array(
			array(
				'key' => 'APIKey',
				'title' => self::t('API key'),
				'comment' => \FileRun\Lang::t('Get it from %1', 'Admin', array('<a href="https://developers.zamzar.com" target="_blank">https://developers.zamzar.com</a>'))
			)
		);
		$this->JSconfig = array(
            'nonTouch' => true,
			"title" => self::t("Zamzar"),
			'icon' => 'images/icons/zamzar.png',
			"popup" => true, 'width' => 580, 'height' => 400,
			"requiredUserPerms" => array("download", "upload")
		);
	}

	function isDisabled() {
		return (strlen(self::getSetting('APIKey')) == 0);
	}

	static function getSetting($k) {
		global $settings;
		$key = 'plugins_zamzar_'.$k;
		return $settings->{$key};
	}

	static function t($text, $vars = false) {
		return \FileRun\Lang::t($text, self::$localeSection, $vars);
	}

	function run() {
		global $fm;
		\FileRun::checkPerms("download");
		$ext = $fm->getExtension($this->data['fileName']);
		$url = $this->urlBase.'/formats/'.S::forURL($ext);
		$http = new \GuzzleHttp\Client();
		try {
			$response = $http->request('GET', $url, [
				'auth' => [self::getSetting('APIKey'), '']
			]);
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			jsonFeedback(false, 'Network connection error: '.$e->getMessage());
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			if ($e->getResponse()->getStatusCode() == 404) {
				echo "Zamzar doesn't seem to provide any conversion option for the ".mb_strtoupper($ext)." file type.";
				exit();
			} else {
				echo 'Error: '.$e->getResponse()->getStatusCode();
			}
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			echo 'Server error: '.$e->getResponse()->getStatusCode();
			exit();
		} catch (RuntimeException $e) {
			echo 'Error: '.$e->getMessage();
			exit();
		}
		$rs = $response->getBody()->getContents();
		if (!$rs) {
			jsonFeedback(false, 'Error checking status: empty server response!');
		}
		$rs = json_decode($rs, true);
		require($this->path."/display.php");
	}
	function requestConversion() {
		$url = $this->urlBase.'/jobs';
		$http = new \GuzzleHttp\Client();
		try {
			$response = $http->request('POST', $url, [
				'auth' => [self::getSetting('APIKey'), ''],
				'multipart' => [
					[
						'name'     => 'target_format',
						'contents' => S::fromHTML($_POST['format'])
					],
					[
						'name'     => 'source_file',
						'contents' => fopen($this->data['filePath'], 'r')
					]
				]
			]);
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			jsonFeedback(false, 'Network connection error: '.$e->getMessage());
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			$rs = json_decode($e->getResponse()->getBody()->getContents(), true);
			jsonFeedback(false, 'Server error: '. $rs['errors'][0]['message']);
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			jsonFeedback(false, 'Server error: '.$e->getResponse()->getStatusCode());
		} catch (RuntimeException $e) {
			jsonFeedback(false, 'Error: '.$e->getMessage());
		}
		$rs = $response->getBody()->getContents();
		if (!$rs) {
			jsonFeedback(false, 'Error requesting conversion: empty server response!');
		}
		$rs = json_decode($rs, true);
		jsonOutput(array(
			'success' => true,
			'msg' => 'Zamzar: '. $rs['status'],
			'jobId' => $rs['id']
		));

	}
	function getStatus() {
		global $fm;
		$url = $this->urlBase.'/jobs/'.S::forURL(S::fromHTML($_POST['jobId']));

		$http = new \GuzzleHttp\Client();
		try {
			$response = $http->request('GET', $url, [
				'auth' => [self::getSetting('APIKey'), '']
			]);
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			jsonFeedback(false, 'Network connection error: '.$e->getMessage());
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			$rs = json_decode($e->getResponse()->getBody()->getContents(), true);
			jsonOutput(array(
				'success' => false,
				'msg' => 'Zamzar status: '.$rs['status'],
				'status' => $rs['status']
			));
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			echo 'Server error: '.$e->getResponse()->getStatusCode();
			exit();
		} catch (RuntimeException $e) {
			echo 'Error: '.$e->getMessage();
			exit();
		}
		$rs = $response->getBody()->getContents();
		if (!$rs) {
			jsonFeedback(false, 'Error checking status: empty server response!');
		}
		$rs = json_decode($rs, true);

		$rs['output']['size'] = $fm->formatFileSize($rs['output']['size']);
		jsonOutput(array(
			'success' => true,
			'msg' => 'Zamzar: '.$rs['status'],
			'status' => $rs['status'],
			'fileId' => $rs['target_files'][0]['id']
		));
	}
	function downloadConverted() {
		global $myfiles, $fm;
		$tempFilePath = $this->data['filePath'].'.zamzar.tmp';
		$toFile = fopen($tempFilePath, "wb") or die("Failed to create temporary file");
		$http = new \GuzzleHttp\Client();
		$local = \GuzzleHttp\Psr7\stream_for($toFile);
		$newName = $fm->replaceExtension($this->data['fileName'], S::fromHTML($_POST['format']));
		$url = $this->urlBase.'/files/'.S::forURL(S::fromHTML($_POST['fileId'])).'/content';
		try {
			$response = $http->request('GET', $url, [
				'auth' => [self::getSetting('APIKey'), ''],
				'save_to' => $local
			]);
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			jsonFeedback(false, 'Network connection error: '.$e->getMessage());
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			$rs = json_decode($e->getResponse()->getBody()->getContents(), true);
			jsonOutput(array(
				'success' => false,
				'msg' => 'Zamzar status: '.$rs['status'],
				'status' => $rs['status']
			));
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			jsonFeedback(false, 'Server error: '.$e->getResponse()->getStatusCode());
		} catch (RuntimeException $e) {
			jsonFeedback(false, 'Error: '.$e->getMessage());
		}
		if ($response->getStatusCode() == 200) {
			fclose($toFile);
			$rs = $myfiles->newFile($fm->dirname($this->data['relativePath']), $newName, $tempFilePath, false, true);
			if (!$rs) {
				jsonFeedback(false, 'Failed to write file data.');
			}
			jsonOutput(array(
				'success' => true,
				'newFileName' => $newName
			));
		}
	}
}