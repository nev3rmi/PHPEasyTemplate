<?php
/*
	Bing KML viewer
*/
class custom_bing_kml_viewer {
	var $online = true;
	function init() {
		$this->JSconfig = array(
			"title" => \FileRun\Lang::t("Bing Maps", 'Custom Actions: Bing Maps'),
			"iconCls" => 'fa fa-fw fa-cloud', 'icon' => 'images/icons/bing.png',
			"extensions" => array("xml", "kmz", "kml"),
			"popup" => true
		);
	}
	function run() {
	$url = $this->weblinks->getOneTimeDownloadLink($this->data['filePath']);
	if (!$url) {
		echo "Failed to setup weblink";
		exit();
	}
	$proto = isSSL() ? "https" : "http";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>View with Bing</title>
    <script type="text/javascript" src="<?php echo $proto?>://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.3"></script>
<script type="text/javascript">
var map = null;
function EventMapLoad() {
	var shapeLayer = new VEShapeLayer();
	var shapeSpec = new VEShapeSourceSpecification(VEDataType.ImportXML,"<?php echo $url; ?>", shapeLayer);
	map.ImportShapeLayerData(shapeSpec);
}
function CreateMap() {
	map = new VEMap('myMap');
	//Api key is not mandatory, use only if you want generate access report on your bing account
	//map.SetCredentials("Your API KEY");
	map.onLoadMap = EventMapLoad;
	map.LoadMap(null, 3, VEMapStyle.Hybrid);
}
</script>
</head>
<body onload="CreateMap();">
    <div id="myMap" style="position:absolute;width: 100%; height: 100%;"></div>
</body>
</html>
<?php
	}
}