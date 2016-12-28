FR = {
	currentSelectedFile: '', currentPath: false, previousPath: false, components: {}, actions: {}, tmp: {}, ext: [], customActions: {}, copyingPaths: [], isMobile: false,
	UI: {xy: [], popups: [], tabs: [], tree: {}, changePassWindow: '', uploadTabs: {java: false, html: false,flash: false}, folderDelConfirmWin: false, grid: {}},
	localSettings: {
		get: function(s, def) {
			var v = Ext.state.Manager.getProvider().get('fr-settings-'+s);
			return def ? (v ? v : def) : v;
		},
		set: function(s, v) {
			Ext.state.Manager.getProvider().set('fr-settings-'+s, v);
		}
	},
	labels: new Ext.util.MixedCollection(),
	audioNotification: function() {
		var audio = Ext.DomHelper.append(Ext.getBody(), {tag: 'audio', src:'sounds/new.mp3', preload: 'auto'});
		if (audio.canPlayType) {audio.play();}
	}
};
Ext.onReady(function() {
	FR.baseURL = URLRoot;
	FR.iconsURL = URLRoot+'/images/icons';
	FR.myfilesBaseURL = URLRoot+'/?module=fileman_myfiles&section=ajax';
	if (Settings.logoutURL) {
		FR.logoutURL = Settings.logoutURL;
	} else {
		FR.logoutURL = URLRoot + '/?module=fileman&page=logout';
		if (Settings.logout_redirect) {
			FR.logoutURL += '&redirect=' + encodeURIComponent(Settings.logout_redirect);
		}
	}
	Ext.QuickTips.init();
	Ext.apply(Ext.QuickTips.getQuickTip(), {trackMouse: true});

	if (Ext.getBody().getSize().width < 480) {
		FR.isMobile = true;
	}

	FR.initToolbar();
	FR.initTree();
	FR.initLayout();

	if (User.perms.file_history && Settings.enablePusher) {
		var pusher = new Pusher(Settings.pusherAppKey, {authEndpoint: '?module=fileman&section=utils&page=pusher_auth', encrypted: true});
		var notifications = pusher.subscribe('private-'+User.id);
		notifications.bind('notifications', function(data) {
			if (data.action == 'comment_added') {if (!User.perms.read_comments) {return false;}}
			if (data.msg) {FR.UI.feedback(data.msg);}
			FR.UI.activityPanel.updateStatus(1, true);
		});
		pusher.subscribe('presence-channel');
	}

	FR.pushState = true;
	window.onpopstate = function (event) {
		FR.pushState = false;
		if (event.state == null) {return;}
		var path = event.state.path;
		if (path) {
			FR.utils.browseToPath(path);
		}
	};

	var startPath = decodeURI(document.location.hash.substring(1));
	if (startPath.substring(0, 1) != '/') {
		startPath = '/HOME';
	}
	FR.utils.browseToPath('/ROOT'+startPath);

	if (User.requiredToChangePass) {
		new Ext.ux.prompt({
			text: FR.T('You are required to change your password.'),
			callback: FR.actions.openAccountSettings
		});
	}
	if (Settings.welcomeMessage.length > 0) {
		new Ext.ux.prompt({text: FR.T(Settings.welcomeMessage)});
	}
	Ext.getDoc().on('paste', FR.actions.handlePaste);
	Ext.fly('loadMsg').fadeOut();
});