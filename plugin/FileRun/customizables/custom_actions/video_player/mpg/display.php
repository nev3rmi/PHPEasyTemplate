<?php
$URL = $config['url']['root']."/?module=custom_actions&action=video_player&method=stream&path=".S::forURL($this->data['relativePath']);
?>
<html>
<head>
	<title></title>
	<style>
		body {
			border: 0px;
			margin: 0px;
			padding: 0px;
			overflow:hidden;
		}
	</style>
</head>

<body>

<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="height:100%" border="0">
<tr>
	<td align="center" valign="middle">
	<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" height="100%" width="100%" align="center">
	<param name="src" value="<?php echo $URL?>">
	<param name="autoplay" value="true">
	<param name="controller" value="true">
	<embed height="100%" width="100%" align="center" src="<?php echo $URL?>" autoplay="true" controller="true">
	</embed>
	</object>
	</td>
</tr>
</table>
</body>
</html>