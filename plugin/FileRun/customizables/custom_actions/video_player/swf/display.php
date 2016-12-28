<?php
$URL = $config['url']['root']."/?module=custom_actions&action=video_player&method=stream&path=".S::forURL($this->data['relativePath']);
?>
<html>
<head>
	<title></title>
	<style>body {border: 0px;margin: 0px;padding: 0px;overflow:hidden;}</style>
</head>

<body>

<script type="text/javascript" src="<?php echo $config['url']['root']?>/js/swfobject/swfobject.js"></script>
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="height:100%" border="0">
<tr>
	<td align="center" valign="middle">
	<div id="swf" style="width:100%;height:100%;">Loading Flash movie...</div>
	<script type="text/javascript">
		swfobject.embedSWF("<?php echo $URL?>", "swf", '100%', '100%', "9.0.124");
	</script>
	</td>
</tr>
</table>
</body>
</html>