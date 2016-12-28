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
	<script src="customizables/custom_actions/google/app.js?v=<?php echo $settings->currentVersion;?>"></script>
	<script src="?module=fileman&section=utils&page=translation.js&sec=<?php echo S::forURL("Custom Actions: Google")?>&lang=<?php echo S::forURL(\FileRun\Lang::getCurrent())?>"></script>
	<script>
		var URLRoot = '<?php echo S::safeJS($config['url']['root'])?>';
		var path = '<?php echo S::safeJS($this->data['relativePath'])?>';
		var filename = '<?php echo S::safeJS($this->data['fileName'])?>';
		var windowId = '<?php echo S::safeJS(S::fromHTML($_REQUEST['_popup_id']))?>';
		var gAuthResult;
	</script>
    <style>.ext-el-mask { background-color: white; }</style>
</head>

<body id="theBODY" onload="FR.init()">


</body>
</html>