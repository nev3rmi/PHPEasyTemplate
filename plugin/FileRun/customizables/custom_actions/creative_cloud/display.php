<?php

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
	<!-- Load Feather code -->
	<script type="text/javascript" src="https://dme0ih8comzn4.cloudfront.net/imaging/v3/editor.js"></script>
	<!-- Instantiate Feather -->
	<script type='text/javascript'>
		var featherEditor = new Aviary.Feather({
			apiKey: '2ad1d525b8a44842a9f7cb03f9e6ddef',
			theme: 'light', displayImageSize: true,
			tools: 'all', noCloseButton: true, fileFormat: 'original',
			appendTo: 'injection_site',
			onLoad: function() {
				launchEditor('image1', '<?php echo S::safeJS($this->data['fileURL'])?>');
			},
			onSave: function(imageID, newURL) {
				var img = document.getElementById(imageID);
				img.src = newURL;
				with (window.parent) {
					Ext.Ajax.request({
						url: '<?php echo S::safeJS($this->data['saveURL'])?>&fromURL='+encodeURIComponent(newURL),
						callback: function(opts, succ, req) {
							FR.utils.reloadGrid();
							FR.UI.feedback(req.responseText);
						}
					});
				}
			},
			onError: function(errorObj) {alert(errorObj.message);}
		});
		function launchEditor(id, src) {
			featherEditor.launch({
				image: id,
				url: src
			});
			return false;
		}
	</script>
</head>

<body>
<div id="injection_site"></div>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td align="center" valign="middle">
	<img id="image1" style="display:none;" src="<?php echo $this->data['fileURL']?>" />
	</td>
</tr>
</table>
</body>
</html>