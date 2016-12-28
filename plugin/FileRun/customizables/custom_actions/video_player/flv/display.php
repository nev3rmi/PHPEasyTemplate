<?php
global $fm, $settings;

$getID3 = new getID3;
$fInfo = $getID3->analyze($this->data['filePath']);

$path = str_replace('&', '%26', $this->data['relativePath']);

$URL = $config['url']['root']."/";
$URL .= S::forURL("?");
$URL .= "module=custom_actions";
$URL .= S::forURL("&");
$URL .= "action=video_player";
$URL .= S::forURL("&");
$URL .= "method=stream";
$URL .= S::forURL("&");
$URL .= "path=".S::forURL($path);
?>
<html>
<head>
	<title></title>
	<style>
		body {
			background-color: black;
			border: 0px;
			margin: 0px;
			padding: 0px;
			overflow:hidden;
		}
	</style>
	<script type="text/javascript" charset="utf-8" src="<?php echo $config['url']['root']?>/js/swfobject/swfobject.js?v=<?php echo $settings->currentVersion;?>"></script>
</head>

<body>
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="height:100%" border="0">
	<tr>
		<td align="center" valign="middle">
		<div id="videoPlayer" style="width:100%;height:100%;"></div>
		<script type="text/javascript">
			var videoSize = {
				width: parseInt('<?php echo $fInfo['video']['resolution_x']?:0; ?>'),
				height: parseInt('<?php echo $fInfo['video']['resolution_y']?:0; ?>')
			};
			if (videoSize.width < 100 || videoSize.width > window.innerWidth) {
				videoSize.width = '100%';
			}
			if (videoSize.height < 100 || videoSize.width > window.innerHeight) {
				videoSize.height = '100%';
			}
			var flashVars = {
				video: "<?php echo $URL?>",
				skin: '<?php echo $config['url']['root']?>/customizables/custom_actions/video_player/flv/mySkin.swf?v=<?php echo $settings->currentVersion;?>',
				autoplay: 1,
				menu: false,
			};
			var params = {
				allowFullScreen: true,
				wmode: 'window',
				quality: 'high'
			}
			swfobject.embedSWF("<?php echo $config['url']['root']?>/customizables/custom_actions/video_player/flv/player.swf?v=<?php echo $settings->currentVersion;?>", "videoPlayer", videoSize.width, videoSize.height, "9.0.0", "js/swfobjectexpressInstall.swf", flashVars, params);
		</script>
		</td>
	</tr>
	</table>
</body>
</html>