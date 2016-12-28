<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/style.css?v=<?php echo $settings->currentVersion;?>" />
	<link rel="stylesheet" type="text/css" href="css/ext.php?v=<?php echo $settings->currentVersion;?>" />
	<script type="text/javascript" src="js/min.php?extjs=1&v=<?php echo $settings->currentVersion;?>"></script>
	<script src="customizables/custom_actions/audio_player/js/soundmanager2-nodebug-jsmin.js?v=<?php echo $settings->currentVersion;?>"></script>
	<script type="text/javascript" src="customizables/custom_actions/audio_player/js/app.js?v=<?php echo $settings->currentVersion;?>"></script>
	<script type="text/javascript" src="customizables/custom_actions/audio_player/js/aurora.js?v=<?php echo $settings->currentVersion;?>"></script>
	<script type="text/javascript" src="customizables/custom_actions/audio_player/js/flac.js?v=<?php echo $settings->currentVersion;?>"></script>
	<script type="text/javascript">
		var URLRoot = '<?php echo S::safeJS($config['url']['root'])?>';
		FR.popupId = '<?php echo S::safeJS(S::fromHTML($_REQUEST['_popup_id']))?>';
		FR.countFiles = <?php echo sizeof($audioFiles)?>;
		FR.files = <?php echo json_encode($audioFiles)?>;
		FR.currentIndex = <?php echo $currentIndex?>;
	</script>
	<style>
		.x-grid3-scroller {overflow-x: hidden;}
	</style>
</head>

<body id="theBODY" onload="FR.init()">
</body>
</html>