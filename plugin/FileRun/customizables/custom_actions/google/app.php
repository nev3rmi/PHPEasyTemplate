<?php

class custom_google {

	var $online = true;
	var $ext = array(
		'docs' => array(
			'doc' => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'odt' => 'application/vnd.oasis.opendocument.text'
		),
		'sheets' => array(
			'xls' => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
		),
		'slides' => array(
			'ppt' => 'application/vnd.ms-powerpoint',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'odp' => 'application/vnd.oasis.opendocument.presentation'
		)
	);

	function init() {
		$this->settings = array(
			array(
				'key' => 'clientID',
				'title' => self::t('OAuth Client ID'),
			),
			array(
				'key' => 'clientSecret',
				'title' => self::t('OAuth Client Secret'),
				'comment' => self::t('Read the <a href="%1" target="_blank">configuration guide</a>.', array('http://docs.filerun.com/google_editor_integration'))
			)
		);

		$this->JSconfig = array(
			"title" => self::t("Google Docs Editor"),
			"icon" => 'images/icons/gdrive.png',
			"extensions" => array_merge(array_keys($this->ext['docs']), array_keys($this->ext['sheets']), array_keys($this->ext['slides'])),
			"popup" => true, "width" => 600, 'height' => 350,
			"requiredUserPerms" => array("download", 'upload'),
			"createNew" => array(
				"title" => self::t("Document with Google"),
				"options" => array(
					array(
						"fileName" => self::t("New Document.docx"),
						"title" => self::t("Word Document"),
						"icon" => 'images/icons/gdocs.png'
					),
					array(
						"fileName" => self::t("New Spreadsheet.xlsx"),
						"title" => self::t("Spreadsheet"),
						"icon" => 'images/icons/gsheets.png'
					),
					array(
						"fileName" =>  self::t("New Presentation.pptx"),
						"title" => self::t("Presentation"),
						"icon" => 'images/icons/gslides.png'
					)
				)
			)
		);
		$this->outputName = "content";
	}

	function isDisabled() {
		return (strlen(self::getSetting('clientID')) == 0 || strlen(self::getSetting('clientSecret')) == 0);
	}

	static function t($v, $r = false) {
		return \FileRun\Lang::t($v, 'Custom Actions: Google Editor', $r);
	}

	static function getSetting($k) {
		global $settings;
		$key = 'plugins_google_'.$k;
		return $settings->{$key};
	}

	function run() {
		\FileRun::checkPerms("download");
		require($this->path."/display.php");
	}

