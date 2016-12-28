<?php

class custom_autodesk {
	var $online = true;

	var $authURL = 'https://developer.api.autodesk.com/authentication/v1/authenticate';
	var $bucketsURL = 'https://developer.api.autodesk.com/oss/v1/buckets';
	var $registerURL = 'https://developer.api.autodesk.com/viewingservice/v1/register';
	var $viewURL = 'https://developer.api.autodesk.com/viewingservice/v1';
	var $authData = array();
	static $localeSection = 'Custom Actions: Autodesk';

	function init() {
		$this->settings = array(
			array(
				'key' => 'clientID',
				'title' => self::t('API client ID')
			),
			array(
				'key' => 'clientSecret',
				'title' => self::t('API client secret'),
				'comment' => \FileRun\Lang::t('Get them from %1', 'Admin', array('<a href="https://developer.autodesk.com" target="_blank">https://developer.autodesk.com</a>'))
			)
		);
		$this->JSconfig = array(
			"title" => self::t("Autodesk"),
			'icon' => 'images/icons/autodesk.png',
			"extensions" => array('3dm', '3ds', 'asm', 'cam360', 'catpart', 'catproduct', 'cgr', 'dae', 'dlv3', 'dwf', 'dwfx', 'dwg', 'dwt', 'dxf', 'exp', 'f3d', 'fbx', 'g', 'gbxml', 'iam', 'idw', 'ifc', 'ige', 'iges', 'igs', 'ipt', 'jt', 'model', 'neu', 'nwc', 'nwd', 'obj', 'prt', 'rvt', 'sab', 'sat', 'session', 'sim', 'sim360', 'skp', 'sldasm', 'sldprt', 'smb', 'smt', 'ste', 'step', 'stl', 'stla', 'stlb', 'stp', 'wire', 'x_b', 'x_t', 'xas', 'xpr'),
			"popup" => true,
			"requiredUserPerms" => array("download"),
			'loadingMsg' => \FileRun\Lang::t('Loading...')
		);
	}

	function isDisabled() {
		return (strlen(self::getSetting('clientID')) == 0) || (strlen(self::getSetting('clientSecret')) == 0);
	}

	static function getSetting($k) {
		global $settings;
		$key = 'plugins_autodesk_'.$k;
		return $settings->{$key};
	}

	static function t($text, $vars = false) {
		return \FileRun\Lang::t($text, self::$localeSection, $vars);
	}

	function run() {
		\FileRun::checkPerms("download");
		require($this->path."/display.php");
	}

	function start() {
		$http = new \GuzzleHttp\Client();
		try {
			$response = $http->request('POST', $this->authURL, [
				'form_params' => [
					'client_id' => self::getSetting('clientID'),
					'client_secret' => self::getSetting('clientSecret'),
					'grant_type' => 'client_credentials',
					'scope' => 'data:read data:write bucket:create'
				]
			]);
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			jsonFeedback(false, 'Network connection error: '.$e->getMessage());
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			$rs = json_decode($e->getResponse()->getBody()->getContents(), true);
			jsonFeedback(false, 'Error while authenticating with Autodesk API: '. $rs['developerMessage']);
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			jsonFeedback(false, 'Server error: '.$e->getResponse()->getStatusCode());
		} catch (RuntimeException $e) {
			jsonFeedback(false, 'Error: '.$e->getMessage());
		}
		$rs = $response->getBody()->getContents();
		if (!$rs) {
			jsonFeedback(false, 'Error: empty server response!');
		}
		$rs = json_decode($rs, 1);
		if (!$rs['access_token']) {
			jsonFeedback(false, 'Error: missing acess token!');
		}
		$this->authData = $rs;

		$bucketKey = 'my-bucket-'.time();
		$rs = $this->createBucket($bucketKey);
		if (!$rs) {
			jsonFeedback(false, 'Error: failed to create bucket!');
		}
		$rs = $this->uploadFile($bucketKey);
		$urn = $rs['objects'][0]['id'];
		$rs = $this->registerFileWithService($urn);
		if ($rs) {
			jsonOutput(array('success' => true, 'msg' => \FileRun\Lang::t('Processing data...'), 'urn' => base64_encode($urn), 'access_token' => $this->authData['access_token']));
		}
	}

