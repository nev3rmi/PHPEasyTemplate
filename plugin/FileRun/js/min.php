<?php
chdir(dirname(dirname(__FILE__)));
if (isset($_GET['cpanel'])) {
	$files = array(
		'js/ext/ux/ScriptLoader.js',
		'js/cpanel/ext.overrides.js',
		'js/cpanel/app.js',
		'js/cpanel/tree.js',
		'js/cpanel/grid.js',
		'js/cpanel/layout.js',
		'js/fileman/user_chooser.js',
		'js/cpanel/userslist.comp.js',
		'js/cpanel/editform.comp.js',
		'js/genpass.js',
		'js/ext/ux/statusbar/StatusBar.js'
	);
} else if (isset($_GET['extjs'])) {
	if (isset($_GET['debug'])) {
		$files = array(
			'js/ext/adapter/ext/_ext-base-debug.js',
			'js/ext/_ext-all-debug-w-comments.js',
			'js/ext/ux/overrides.js',
			'js/ext/ux/LocalStorage.js',
			'js/ext/ux/FileRunPrompt.js',
			'js/ext/ux/ListPanel.js'
		);
	} else {
		$files = array(
			'js/ext/adapter/ext/ext-base.js',
			'js/ext/ext-all.js',
			'js/ext/ux/overrides.js',
			'js/ext/ux/LocalStorage.js',
			'js/ext/ux/FileRunPrompt.js',
			'js/ext/ux/ListPanel.js'
		);
	}
} else if (isset($_GET['weblink_gallery'])) {
		$files = array(
			'js/jquery/jquery.min.js',
			'js/nanobar.min.js',
			'js/headroom.min.js',
			'js/jquery/jG/jquery.justifiedGallery.min.js',
			'js/weblink.js'
		);
		if (isset($_GET['debug'])) {
			$files[] = 'js/jquery/swipebox/js/_jquery.swipebox.js';
		} else {
			$files[] = 'js/jquery/swipebox/js/jquery.swipebox.min.js';
		}
} else if (isset($_GET['file_request'])) {
	$files = array(
		'js/file_request.js'
	);
	if (isset($_GET['debug'])) {
		$files[] = 'js/flow/_flow.js';
		$files[] = 'js/flow/_flowfile.js';
		$files[] = 'js/flow/_flowchunk.js';
	} else {
		$files[] = 'js/flow/all-standalone.min.js';
	}
} else if (isset($_GET['flow'])) {
	if (isset($_GET['debug'])) {
		$files[] = 'js/flow/_flow.js';
		$files[] = 'js/flow/_flowfile.js';
		$files[] = 'js/flow/_flowchunk.js';
		$files[] = 'js/flow/_flowext.js';
	} else {
		$files[] = 'js/flow/all.min.js';
	}
} else if (isset($_GET['flow-standalone'])) {
	if (isset($_GET['debug'])) {
		$files[] = 'js/flow/_flow.js';
		$files[] = 'js/flow/_flowfile.js';
		$files[] = 'js/flow/_flowchunk.js';
	} else {
		$files[] = 'js/flow/all-standalone.min.js';
	}
} else {
	$files = array(
		'js/ext/ux/ProgressColumn/ProgressColumn.js',
		'js/ext/ux/GridDragSelector.js',
		'js/fileman/filerun.js',
		'js/fileman/toolbars_and_menus.js',
		'js/fileman/grid.js',
		'js/fileman/tree.js',
		'js/fileman/info_panel.js',
		'js/fileman/details_panel.js',
		'js/fileman/download_cart.js',
		'js/fileman/activity_panel.js',
		'js/fileman/comments_panel.js',
		'js/fileman/layout.js',
		'js/fileman/ui_utils.js',
		'js/fileman/actions.js',
		'js/fileman/image_viewer.js'
	);
	if (isset($_GET['debug'])) {
		$files[] = 'js/flow/_flow.js';
		$files[] = 'js/flow/_flowfile.js';
		$files[] = 'js/flow/_flowchunk.js';
		$files[] = 'js/flow/_flowext.js';
	} else {
		$files[] = 'js/flow/all.min.js';
	}
}


if (extension_loaded("zlib") && (ini_get("output_handler") != "ob_gzhandler")) {
	ini_set("zlib.output_compression", 1);
}

header("Content-Type: application/javascript; charset=UTF-8");
header("Cache-control: public");
header("Pragma: cache");
header("Expires: " . gmdate ("D, d M Y H:i:s", time() + 31356000) . " GMT");

foreach ($files as $key => $file) {
	readfile($file);
	echo "\r\n";
}