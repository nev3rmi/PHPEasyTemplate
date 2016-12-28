<?php
class custom_markdown_viewer {
	var $localeSection = 'Custom Actions: Markdown Viewer';
	function init() {
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Markdown Viewer", $this->localeSection),
			'iconCls' => 'fa fa-fw fa-quote-right',
			'extensions' => array('md'),
			"popup" => true,
			"requiredUserPerms" => array("download")
		);
	}
	function run() {
		\FileRun::checkPerms("download");
		$this->data['fileContents'] = file_get_contents(S::forFS($this->data['filePath']));
		$enc = mb_list_encodings();
		if ($_REQUEST['charset'] && in_array($_REQUEST['charset'], $enc)) {
			$this->data['fileContents'] = S::convert2UTF8($this->data['fileContents'], $_REQUEST['charset']);
		}

?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<link href="<?php echo $this->url;?>/markdown.css" rel="stylesheet" />
		</head>
		<body class="markdown-body">
<?php echo \FileRun\Utils\Markup\Markdown::toHTML($this->data['fileContents']);?>
		</body>
		</html>
<?php
		\FileRun\Log::add(false, "preview", array(
			"relative_path" => $this->data['relativePath'],
			"full_path" => $this->data['filePath'],
			"method" => "Markdown Viewer"
		));
	}
}