	function createBucket($key) {
		$http = new \GuzzleHttp\Client();
		try {
			$response = $http->request('POST', $this->bucketsURL, [
				'headers' => [
					'Authorization' => 'Bearer ' . $this->authData['access_token']
				],
				'json' => [
					'bucketKey' => $key,
					'policy' => 'transient'
				]
			]);
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			jsonFeedback(false, 'Network connection error: '.$e->getMessage());
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			$response = $e->getResponse()->getBody()->getContents();
			$rs = @json_decode($response, true);
			if (!is_array($rs)) {
				jsonFeedback(false, 'Error while creating bucket: '. $response);
			}
			jsonFeedback(false, 'Error while creating bucket: '. $rs['reason']);
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			jsonFeedback(false, 'Server error: '.$e->getResponse()->getStatusCode());
		} catch (RuntimeException $e) {
			jsonFeedback(false, 'Error: '.$e->getMessage());
		}
		$rs = $response->getBody()->getContents();
		if (!$rs) {
			jsonFeedback(false, 'Error creating bucket: empty server response!');
		}
		return json_decode($rs, true);
	}

	function uploadFile($bucketKey) {
		global $fm;
		$http = new \GuzzleHttp\Client();
		try {
			$response = $http->request('PUT', $this->bucketsURL.'/'.S::forURL($bucketKey).'/objects/'.S::forURL($this->data['fileName']), [
				'headers' => [
					'Authorization' => 'Bearer ' . $this->authData['access_token'],
					'Content-Length', $fm->getFileSize($this->data['filePath']),
					'Content-Type', 'application/octet-stream',
					'Expect', ''
				],
				'body' => fopen($this->data['filePath'], 'r')
			]);
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			jsonFeedback(false, 'Network connection error: '.$e->getMessage());
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			$rs = json_decode($e->getResponse()->getBody()->getContents(), true);
			jsonFeedback(false, 'Error while uploading file: '. $rs['reason']);
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			jsonFeedback(false, 'Server error: '.$e->getResponse()->getStatusCode());
		} catch (RuntimeException $e) {
			jsonFeedback(false, 'Error: '.$e->getMessage());
		}
		$rs = $response->getBody()->getContents();
		if (!$rs) {
			jsonFeedback(false, 'Error uploading file: empty server response!');
		}
		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "Autodesk"
		));
		return json_decode($rs, true);
	}

	function registerFileWithService($urn) {
		$http = new \GuzzleHttp\Client();
		try {
			$response = $http->request('POST', $this->registerURL, [
				'headers' => [
					'Authorization' => 'Bearer ' . $this->authData['access_token']
				],
				'json' => ['urn' => base64_encode($urn)]
			]);
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			jsonFeedback(false, 'Network connection error: '.$e->getMessage());
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			$rs = json_decode($e->getResponse()->getBody()->getContents(), true);
			jsonFeedback(false, 'Error while registering file with service: '. $rs['reason']);
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			jsonFeedback(false, 'Server error: '.$e->getResponse()->getStatusCode());
		} catch (RuntimeException $e) {
			jsonFeedback(false, 'Error: '.$e->getMessage());
		}
		$rs = $response->getBody()->getContents();
		if (!$rs) {
			jsonFeedback(false, 'Error registering file: empty server response!');
		}
		return json_decode($rs, true);
	}

	function checkStatus() {
		$urn = S::fromHTML($_REQUEST['urn']);
		$access_token = S::fromHTML($_REQUEST['access_token']);

		$http = new \GuzzleHttp\Client();
		try {
			$response = $http->request('GET', $this->viewURL.'/'.$urn.'/status', [
				'headers' => [
					'Authorization' => 'Bearer ' . $access_token
				],
				'json' => ['urn' => base64_encode($urn)]
			]);
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			jsonFeedback(false, 'Network connection error: '.$e->getMessage());
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			$rs = json_decode($e->getResponse()->getBody()->getContents(), true);
			jsonFeedback(false, 'Error checking status: '. $rs['reason']);
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			jsonFeedback(false, 'Server error: '.$e->getResponse()->getStatusCode());
		} catch (RuntimeException $e) {
			jsonFeedback(false, 'Error: '.$e->getMessage());
		}
		$rs = $response->getBody()->getContents();
		if (!$rs) {
			jsonFeedback(false, 'Error checking status: empty server response!');
		}
		$rs = json_decode($rs, true);
		$percent = strstr($rs['success'], '%', true);
		jsonOutput(array('success' => true, 'data' => $rs, 'percent' => $percent));
	}
}