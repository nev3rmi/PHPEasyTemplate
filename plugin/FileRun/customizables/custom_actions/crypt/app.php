<?php

class custom_crypt {

	static $localeSection = 'Custom Actions: File Encryption';

	function init() {
		$pathToAESCrypt = 'H:/apps/tools/aescrypt.exe'; //and set the path here
		$this->config = array(
			'encrypt_command' => $pathToAESCrypt.' -e -p [%pass%] [%filePath%]',
			'decrypt_command' => $pathToAESCrypt.' -d -p [%pass%] [%filePath%]',
			'encrypted_file_extension' => 'aes',
			'debug' => false
		);
		$this->settings = array(
			array(
				'key' => 'pathToAESCrypt',
				'title' => self::t('Path to AESCrypt'),
				'comment' => self::t('Download and install AESCrypt from <a href="%1" target="_blank">here</a>.', array('https://www.aescrypt.com'))
			)
		);
		$this->JSconfig = array(
            'nonTouch' => true,
			'title' => self::t('AES File Encryption'),
			'iconCls' => 'fa fa-fw fa-lock',
			'requiredUserPerms' => array('upload', 'alter'),
			'fn' => 'FR.customActions.crypt.run()'
		);
	}
	function isDisabled() {
		return (strlen(self::getSetting('pathToAESCrypt')) == 0);
	}

	static function getSetting($k) {
		global $settings;
		$key = 'plugins_crypt_'.$k;
		return $settings->{$key};
	}

	static function t($text, $vars = false) {
		return \FileRun\Lang::t($text, self::$localeSection, $vars);
	}
	function run() {
		global $fm;
		if (!\FileRun\Perms::check("upload") || !\FileRun\Perms::check("download")) {
			jsonFeedback(false, self::t("The user doesn't have permission to use this function!"));
		} else {
			$deleteSrc = (S::fromHTML($_POST['deleteSrc']) == 1 ? true : false);
			if (is_file($this->data['filePath'])) {
				$extension = $fm->getExtension($this->data['fileName']);
				session_write_close();
				if ($extension == $this->config['encrypted_file_extension']) {
					$targetFileName = str_replace(".".$this->config['encrypted_file_extension'], "", $this->data['fileName']);
					$targetPath = gluePath($this->data['path'], $targetFileName);
					if (!file_exists($targetPath)) {
						$rs = $this->decrypt();
						if ($rs) {
							if ($deleteSrc) {
								$this->deleteSource();
							}
							\FileRun\Log::add(false, "file_decrypted", array(
								"relative_path" => $this->data['relativePath'],
								"to_relative_path" => gluePath($fm->dirname($this->data['relativePath']), $targetFileName),
								"full_path" => $this->data['filePath'],
								"to_full_path" => $targetPath,
								"method" => "AES"
							), $this->data['filePath']);
							jsonFeedback(true, self::t("The selected file was successfully decrypted."));
						} else {
							jsonFeedback(false, self::t("Failed to decrypt the selected file!"));
						}
					} else {
						jsonFeedback(false, self::t("A file named \"%1\" already exists!", array($targetFileName)));
					}
				} else {
					$targetFileName = $this->data['fileName'].".".$this->config['encrypted_file_extension'];
					$targetPath = gluePath($this->data['path'], $targetFileName);
					if (!file_exists($targetPath)) {
						$rs = $this->encrypt();
						if ($rs) {
							if ($deleteSrc) {
								$this->deleteSource();
							}
							\FileRun\Log::add(false, "file_encrypted", array(
								"relative_path" => $this->data['relativePath'],
								"to_relative_path" => gluePath($fm->dirname($this->data['relativePath']), $targetFileName),
								"full_path" => $this->data['filePath'],
								"to_full_path" => $targetPath,
								"method" => "AES"
							), $this->data['filePath']);
							jsonFeedback(true, self::t("The selected file was successfully encrypted."));
						} else {
							jsonFeedback(false, self::t("Failed to encrypt the selected file!"));
						}
					} else {
						jsonFeedback(false, self::t("A file named \"%1\" already exists!", array($targetFileName)));
					}
				}
			} else {
				jsonFeedback(false, self::t("The selected file was not found!"));
			}
		}
	}
	function JSinclude() {
		include(gluePath($this->path, "include.js.php"));
	}
	function encrypt() {
		$cmd = $this->parseCmd($this->config['encrypt_command']);
		return $this->runCmd($cmd);
	}
	function decrypt() {
		$cmd = $this->parseCmd($this->config['decrypt_command']);
		return $this->runCmd($cmd);
	}
	function parseCmd($cmd) {
		return str_replace(
			array("[%pass%]", "[%filePath%]"), 
			array($this->escapeshellarg(S::fromHTML($_POST['pass'])), $this->escapeshellarg($this->data['filePath'])),
		$cmd);
	}
	function escapeshellarg($s) {
		return '"'.addslashes($s).'"';
	}
	function deleteSource() {
		global $myfiles, $fm;
		return $myfiles->deleteFile($fm->dirname($this->data['relativePath']), $this->data['fileName'], $permanent = false);
	}
	function runCmd($cmd) {
		@exec($cmd, $return_text, $return_code);
		if ($return_code != 0) {
			if ($this->config['debug']) {
				echo " * command: ".$cmd."<br>";
				echo " * returned code: ".$return_code."<br>";
				echo " * returned text: "; print_r($return_text);
				flush();
			}
			return false;
		} else {
			return true;
		}
	}
}
