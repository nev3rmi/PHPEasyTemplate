<?php
global $config, $settings, $fm;
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/ext.php?v=<?php echo $settings->currentVersion;?>" />
	<script type="text/javascript" src="js/min.php?extjs=1&v=<?php echo $settings->currentVersion;?>"></script>
	<script type="text/javascript" src="customizables/custom_actions/arch/grid.js?v=<?php echo $settings->currentVersion;?>"></script>
	<style>
		.x-grid3-cell {font-family: tahoma, arial, helvetica, sans-serif;}
	</style>
</head>

<body>
	<table id="theTable" width="100%">
		<thead>
			<tr class="header">
				<th name="filename">File name</td>
				<th name="size">Size</td>
				<th name="path">Path</td>
			</tr>
		</thead>
		<tbody>
		<?php
		$limit = 50;
		$i = 0;
		foreach ($list as $key => $item) {
			if ($i == $limit) {
				?>
				<tr>
					<td>Archive contains <?php echo $count-$limit;?> files more that are not displayed in this preview.</td>
					<td>&nbsp;</td>
				</tr>
				<?php
				break;
			}
			if ($item['checksum'] != "00000000") {
				if ($item['utf8_encoded']) {
					$srcEnc = "UTF-8";
				} else {
					if ($config['app']['encoding']['unzip']) {//convert from a predefined encoding
						$srcEnc = $config['app']['encoding']['unzip'];
					} else {
						$srcEnc = S::detectEncoding($item['filename']);
					}
				}

				$item['filename'] = \S::convert2UTF8($item['filename'], $srcEnc);
				if ($item['type'] == "file" && $item['filename']) {
				?>
				<tr>
					<td><img src="<?php echo $fm->getFileIconURL($item['filename']);?>" border="0" height="16" width="16"> <?php echo S::safeHTML($item['filename']);?></td>
					<td width="100"><?php echo $fm->formatFileSize($item['filesize']);?></td>
					<td><?php echo S::forHTML(gluePath("/", $item['path']), 1);?></td>
				</tr>
				<?php
				$i++;
				}
			}
		}
		?>
		</tbody>
	</table>
</body>
</html>