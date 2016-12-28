<?php

$rs = $this->weblinks->createForService($data['filePath'], 5, $data['relativePath']);
$URL = $this->weblinks->getURL(array(
		"id_rnd" => $rs['id_rnd'],
		"filename" => $this->data['fileName'],
		"password" => $rs['password']
	)
);
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
	 <OBJECT id="mediaPlayer" width="420" height="385" classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" standby="Loading Microsoft Windows Media Player components..." type="application/x-oleobject">
      <param name="fileName" value="<?php echo $URL?>">
      <param name="animationatStart" value="true">
      <param name="transparentatStart" value="true">
      <param name="autoStart" value="true">
      <param name="showControls" value="true">
      <param name="loop" value="true">
      <EMBED type="application/x-mplayer2" pluginspage="http://microsoft.com/windows/mediaplayer/en/download/" id="mediaPlayer" name="mediaPlayer" displaysize="4" autosize="-1" bgcolor="darkblue" showcontrols="true" showtracker="-1" showdisplay="0" showstatusbar="-1" videoborder3d="-1" width="420" height="385" src="<?php echo $URL?>" autostart="true" designtimesp="5311" loop="true">
      </EMBED>
      </OBJECT>
	</td>
</tr>
</table>
</body>
</html>