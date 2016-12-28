<?php
$url = $config['url']['root'];
$url .= "/?module=fileman_myfiles&section=ajax&page=thumbnail";
$url .= "&path=".S::forURL($this->data['relativePath']);
$url .= "&noCache=1";
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
    <title></title>
	<script>
		function run() {
			document.getElementById('imgHolder').innerHTML = '<img src="<?php echo $url?>&width='+encodeURIComponent(document.body.clientWidth)+'&height='+encodeURIComponent(document.body.clientHeight)+'" border="0" />';
		}
	</script>
	<style>body {margin:0px;padding:0px;}</style>
</head>

<body onload="javscript:run()">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="center" id="imgHolder"></td>
</tr>
</table>
</body>
</html>