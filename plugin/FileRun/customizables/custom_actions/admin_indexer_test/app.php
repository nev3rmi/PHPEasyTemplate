<?php
class custom_admin_indexer_test {
	function init() {
		$this->config = array(
			'localeSection' => 'Custom Actions: Admin: Text Indexer Test'
		);
		$this->JSconfig = array(
			'title' => $this->getString('Admin: Text Indexer Test'),
			'iconCls' => 'fa fa-fw fa-bug',
			'requiredUserPerms' => array('download'),
			"popup" => true,
			'width' => 500
		);
	}
	function isDisabled() {
		global $settings;
		return (!$settings->search_enable || !\FileRun\Perms::isSuperUser());
	}
	function getString($s) {
		return S::safeJS(\FileRun\Lang::t($s, $this->config['localeSection']));
	}
	function run() {
		$search = new \FileRun\Search();
		$tika = $search->getApacheTika();
		header('Content-type: text/plain; charset=UTF-8');
		if (\FM::isPlainText(array('fileName' => $this->data['fileName']))) {
			echo $tika->getText($this->data['filePath']);
		} else {
			echo S::convert2UTF8($tika->getText($this->data['filePath']));
		}
	}
}