<?php

class custom_onlyoffice {

	var $online = true;
	var $outputName = 'content';
	var $canEdit = array(
		'doc', 'docx', 'odt', 'rtf', 'txt',
		'xls', 'xlsx', 'ods', 'csv',
		'ppt', 'pptx', 'odp'
	);

	function init() {
		$this->settings = array(
			array(
				'key' => 'serverURL',
				'title' => self::t('ONLYOFFICE server URL'),
				'comment' => self::t('Download and install %1', array('<a href="https://github.com/ONLYOFFICE/DocumentServer" target="_blank">ONLYOFFICE DocumentServer</a>'))
			)
		);
		$this->JSconfig = array(
			"title" => self::t("ONLYOFFICE"),
			"popup" => true,
			'icon' => 'images/icons/onlyoffice.png',
			"loadingMsg" => self::t('Loading document in ONLYOFFICE. Please wait...'),
			'useWith' => array(
				'office'
			),
			"requiredUserPerms" => array(
				"download",
				"upload"
			),
			"createNew" => array(
				"title" => self::t("Document with ONLYOFFICE"),
				"options" => array(
					array(
						"fileName" => self::t("New Document.docx"),
						"title" => self::t("Word Document"),
						"iconCls" => 'fa fa-fw fa-file-word-o'
					),
					array(
						"fileName" => self::t("New Spreadsheet.xlsx"),
						"title" => self::t("Spreadsheet"),
						"iconCls" => 'fa fa-fw fa-file-excel-o'
					),
					array(
						"fileName" => self::t("New Presentation.pptx"),
						"title" =>  self::t("Presentation"),
						"iconCls" => 'fa fa-fw fa-file-powerpoint-o'
					)
				)
			)
		);
	}

	function isDisabled() {
		return (strlen(self::getSetting('serverURL')) == 0);
	}

	static function getSetting($k) {
		global $settings;
		$key = 'plugins_onlyoffice_'.$k;
		return $settings->{$key};
	}

	static function t($text, $vars = false) {
		$section = 'Custom Actions: ONLYOFFICE';
		return \FileRun\Lang::t($text, $section, $vars);
	}

	function getNewFileContents() {
		$body_stream = file_get_contents("php://input");
		if ($body_stream === false) {return false;}
		$this->POST = json_decode($body_stream, true);
		if ($this->POST["status"] != 2) {return false;}
		return file_get_contents($this->POST["url"]);
	}

	function saveFeedback($success, $message) {
		if ($this->POST["status"] != 2) {
			//ONLYOFFICE makes various calls to the save URL
			exit('{"error":0}');
		}
		if ($success) {
			exit('{"error":0}');
		} else {
			exit($message);
		}
	}
	
	function createBlankFile() {
		global $myfiles, $fm;
		if (!\FileRun\Perms::check('upload')) {exit();}
		$ext = $fm->getExtension($this->data['fileName']);
		if (!in_array($ext, $this->canEdit)) {
			jsonOutput(array("rs" => false, "msg" => self::t('The file extension needs to be one of the following: %1', array(implode(', ', $this->canEdit)))));
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

	function run() {
		global $fm;
		\FileRun::checkPerms("download");

		if (\FileRun\Perms::check('upload') && (!$this->data['shareInfo'] || ($this->data['shareInfo'] && $this->data['shareInfo']['perms_upload']))) {
			$weblinkInfo = $this->weblinks->createForService($this->data['filePath'], false, $this->data['shareInfo']['id']);
			if (!$weblinkInfo) {
				echo "Failed to setup saving weblink";
				exit();
			}
			$saveURL = $this->weblinks->getSaveURL($weblinkInfo['id_rnd'], false, "onlyoffice");
		} else {
			$saveURL = "";
		}

		$extension = $fm->getExtension($this->data['fileName']);

		if (in_array($extension, array('docx', 'doc','odt','txt','rtf','html','htm','mht','epub','pdf','djvu','xps'))) {
			$docType = 'text';
		} else if (in_array($extension, array('xlsx','xls','ods','csv'))) {
			$docType = 'spreadsheet';
		} else {
			$docType = 'presentation';
		}

		$url = $this->weblinks->getURL(array('id_rnd' => $weblinkInfo['id_rnd'], 'download' => 1));

		if (!$url) {
			echo "Failed to setup weblink";
			exit();
		}

		//add the action to the user activity log
		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "ONLYOFFICE"
		));

		$mode = 'view';
		if (in_array($extension, $this->canEdit)) {
			$mode = 'edit';
		}

		global $auth;
		$author = \FileRun\Users::formatFullName($auth->currentUserInfo);

		$fileSize = \FM::getFileSize($this->data['filePath']);
		$documentKey = substr($fileSize.md5($this->data['filePath']), 0, 20);
?>
	<html>
	<head>
		<title></title>
		<style>
			body {
				border: 0;
				margin: 0;
				padding: 0;
				overflow:hidden;
			}
		</style>
	</head>

	<body>
	<div id="placeholder"></div>
	<script type="text/javascript" src="<?php echo self::getSetting('serverURL');?>/web-apps/apps/api/documents/api.js"></script>
	<script>
		var innerAlert = function (message) {
			if (console && console.log)
			console.log(message);
		};

		var onReady = function () {
			innerAlert("Document editor ready");
		};

		var onDocumentStateChange = function (event) {
			var title = document.title.replace(/\*$/g, "");
			document.title = title + (event.data ? "*" : "");
		};

		var onError = function (event) {
			if (event) innerAlert(event.data);
		};
		var docEditor = new DocsAPI.DocEditor("placeholder", {
			"documentType": "<?php echo $docType;?>",
			"type": "desktop",
			"document": {
				"fileType": "<?php echo $extension;?>",
				"key": "<?php echo $documentKey;?>",
				"title": "<?php echo \S::safeJS($this->data['fileName']);?>",
				"url": "<?php echo \S::safeJS($url);?>",
				"info": {
					"author": "<?php echo \S::safeJS($author);?>"
				}
			},
			"editorConfig": {
				"mode": '<?php echo $mode;?>',
				"lang": '<?php echo $this->getShortLangName(\FileRun\Lang::getCurrent());?>',
				"callbackUrl": "<?php echo \S::safeJS($saveURL);?>",
				"user": {
					"firstname": "<?php echo \S::safeJS($auth->currentUserInfo['name']);?>",
					"id": "<?php echo \S::safeJS($auth->currentUserInfo['id']);?>",
					"lastname": "<?php echo \S::safeJS($auth->currentUserInfo['name2']);?>"
				}
			},
			"customization": {
				'about': false,
				'comments': false,
				'feedback': false,
				'goback': false
			},
			"events": {
				'onReady': onReady,
				'onDocumentStateChange': onDocumentStateChange,
				'onError': onError
			}
		});
	</script>
	</body>
	</html>
	<?php
	}

	function getShortLangName($langName) {
		$codes = array(
			'basque' => 'eu',
			'brazilian portuguese' => 'pt',
			'chinese traditional' => 'zh',
			'chinese' => 'zh',
			'danish' => 'da',
			'dutch' => 'nl',
			'english' => 'en',
			'finnish' => 'fi',
			'french' => 'fr',
			'german' => 'de',
			'italian' => 'it',
			'polish' => 'pl',
			'romanian' => 'ro',
			'russian' => 'ru',
			'spanish' => 'es',
			'swedish' => 'sv',
			'turkish' => 'tr'
		);
		return $codes[$langName];
	}
}