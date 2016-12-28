<?php
$getID3 = new getID3;
$fInfo = $getID3->analyze($this->data['filePath']);
$URL = $config['url']['root']."/?module=custom_actions&action=video_player&method=stream&path=".S::forURL($this->data['relativePath']);
?>
<html>
<head>
	<title></title>
	<style>
		body {
			border: 0;
			margin: 0;
			padding: 0;
			overflow: hidden;
		}
		video {
			position: absolute;
			min-width: 100%;
			min-height: 100%;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
		}
	</style>
</head>

<body>
<script>
	var videoSize = {
		width: parseInt('<?php echo $fInfo['video']['resolution_x']?:0; ?>'),
		height: parseInt('<?php echo $fInfo['video']['resolution_y']?:0; ?>')
	};

	var html = '<video controls="controls" autoplay="autoplay"';

	if (window.innerWidth < window.innerHeight) {
		var width = videoSize.width;
		if (videoSize.width > window.innerWidth) {
			width = window.innerWidth;
		}
		html += ' width="'+width+'"';
	} else {
		var height = videoSize.height;
		if (videoSize.height > window.innerHeight) {
			height = window.innerHeight;
		}
		html += ' height="'+height+'"';
	}

	html += ' src="<?php echo $URL?>"></video>';
	document.write(html);
</script>
</body>
</html>