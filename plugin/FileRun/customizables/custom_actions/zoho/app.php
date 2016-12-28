<?php

class custom_zoho {

	var $online = true;
	static $localeSection = "Custom Actions: Zoho";

	var $writerExtensions = array("doc", "docx", "html", "htm", "rtf", "txt", "odt");
	var $sheetExtensions = array("xls", "xlsx", "sxc", "csv", "ods", "tsv");
	var $showExtensions = array("ppt", "pptx", "pps", "odp", "sxi", "ppts", "ppsx");

	var $writerURL = "https://writer.zoho.com/writer/remotedoc.im";
	var $sheetURL = "https://sheet.zoho.com/sheet/remotedoc.im";
	var $showURL = "https://show.zoho.com/show/remotedoc.im";

	function init() {
		$this->settings = array(
			array(
				'key' => 'APIKey',
				'title' => self::t('API key'),
				'comment' => \FileRun\Lang::t('Get it from %1', 'Admin', array('<a href="https://zapi.zoho.com" target="_blank">https://zapi.zoho.com</a>'))
			)
		);
		$this->JSconfig = array(
			"title" => self::t("Zoho Editor"),
			'icon' => 'images/icons/zoho.png',
			"extensions" => array_merge($this->writerExtensions, $this->sheetExtensions, $this->showExtensions),
			"popup" => true,
			"requiredUserPerms" => array("download", 'upload'),
			"createNew" => array(
				"title" => self::t("Document with Zoho"),
				"options" => array(
					array(
						"fileName" => self::t("New Document.odt"),
						"title" => self::t("Word Document"),
						"icon" => 'images/icons/zwriter.png'
					),
					array(
						"fileName" => self::t("New Spreadsheet.ods"),
						"title" => self::t("Spreadsheet"),
						"icon" => 'images/icons/zsheet.png'
					),
					array(
						"fileName" => self::t("New Presentation.odp"),
						"title" =>  self::t("Presentation"),
						"icon" => 'images/icons/zshow.png'
					)
				)
			)
		);
	}

	function isDisabled() {
		return (strlen(self::getSetting('APIKey')) == 0);
	}

	static function getSetting($k) {
		global $settings;
		$key = 'plugins_zoho_'.$k;
		return $settings->{$key};
	}

	static function t($text, $vars = false) {
		return \FileRun\Lang::t($text, self::$localeSection, $vars);
	}

	function run() {
		global $fm, $auth;
		\FileRun::checkPerms("download");

		if (\FileRun\Perms::check('upload') && (!$this->data['shareInfo'] || ($this->data['shareInfo'] && $this->data['shareInfo']['perms_upload']))) {
			$weblinkInfo = $this->weblinks->createForService($this->data['filePath'], false, $this->data['shareInfo']['id']);
			if (!$weblinkInfo) {
				echo "Failed to setup saving weblink";
				exit();
			}
			$saveURL = $this->weblinks->getSaveURL($weblinkInfo['id_rnd'], false, "zoho");
		} else {
			$saveURL = "";
		}

		$extension = $fm->getExtension($this->data['fileName']);
		if (in_array($extension, $this->writerExtensions)) {
			$url = $this->writerURL;
		} else if (in_array($extension, $this->showExtensions)) {
			$url = $this->showURL;
		} else {
			$url = $this->sheetURL;
		}

		$post = array(
			['name' => 'apikey', 'contents' => self::getSetting('APIKey')],
			['name' => 'username', 'contents' => $auth->currentUserInfo['name']],
			['name' => 'output', 'contents' => 'url'],
			['name' => 'mode', 'contents' => 'collabedit'],
			['name' => 'filename', 'contents' => $this->data['fileName']],
			['name' => 'format', 'contents' => $extension],
			['name' => 'saveurl', 'contents' => $saveURL],
			['name' => 'content', 'contents' => fopen($this->data['filePath'], 'r')]
		);

		$d = \FileRun\MetaFields::getTable();
		$docIdMetaFieldId = $d->selectOneCol('id', array(array('system', '=', 1), array('name', '=', $d->q('zoho_collab'))));
		$metaFileId = FileRun\MetaFiles::getId($this->data['filePath']);
		if ($metaFileId) {
			$zohoDocId = \FileRun\MetaValues::get($metaFileId, $docIdMetaFieldId);
			if ($zohoDocId) {
				$post[] = ['name' => 'documentid', 'contents' => $zohoDocId];
				$post[] = ['name' => 'id', 'contents' => $zohoDocId];
			}
		} else {
			$post[] = ['name' => 'id', 'contents' => uniqid(rand())];
		}

		$http = new \GuzzleHttp\Client();
		try {
			$response = $http->request('POST', $url, [
				'headers' => ['User-Agent' => ''],
				'multipart' => $post
			]);
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			jsonFeedback(false, 'Network connection error: '.$e->getMessage());
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			echo 'Server error: '.$e->getResponse()->getStatusCode();
			echo $e->getMessage();
			exit();
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			echo 'Server error: '.$e->getResponse()->getStatusCode();
			echo $e->getMessage();
			exit();
		} catch (RuntimeException $e) {
			echo 'Error: '.$e->getMessage();
			exit();
		}
		$rs = $response->getBody()->getContents();
		if (!$rs) {
			jsonFeedback(false, 'Error uploading file: empty server response!');
		}
		$rs = $this->parseZohoReply($rs);
		if ($rs['RESULT'] != "FALSE") {
			//save document id for collaboration
			if ($rs['DOCUMENTID']) {
				\FileRun\MetaValues::setByPath($this->data['filePath'], $docIdMetaFieldId, $rs['DOCUMENTID']);
			}

			\FileRun\Log::add(false, "preview", array(
				"relative_path" => gluePath($this->data['relativePath'], $this->data['fileName']),
				"full_path" => $this->data['filePath'],
				"method" => "Zoho"
			));
			header("Location: ".$rs['URL']."");
			exit();
		} else {
			echo "<strong>Zoho:</strong>";
			echo "<div style=\"margin:5px;border:1px solid silver;padding:5px;overflow:auto;\"><pre>";
			echo $response;
			if (strstr($rs['warning'], "unable to import content")) {
				echo "\r\n\r\nZoho.com service does not support this type of documents or was not able to access this web server.";
			} else {
				echo $response;
			}
			echo "</pre></div>";
		}
	}
	function createBlankFile() {
		global $myfiles, $fm;
		if (!\FileRun\Perms::check('upload')) {exit();}
		if (file_exists($this->data['filePath'])) {
			jsonOutput(array("rs" => false, "msg" => self::t('A file with the specified name already exists. Please try again.')));
		}
		$rs = $myfiles->newFile($fm->dirname($this->data['relativePath']), $this->data['fileName'], false, "");
		if ($rs) {
			jsonOutput(array("rs" => true, 'path' => $this->data['relativePath'], "filename" => $this->data['fileName'], "msg" => self::t("Blank file created successfully")));
		} else {
			jsonOutput(array("rs" => false, "msg" => $myfiles->error['msg']));
		}
	}
	function getNewFileContents() {
		if ($_FILES['content']['tmp_name']) {
			return file_get_contents($_FILES['content']['tmp_name']);
		}
	}
	function parseZohoReply($reply) {
		$lines = explode("\n", $reply);
		$rs = array();
		foreach ($lines as $line) {
			$line = trim($line);
			$p = strpos($line, "=");
			$key = substr($line, 0, $p);
			$val = substr($line, $p+1);
			if (strlen($key) > 0) {
				$rs[$key] = $val;
			}
		}
		return $rs;
	}
}