<?php
class custom_handle_url {
    var $localeSection = 'Custom Actions: Link Opener';
	function init() {
		$this->JSconfig = array(
			'title' => \FileRun\Lang::t('Link Opener', $this->localeSection),
			'iconCls' => 'fa fa-fw fa-share-square-o',
			'extensions' => array('url'),
			'replaceDoubleClickAction' => true,
			'popup' => true,
            'requiredUserPerms' => array('download')
		);
	}
	function run() {
       \FileRun::checkPerms("download");
		$c = file($this->data['filePath']);
		foreach ($c as $r) {
			if (stristr($r, 'URL=') !== false) {
				header('Location: '.str_ireplace(array('URL=', '\''), array(''), $r));
				exit();
			}
		}
	}
}
