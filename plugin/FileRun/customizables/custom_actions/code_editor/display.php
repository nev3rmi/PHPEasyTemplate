<?php
global $app, $settings, $config;
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/style.css?v=<?php echo $settings->currentVersion;?>" />
	<link rel="stylesheet" type="text/css" href="css/ext.php?v=<?php echo $settings->currentVersion;?>" />
	<script type="text/javascript" src="js/min.php?extjs=1&v=<?php echo $settings->currentVersion;?>"></script>
	<script src="customizables/custom_actions/code_editor/app.js?v=<?php echo $settings->currentVersion;?>"></script>
	<script src="?module=fileman&section=utils&page=translation.js&sec=<?php echo S::forURL("Custom Actions: Text Editor")?>&lang=<?php echo S::forURL(\FileRun\Lang::getCurrent())?>"></script>
	<script src="customizables/custom_actions/code_editor/ace/ace.js" type="text/javascript" charset="utf-8"></script>
	<script src="customizables/custom_actions/code_editor/ace/ext-modelist.js" type="text/javascript" charset="utf-8"></script>
	<script>
		var URLRoot = '<?php echo S::safeJS($config['url']['root'])?>';
		var path = '<?php echo S::safeJS($this->data['relativePath'])?>';
		var filename = '<?php echo S::safeJS($this->data['fileName'])?>';
		var windowId = '<?php echo S::safeJS(S::fromHTML($_REQUEST['_popup_id']))?>';
		var charset = '<?php echo S::safeJS(S::fromHTML($_REQUEST['charset']))?>';
		var charsets = <?php
		$list = array();
		foreach($enc as $e) {
			$list[] = array($e);
		}
		echo json_encode($list);
		?>
	</script>
	<style>.ext-el-mask { background-color: white; }</style>
</head>

<body id="theBODY" onload="FR.init()">

<textarea style="display:none" id="textContents"><?php echo S::safeHTML(S::convert2UTF8($this->data['fileContents']))?></textarea>

</body>
</html>