<?php 
global $config, $settings;
?>
<html dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title></title>
	<script src="customizables/custom_actions/webodf/webodf.js" type="text/javascript" charset="utf-8"></script>
	<script>
	var URLRoot = '<?php echo S::safeJS($config['url']['root'])?>';
	var path = '<?php echo S::safeJS($this->data['relativePath'])?>';
	var fName = '<?php echo S::safeJS($this->data['fileName'])?>';
	var fileURL = URLRoot+'/?module=custom_actions&action=webodf&method=download&path='+encodeURIComponent(path)+'&filename='+encodeURIComponent(fName);
	</script>
</head>
<body id="theBODY">
	<div id="odf"></div>
	<script type="text/javascript">
		var odfelement = document.getElementById("odf"),
		odfcanvas = new odf.OdfCanvas(odfelement);
		odfcanvas.load(fileURL);
	</script>
</body>
</html>