	function getGoogleClient() {
		global $config;
		$client = new Google_Client();
		$client->setClientId(self::getSetting('clientID'));
		$client->setClientSecret(self::getSetting('clientSecret'));
		$client->setScopes(array(Google_Service_Drive::DRIVE_FILE));
		$redirectURI = $config['url']['root'].'/?module=custom_actions&action=google&method=getToken';
		$client->setRedirectUri($redirectURI);
		return $client;
	}
	function gauth() {
		$client = $this->getGoogleClient();
		if ($_GET['error']) {
			echo S::safeHTML(S::fromHTML($_GET['error']));
			exit();
		}
		$authUrl = $client->createAuthUrl();
		header('Location: '.$authUrl);
		exit();
	}
	function getToken() {
		$client = $this->getGoogleClient();
		$code = S::fromHTML($_GET['code']);
		if ($code) {
			$rs = $client->authenticate($code);
			if ($rs) {
				session_start();
				$_SESSION['FileRun']['googleAPItoken'] = $client->getAccessToken();
                echo self::t('Sending document data to Google. Please wait.');
				echo '<script>with (window.opener) {FR.sendFile();}</script>';
				exit();
			} else {
				echo self::t('Failed to authenticate');
				exit();
			}
		} else {
			$auth_url = $client->createAuthUrl();
			header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
			exit();
		}
	}
	function sendFile() {
		$client = $this->getGoogleClient();
		session_start();
		if (!$_SESSION['FileRun']['googleAPItoken']) {exit('Missing token');}
		$client->setAccessToken($_SESSION['FileRun']['googleAPItoken']);
		$service = new Google_Service_Drive($client);

		$fileOpts = ['name' => $this->data['fileName']];
		$ext = \FM::getExtension($this->data['fileName']);
		if (array_key_exists($ext, $this->ext['docs'])) {
			$mime = $this->ext['docs'][$ext];
			$fileOpts['mimeType'] = 'application/vnd.google-apps.document';
        } else if (array_key_exists($ext, $this->ext['sheets'])) {
			$mime = $this->ext['sheets'][$ext];
			$fileOpts['mimeType'] = 'application/vnd.google-apps.spreadsheet';
		} else if (array_key_exists($ext, $this->ext['slides'])) {
			$mime = $this->ext['slides'][$ext];
			$fileOpts['mimeType'] = 'application/vnd.google-apps.presentation';
		}
		$file = new Google_Service_Drive_DriveFile($fileOpts);
		try {
			$opts = array(
				'data' => file_get_contents($this->data['filePath']),
				'mimeType' => $mime,
				'uploadType' => 'multipart',
				'fields' => 'webViewLink,id'
			);
			$result = $service->files->create($file, $opts);
		} catch (Google_Service_Exception $e) {
			$errors = $e->getErrors();
			jsonOutput(array('rs' => false, 'msg' => $errors[0]['message']));
		}
		jsonOutput(array('rs' => true, 'data' => $result));
	}
	function retrieveFile() {
		global $myfiles, $fm;
		$client = $this->getGoogleClient();
		session_start();
		if (!$_SESSION['FileRun']['googleAPItoken']) {exit('Missing token');}
		$client->setAccessToken($_SESSION['FileRun']['googleAPItoken']);
		$service = new Google_Service_Drive($client);

		$ext = \FM::getExtension($this->data['fileName']);
		$extension = false;
		if ($ext == 'doc') {
			$ext = 'docx';
			$extension = $ext;
		} else if ($ext == 'xls') {
			$ext = 'xlsx';
			$extension = $ext;
		} else if ($ext == 'ppt') {
			$ext = 'pptx';
			$extension = $ext;
		}
		if (array_key_exists($ext, $this->ext['docs'])) {
			$mime = $this->ext['docs'][$ext];

		} else if (array_key_exists($ext, $this->ext['sheets'])) {
			$mime = $this->ext['sheets'][$ext];
		} else if (array_key_exists($ext, $this->ext['slides'])) {
			$mime = $this->ext['slides'][$ext];
		}
		$filename = $this->data['fileName'];
		if ($extension) {
			$filename = $fm->replaceExtension($this->data['fileName'], $extension);
		}
		try {
			$r = $service->files->export($_REQUEST['fileId'], $mime, array('alt' => 'media'));
		} catch(Google_Service_Exception $er) {
			$json = json_decode($er->getMessage(), true);
			jsonOutput(array('rs' => false, 'msg' => $json['error']['errors'][0]['message']));
		}
		$contents = $r->getBody()->getContents();
		$rs = $myfiles->newFile($fm->dirname($this->data['relativePath']), $filename, false, $contents);
		$this->deleteRemoteFile();
		if ($rs) {
			jsonOutput(array('rs' => true, 'filename' => $filename, 'msg' => self::t('File saved as "%1"', array($filename))));
		} else {
			jsonOutput(array('rs' => false));
		}
	}
	function deleteRemoteFile() {
		$client = $this->getGoogleClient();
		session_start();
		if (!$_SESSION['FileRun']['googleAPItoken']) {exit('Missing token');}
		$client->setAccessToken($_SESSION['FileRun']['googleAPItoken']);
		$service = new Google_Service_Drive($client);
		try {
			$service->files->delete($_REQUEST['fileId']);
		} catch (Google_Service_Exception $e) {
			$errors = $e->getErrors();
			jsonOutput(array('rs' => false, 'msg' => $errors[0]['message']));
			return false;
		}
		return true;
	}
	function createBlankFile() {
		global $myfiles, $fm;
		if (!\FileRun\Perms::check('upload')) {exit();}
		$ext = $fm->getExtension($this->data['fileName']);
		if (!in_array($ext, $this->JSconfig['extensions'])) {
			jsonOutput(array("rs" => false, "msg" => self::t('The file extension needs to be one of the following: %1', array(implode(', ', $this->JSconfig['extensions'])))));
		}
		if (file_exists($this->data['filePath'])) {
			jsonOutput(array("rs" => false, "msg" => self::t('A file with the specified name already exists. Please try again.')));
		}
		$src = gluePath($this->path, 'blanks/blank.'.$ext);
		$rs = $myfiles->newFile($fm->dirname($this->data['relativePath']), $this->data['fileName'], $src);
		if ($rs) {
			jsonOutput(array("rs" => true, 'path' => $this->data['relativePath'], "filename" => $this->data['fileName'], "msg" => self::t("Blank file created successfully")));
		} else {
			jsonOutput(array("rs" => false, "msg" => $myfiles->error['msg']));
		}
	}
}