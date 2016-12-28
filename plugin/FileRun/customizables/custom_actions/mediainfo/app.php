<?php
class custom_mediainfo {
	function init() {
		$this->config = array(
			'localeSection' => 'Custom Actions: MediaInfo'
		);
		$this->JSconfig = array(
			'title' => $this->getString('Media Info'),
			'iconCls' => 'fa fa-fw fa-info-circle',
			'requiredUserPerms' => array('download'),
			"popup" => true,
			'width' => 500
		);
	}
	function getString($s) {

		return S::safeJS(\FileRun\Lang::t($s, $this->config['localeSection']));
	}
	function run() {
		global $fm;



/*
		\FileRun\MetaTypes::$debug=true;
		global $db;
		//$db->debug=1;
		\FileRun\MetaTypes::auto($this->data['filePath']);exit();
*/
		//asdf(\FileRun\Media\Image\Image::fromFile($this->data['filePath'])->getXmp()->dom->saveHTML());

/*
				$rec = \FileRun\Media\Image\Image::fromFile($this->data['filePath'])->getReconcile();
				$rec->debug=true;
				asdf($rec->get('DateCreated'));
*/


		$getID3 = new getID3;
		$fInfo = $getID3->analyze($this->data['filePath']);
		//asdf($fInfo);
		require($this->path."/display.php");
	}
	function displayRow($title, $value) {

		$tmp = '';
		if (is_array($value)) {
			foreach($value as $v) {
				$tmp .= '<div>';
				$tmp .= S::safeHTML(S::forHTML($v));
				$tmp .= '</div>';
			}
		} else {
			$tmp = S::safeHTML(S::forHTML($value));
		}
		if (strlen($tmp) > 0 && $tmp != "0") {
			echo '<tr>';
				echo '<td>'.$title.'</td>';
				echo '<td>'.$tmp.'</td>';
			echo '</tr>';
		}
	}
}
