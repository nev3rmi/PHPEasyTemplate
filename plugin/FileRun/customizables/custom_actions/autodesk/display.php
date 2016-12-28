<?php
global $app, $settings, $config;
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title></title>
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" type="text/css" href="css/ext.php?v=<?php echo $settings->currentVersion;?>" />
	<link rel="stylesheet" href="<?php echo $this->viewURL;?>/viewers/style.css" type="text/css">
	<script src="<?php echo $this->viewURL;?>/viewers/viewer3D.min.js"></script>
	<script type="text/javascript" src="js/min.php?extjs=1&v=<?php echo $settings->currentVersion;?>"></script>
	<script src="customizables/custom_actions/autodesk/app.js?v=<?php echo $settings->currentVersion;?>"></script>
	<script src="?module=fileman&section=utils&page=translation.js&sec=<?php echo S::forURL(self::$localeSection)?>&lang=<?php echo S::forURL(\FileRun\Lang::getCurrent())?>"></script>
	<script>
		var URLRoot = '<?php echo S::safeJS($config['url']['root'])?>';
		var path = '<?php echo S::safeJS($this->data['relativePath'])?>';
		var filename = '<?php echo S::safeJS($this->data['fileName'])?>';
		var windowId = '<?php echo S::safeJS(S::fromHTML($_REQUEST['_popup_id']))?>';
	</script>
</head>

<body id="theBODY">
</body>
</html>