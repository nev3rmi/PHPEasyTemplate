<?php
global $app, $settings, $fm, $config;
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/style.css?v=<?php echo $settings->currentVersion;?>" />
	<link rel="stylesheet" type="text/css" href="customizables/custom_actions/zamzar/style.css?v=<?php echo $settings->currentVersion;?>" />
	<link rel="stylesheet" type="text/css" href="css/ext.php?v=<?php echo $settings->currentVersion;?>" />
	<script type="text/javascript" src="js/min.php?extjs=1&v=<?php echo $settings->currentVersion;?>"></script>
	<script src="customizables/custom_actions/zamzar/app.js?v=<?php echo $settings->currentVersion;?>"></script>
	<script src="?module=fileman&section=utils&page=translation.js&sec=<?php echo S::forURL("Custom Actions: Zamzar")?>&lang=<?php echo S::forURL(\FileRun\Lang::getCurrent())?>"></script>
	<script>
		var URLRoot = '<?php echo S::safeJS($config['url']['root'])?>';
		var path = '<?php echo S::safeJS($this->data['relativePath'])?>';
		var windowId = '<?php echo S::safeJS(S::fromHTML($_REQUEST['_popup_id']))?>';
	</script>
	<style>.ext-el-mask { background-color: white; }</style>
</head>

<body>
<div id="selectFormat">
	<div style="clear:both;margin:10px;">
		<?php
		if (sizeof($rs['targets']) > 0){
			echo self::t('Convert "%1" to:', array(S::safeHTML($this->data['fileName'])));
		} else {
			echo self::t('No conversion option found for the "%1" file type.', array(strtoupper(S::safeHTML($fm->getExtension($this->data['fileName'])))));
		}
		?>
	</div>
	<div style="max-width:600px">
	<?php
	foreach ($rs['targets'] as $format) {
		$fileTypeInfo = $fm->fileTypeInfo(false, $format['name']);
		?>
		<div class="format" data-format="<?php echo S::safeHTML($format['name']);?>">
		<img src="images/fico/<?php echo $fileTypeInfo['icon'];?>" width="96" height="96" border="0" />
			<?php echo S::safeHTML(strtoupper($format['name']))?>
		</div>
	<?php
	}
	?>
	</div>
</div>
</body>
</html>