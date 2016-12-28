<?php
class custom_code_editor {
	function init() {
		$this->localeSection = "Custom Actions: Text Editor";
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Text Editor", $this->localeSection),
			'iconCls' => 'fa fa-fw fa-file-text-o',
			'useWith' => array('txt', 'noext'),
			"popup" => true,
			"createNew" => array(
				"title" => \FileRun\Lang::t("Text File", $this->localeSection),
				"options" => array(
					array(
						'fileName' => \FileRun\Lang::t('New Text File.txt', $this->localeSection),
						'title' => \FileRun\Lang::t('Plain Text', $this->localeSection),
						'iconCls' => 'fa fa-fw fa-file-text-o',
					),
					array(
						'fileName' => 'index.html',
						'title' => \FileRun\Lang::t('HTML', $this->localeSection),
						'iconCls' => 'fa fa-fw fa-file-code-o',
					),
					array(
						'fileName' => 'script.js',
						'title' => \FileRun\Lang::t('JavaScript', $this->localeSection),
						'iconCls' => 'fa fa-fw fa-file-code-o',
					),
					array(
						'fileName' => 'style.css',
						'title' => \FileRun\Lang::t('CSS', $this->localeSection),
						'iconCls' => 'fa fa-fw fa-file-code-o',
					),
					array(
						'fileName' => 'index.php',
						'title' => \FileRun\Lang::t('PHP', $this->localeSection),
						'iconCls' => 'fa fa-fw fa-file-code-o',
					),
					array(
						'fileName' => 'readme.md',
						'title' => \FileRun\Lang::t('Markdown', $this->localeSection),
						'iconCls' => 'fa fa-fw fa-file-code-o',
					),
					array(
						'fileName' => '',
						'title' => \FileRun\Lang::t('Other..', $this->localeSection),
						'iconCls' => 'fa fa-fw fa-file-text-o',
					)
				)
			),
			"requiredUserPerms" => array("download", "upload")
		);
	}
	function run() {
		\FileRun::checkPerms("download");
		$this->data['fileContents'] = file_get_contents(S::forFS($this->data['filePath']));
		$enc = mb_list_encodings();
		if ($_REQUEST['charset'] && in_array($_REQUEST['charset'], $enc)) {
			$this->data['fileContents'] = S::convert2UTF8($this->data['fileContents'], $_REQUEST['charset']);
		}
		require($this->path."/display.php");
		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "Code Editor"
		));
	}
	function saveChanges() {
		global $myfiles, $fm;
		\FileRun::checkPerms("upload");
		$textContents = S::fromHTML($_POST['textContents']);
		$charset = S::fromHTML($_POST['charset']);
		if ($charset != 'UTF-8') {
			$textContents = S::convertEncoding($textContents, 'UTF-8', $charset);
		}
		$rs = $myfiles->newFile($fm->dirname($this->data['relativePath']), $this->data['fileName'], false, $textContents);
		if ($rs) {
			jsonOutput(array("rs" => true, "filename" => $this->data['fileName'], "msg" => \FileRun\Lang::t("File successfully saved", $this->localeSection)));
		} else {
			jsonOutput(array("rs" => false, "msg" => $myfiles->error['msg']));
		}
	}
	function createBlankFile() {
		global $myfiles, $fm;
		 \FileRun::checkPerms("upload");
		if (strlen($this->data['fileName']) == 0) {
			jsonOutput(array("rs" => false, "msg" => \FileRun\Lang::t('Please type a file name', $this->localeSection)));
		} else {
			if (is_file($this->data['filePath'])) {
				jsonOutput(array("rs" => false, "msg" => \FileRun\Lang::t('A file with that name already exists', $this->localeSection)));
			}
			$rs = $myfiles->newFile($fm->dirname($this->data['relativePath']), $this->data['fileName'], false, "");
			if ($rs) {
				jsonOutput(array("rs" => true, 'path' => $this->data['relativePath'], "filename" => $this->data['fileName'], "msg" => \FileRun\Lang::t("File successfully created", $this->localeSection)));
			} else {
				jsonOutput(array("rs" => false, "msg" => $myfiles->error['msg']));
			}
		}
	}
}