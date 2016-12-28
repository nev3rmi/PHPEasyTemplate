FR.initToolbar = function() {
	/* Labels */
	var labels = [{text: FR.T('No label'), handler: function(item){FR.actions.setLabel('');}}];
	FR.labels.each(function(label) {
		labels.push({
			text: '<span style="color:'+label.color+';">'+label.text+'</span>', labelInfo: label,
			handler: function(item) {
				FR.actions.setLabel(label.text+'|'+label.color);
			}
		});
	});
	labels.push('-');
	labels.push({
		text: FR.T('Custom label')+'..',
		handler: function() {return false;},
		menu: new Ext.menu.ColorMenu({
			allowReselect: true,
			colors: [
				'000000', '00008B', '0000FF', '8B008B', '8A2BE2', '4682B4', '1E90FF',
				'00FFFF', '006400', '008000', '008B8B', '00FF00', '7FFF00', 'FF0000', 'DC143C',
				'A52A2A', 'FF1493', 'FF4500', 'FF8C00', 'FFFF00', '808080'
			], height: 62,
			handler: function(cm, color){
				new Ext.ux.prompt({
					title: FR.T('Custom label'),
					text: FR.T('Please type the %1label\'s text%2').replace('%1', '<span class="FRLabel" style="background-color:#'+color+';">').replace('%2', '</span>'),
					defaultValue: FR.T('New label'),
					confirmHandler: function(text) {FR.actions.setLabel(text+'|#'+color);}
				});
			}
		})
	});



	/* "Open with" and "Create" items */
	FR.customActions.unshift({
		actionName: 'open_in_browser',
		title: FR.T('External Window'), iconCls: 'fa-external-link', requiredUserPerms: ['download'],
		handler: function() {
			var item = FR.UI.gridPanel.getOneSel();
			var path = item.data.path ? item.data.path : FR.currentPath+'/'+item.data.filename;
			return FR.actions.openFileInBrowser(path);
		}
	});


	var createNewFileItems = [];
	Ext.each(FR.customActions, function(ca) {
		if (ca.createNew) {
			var show = true;
			Ext.each(ca.requiredUserPerms, function(perm) {
				if (!User.perms[perm]) {show = false;}
			});
			if (show) {
				var menu = false;
				var handler = function() {return FR.actions.createNew(ca);};
				if (ca.createNew.options) {
					menu = [];
					Ext.each(ca.createNew.options, function(o) {
						menu.push({
							text: o.title, icon: o.icon, iconCls: o.iconCls,
							handler: function() {return FR.actions.createNew(ca, o.fileName);}
						});
					});
					handler = function() {return false;};
				}
				createNewFileItems.push(new Ext.Action({
					text: FR.T(ca.createNew.title), icon: ca.icon, iconCls: ca.iconCls,
					handler: handler, menu: menu
				}));
			}
		}
	});


	var searchMetaFields = [];
	Ext.each(FR.searchMetaColumns, function(item) {
		searchMetaFields.push({
			text: item.n, handler: function () {
				var s = FR.UI.actions.searchField;
				s.searchParams.metafield = item.id;
				s.doSearch('meta');
			}
		});
	});

	if (Settings.fullTextSearch || FR.searchMetaColumns.length > 0) {
		var searchOptsMenu = new Ext.menu.Menu({
			items: [
				{
					text: FR.T('Search file names'),
					handler: function() {
						FR.UI.actions.searchField.doSearch('filename');
					}
				},{
					text: FR.T('Search file contents'), hidden: !Settings.fullTextSearch,
					handler: function() {
						FR.UI.actions.searchField.doSearch('contents');
					}
				},
				{
					text: FR.T('Search metadata'), hidden: (FR.searchMetaColumns.length == 0),
					menu: searchMetaFields
				}
			]
		});
	}

	FR.UI.actions = {
		searchField: new FR.components.SearchBox({width:258, listeners: {
			'show':function(){FR.UI.actions.searchOpts.show();},
			'hide':function(){FR.UI.actions.searchOpts.hide();}
		}}),
		searchOpts: new Ext.Action({
			iconCls: 'fa fa-caret-down', cls: 'search-trigger',
			menu: searchOptsMenu,
			handler: function() {
				if (!this.menu) {FR.UI.actions.searchField.doSearch('filename');}
			},
			listeners: {'afterrender': (Settings.fullTextSearch || FR.searchMetaColumns.length > 0) ? FR.UI.tooltip(FR.T('Search options')) : function(){}}
		}),
		logout: new Ext.Action({
			text: FR.T('Sign out'),
			iconCls: 'fa-sign-out',
			handler:function(){document.location.href = FR.logoutURL;},
			hidden: Settings.hideLogout
		}),
		cpanel: new Ext.Action({
			iconCls: 'fa fa-fw fa-cog',
			listeners: {'afterrender': FR.UI.tooltip(FR.T('Control Panel'))},
			handler:function(){
				FR.UI.popup({
					src: FR.baseURL+'/?module=cpanel',
					title: FR.T('Control Panel')
				});
			}, hidden: (!User.isAdmin && !User.isIndep)
		}),
		help: new Ext.Action({
			iconCls: 'fa fa-fw fa-question',
			listeners: {'afterrender': FR.UI.tooltip(FR.T('Help'))},
			handler:function(){
				FR.UI.popup({src: Settings.helpURL, noId:1});
			}, hidden: (FR.isMobile || !Settings.helpURL)
		}),
		createNewFolder: new Ext.Action({
			text: FR.T('Folder'), iconCls: 'fa-folder',
			handler: function() {
				var path;
				if (FR.utils.currentFolderAllowsUpload()) {
					path = FR.currentPath;
				} else {
					path = '/ROOT/HOME';
				}
				new Ext.ux.prompt({
					title: FR.T('Create new folder'), defaultValue: FR.T('New Folder'),
					confirmHandler: function(folderName) {
						if (folderName) {
							FR.actions.newFolder(path, folderName);
						}
					}
				});
			}
		}),
		fileRequest: new Ext.Action({
			text: FR.T('File request'), iconCls: 'fa-plus-square', hidden: !User.perms.weblink,
			handler: function() {
				var path;
				if (FR.utils.currentFolderAllowsUpload()) {
					path = FR.currentPath;
				} else {
					path = '/ROOT/HOME';
				}
				new Ext.ux.prompt({
					title: FR.T('What are you requesting?'), placeHolder: FR.T('Photos, Documents, Contracts...'),
					confirmHandler: function(folderName) {
						if (folderName) {
							FR.actions.newFolder(path, folderName, function(folderName) {
								FR.actions.WebLink(path+'/'+folderName, folderName, true);
							});
						}
					}
				});
			}
		}),
		weblink: new Ext.Action({
			iconCls: 'fa fa-link fa-fw', hidden: true,
			listeners: {'afterrender': FR.UI.tooltip(FR.T('Get link'))},
			handler: function() {
				var item = FR.UI.gridPanel.getOneSel();
				var path = (item.data.path || FR.currentPath+'/'+item.data.filename);
				return FR.actions.WebLink(path, item.data.filename);
			}
		}),
		shareWithUsers: new Ext.Action({
			iconCls: 'fa fa-user-plus fa-fw', hidden: true,
			listeners: {'afterrender': FR.UI.tooltip(FR.T('Share with users'))},
			handler: function() {
				FR.UI.contextMenu.location = 'grid';
				FR.UI.contextMenu.target = FR.UI.gridPanel.getSelectedFiles();
				FR.contextMenuActions.shareWithUsers(FR.UI.contextMenu);
			}
		}),
		preview: new Ext.Action({
			iconCls: 'fa fa-eye fa-fw', hidden: true,
			listeners: {'afterrender': FR.UI.tooltip(FR.T('Preview'))},
			handler: function() {return FR.utils.showPreview();}
		}),
		remove: new Ext.Action({
			iconCls: 'fa fa-trash fa-fw', hidden: true,
			listeners: {'afterrender': FR.UI.tooltip(FR.T('Remove'))},
			handler: function() {
				FR.UI.contextMenu.location = 'grid';
				FR.UI.contextMenu.target = FR.UI.gridPanel.getSelectedFiles();
				FR.contextMenuActions.remove(FR.UI.contextMenu, FR.UI.contextActions.remove);
			}
		}),
		more: new Ext.Action({
			iconCls: 'fa fa-ellipsis-v fa-fw', hidden: true,
			listeners: {'afterrender': FR.UI.tooltip(FR.T('More options'))},
			handler: function() {FR.UI.gridPanel.showContextMenu();return false}
		}),
		moreSep: new Ext.Toolbar.Separator({hidden: true}),
		info: new Ext.Button({
			iconCls: 'fa fa-info-circle fa-fw', cls: 'fr-btn-info', enableToggle: true, pressed: true,
			listeners: {'afterrender': FR.UI.tooltip(FR.T((User.perms.file_history)?'Details and activity':'Details'))},
			toggleHandler: function(btn, toggled) {
				if (toggled) {
					FR.UI.infoPanel.customExpand();
				} else {
					FR.UI.infoPanel.customCollapse();
				}
			}
		}),
		toggleViewList: new Ext.Action({
			iconCls: FR.UI.getViewIconCls(), cls: 'fr-btn-toggle-view',
			handler: function() {FR.UI.gridPanel.toggleView();}
		}),
		profileSettings: new Ext.Action({
			text: FR.T('Account settings'), iconCls: 'fa-user', hidden: !User.perms.edit_profile,
			handler: function() {FR.actions.openAccountSettings();}
		})
	};

	var logoHTML = '';
	if (Settings.ui_user_logo.length > 0) {
		logoHTML = FR.UI.getImageLogo(Settings.ui_user_logo);
	} else {
		if (Settings.ui_title_logo) {
			logoHTML = FR.UI.getTextLogo(Settings.title);
		} else if (Settings.ui_logo_url) {
			logoHTML = FR.UI.getImageLogo(Settings.ui_logo_url);
		}
	}
	FR.UI.actions.logo = new Ext.Toolbar.TextItem({text: logoHTML});

	var newBtn = {
		text: FR.T('New'), cls: 'fr-btn-new', width: 112,
		hidden: !User.perms.upload
	};
	newBtn.menu = [
		FR.UI.actions.createNewFolder,
		FR.UI.actions.fileRequest,
		'-',
		{text: FR.T('File upload'), iconCls: 'fa-upload', handler: function() {FR.UI.showUploadForm('files');}},
		{text: FR.T('Folder upload'), iconCls: 'fa-upload', handler: function() {FR.UI.showUploadForm('folder');}}
	];
	if (createNewFileItems.length > 0) {
		newBtn.menu.push('-');
		newBtn.menu.push(createNewFileItems);
	}
	FR.UI.actions.newItem = new Ext.Button(newBtn);


	/* Header toolbar */
	FR.UI.headerTBar = new Ext.Toolbar({
		height: 60, plain: true, cls: 'headerTbar noselect',
		items: [
			FR.UI.actions.logo,
			FR.UI.actions.searchField,
			FR.UI.actions.searchOpts,
			'->',
			FR.UI.actions.weblink,
			FR.UI.actions.shareWithUsers,
			FR.UI.actions.preview,
			FR.UI.actions.remove,
			FR.UI.actions.more,
			FR.UI.actions.moreSep,
			FR.UI.actions.toggleViewList,
			FR.UI.actions.info,
			FR.UI.actions.cpanel,
			FR.UI.actions.help,
			new Ext.Button({
				cls:'fr-btn-user',
				listeners: {'afterrender': FR.UI.tooltip(User.fname)},
				text: '<img src="'+FR.baseURL+'/a/?uid='+User.id+'" id="avatar" />',
				menu: [
					FR.UI.actions.profileSettings,
					FR.UI.actions.logout
				]
			})
		]
	});


	FR.UI.contextActions = {};
	var a = FR.UI.contextActions;
	a.newOpt = new FR.ContextAction({
		text: FR.T('New'), requires: ['empty', 'create'], menu: newBtn.menu
	});
	a.newFolder = new FR.ContextAction({
		text: FR.T('New sub-folder'), iconCls: 'fa-folder', action: 'newFolder', requires: ['non-empty', 'in-tree', 'create']
	});
	a.sep0 = new Ext.menu.Separator({requires: [function(cm) {return cm.countVisible;}]});
	a.refresh = new FR.ContextAction({
		text: FR.T('Refresh'), iconCls: 'fa-refresh', overflowText: FR.T('Refresh'), action: 'refresh', requires: ['empty']
	});
	a.selectAll = new FR.ContextAction({
		text: FR.T('Select All'), iconCls: 'fa-check-square-o', action: 'selectAll', requires: ['empty', function() {return FR.UI.gridPanel.store.getCount();}]
	});
	a.sortItems = new FR.ContextAction({
		text: FR.T('Sort'), iconCls: 'fa-sort', action: 'sortItems', requires: ['empty',
			function() {return (['thumbnails', 'large'].indexOf(FR.UI.gridPanel.viewMode) != -1);},
			function() {return FR.UI.gridPanel.store.getCount();}
		]
	});
	a.locate = new FR.ContextAction({
		text: FR.T('Locate'), iconCls: 'fa-crosshairs', action: 'locate', requires: ['non-empty', 'single', 'in-grid', {sections:['starred', 'webLinked', 'search', 'photos']}]
	});
	a.sepLocate = new Ext.menu.Separator({requires: ['non-empty', 'single', 'in-grid', function(cm) {return cm.countVisible;}]});
	a.download = new FR.ContextAction({
		text: FR.T('Download'), iconCls: 'fa-download',
		action: 'download', requires: ['non-empty', 'section-not-trash', 'download', function(cm) {
			if (cm.location == 'tree') {
				return User.perms.download_folders;
			} else {
				return !(cm.target.length == 1 && cm.target[0].isFolder && !User.perms.download_folders);
			}
		}]
	});
	a.preview= new FR.ContextAction({
		text: FR.T('Preview'), iconCls: 'fa-eye',
		action: 'preview', requires: ['single', 'file', 'section-not-trash', 'download']
	});



	var openWithItems = [];
	Ext.each(FR.customActions, function(ca) {
		var a = {
			text: ca.title, icon: ca.icon, iconCls: ca.iconCls, requires: [],
			handler: function() {return FR.actions.customAction(ca);}
		};
		if (!ca.multiple) {a.requires.push('single');}
		if (ca.onlyMultiple) {a.requires.push('multiple');}
		if (!ca.folder) {a.requires.push('file');}
		if (ca.extensions) {
			a.requires.push(function(cm, opt) {
				//var fileType = cm.target[0].filetype;
				return (opt.settings.extensions.indexOf(FR.utils.getFileExtension(cm.target[0].filename)) != -1);
			});
			if (ca.replaceDoubleClickAction) {
				Ext.each(ca.extensions, function(ext) {
					FR.ext[ext] = ca.actionName;
				});
			}
		}
		if (ca.useWith) {
			a.requires.push(function(cm, opt) {
				return (opt.settings.useWith.indexOf(cm.target[0].filetype) != -1);
			});
		}
		if (ca.requiredUserPerms) {
			a.requires.push(function(cm, opt) {
				var show = true;
				Ext.each(opt.settings.requiredUserPerms, function (perm) {
					if (!User.perms[perm] || ((FR.currentFolderPerms && !FR.currentFolderPerms[perm]))) {
						show = false;
						return false;
					}
				});
				return show;
			});
		}
		FR.UI.contextActions[ca.actionName] = new FR.ContextAction(a);
		FR.UI.contextActions[ca.actionName].settings = ca;
		openWithItems.push(FR.UI.contextActions[ca.actionName]);
	});
	a.openWith = new FR.ContextAction({
		text: FR.T('Open with..'), menu: {items: openWithItems}, requires: ['download', 'file', 'section-not-trash', function() {
			if (FR.currentSection == 'sharedFolder') {return FR.currentFolderPerms.download;}
			return true;
		}]
	});

	a.sep1= new Ext.menu.Separator({requires: ['non-empty', 'download', 'not-homefolder', 'section-not-trash', function(cm) {return cm.countVisible;}]});
	a.comment= new FR.ContextAction({
		text: FR.T('Comment'), iconCls: 'fa-comments-o',
		action: 'comment', requires: ['single', 'file', 'section-not-trash', function() {
			if (!User.perms.read_comments) {return false;}
			if (FR.currentSection == 'sharedFolder') {return FR.currentFolderPerms.read_comments;}
			return true;
		}]
	});
	a.label= new FR.ContextAction({
		text: FR.T('Label'), iconCls: 'fa-tag',	menu: {items: labels}, requires: ['file', 'section-not-trash',
		function() {
			if (!User.perms.read_comments || !User.perms.write_comments) {return false;}
			if (FR.currentSection == 'sharedFolder') {return FR.currentFolderPerms.comment;}
			return true;
		}]
	});
	a.addStar= new FR.ContextAction({
		text: FR.T('Add star'), iconCls: 'fa-star', action: 'addStar',
		requires: [
			'non-empty', 'section-not-trash', 'not-homefolder',
			function() {return !User.perms.read_only;},
			function(cm) {
				if (cm.location == 'grid') {
					var countStarred = 0;
					Ext.each(cm.target, function (t) {if (t.star) {countStarred++;}});
					if (countStarred == 0 || (countStarred > 0 && countStarred < cm.target.length)) {
						return true;
					}
				} else {
					var t = cm.target;
					if (t.section == 'myfiles' || t.section == 'sharedFolder') {
						return (!t.custom || (t.custom && !t.custom.star));
					}
				}
			}
		]
	});
	a.removeStar= new FR.ContextAction({
		text: FR.T('Remove star'), iconCls: 'fa-star-o', action: 'removeStar',
		requires: [
			'non-empty', 'section-not-trash',
			function() {return !User.perms.read_only;},
			function(cm) {
				if (cm.location == 'grid') {
					var countStarred = 0;
					Ext.each(cm.target, function (t) {if (t.star) {countStarred++;}});
					if (countStarred > 0) {
						return true;
					}
				} else {
					var t = cm.target;
					if (t.section == 'myfiles' || t.section == 'sharedFolder') {
						return (t.custom && t.custom.star);
					}
				}
			}
		]
	});
	a.sep2= new Ext.menu.Separator({requires: ['non-empty', 'not-homefolder', 'section-not-trash',
		function(cm) {return cm.countVisible;},
		function(cm) {return !User.perms.read_only;}
	]});
	a.metadata= new FR.ContextAction({
		text: FR.T('Metadata'), action: 'metadata', requires: ['single', 'file', function() {return (User.perms.metadata);}]
	});
	a.rename= new FR.ContextAction({
		text: FR.T('Rename'), action: 'rename',
		requires: ['non-empty', 'section-not-trash', 'not-homefolder', 'single', 'alter']
	});
	a.restore= new FR.ContextAction({
		text: FR.T('Restore'), iconCls: 'fa-thumbs-o-up',
		action: 'restore', requires: ['non-empty', 'alter', {sections: ['trash']}]
	});
	a.sep3= new Ext.menu.Separator({requires: ['non-empty', 'not-homefolder', 'alter',
		function(cm) {return cm.countVisible;}
	]});
	a.remove= new FR.ContextAction({
		text: FR.T('Remove'), iconCls: 'fa-trash',
		action: 'remove', requires: ['non-empty', 'not-homefolder', 'alter']
	});
	a.emptyTrash= new FR.ContextAction({
		text: FR.T('Empty trash'), iconCls: 'fa-trash-o', action: 'emptyTrash', requires: [{sections: ['trash']}]
	});
	a.shareWithUsers= new FR.ContextAction({
		text: FR.T('With users'), iconCls: 'fa-user-plus', action: 'shareWithUsers', requires: [
			'single', 'folder', 'non-empty', 'section-myfiles', function (){return User.perms.share;}
		]
	});
	a.weblink= new FR.ContextAction({
		text: FR.T('Web link'), iconCls: 'fa-link', action: 'weblink', requires: ['single', function (){return User.perms.weblink;}]
	});
	a.email= new FR.ContextAction({
		text: FR.T('E-mail'), iconCls: 'fa-envelope-o', action: 'email', requires: [function (){return User.perms.email;}]
	});
	a.notifications= new FR.ContextAction({
		text: FR.T('Notifications'), iconCls: 'fa-bell', requires: ['single', 'folder', 'non-empty', function (){return Settings.allow_folder_notifications;}],
		menu: {
			listeners: {
				'beforeshow': function() {
					var nfo;
					var cRead = 0;
					var cWrite = 0;
					var nW = this.items.items[0];
					var nR = this.items.items[1];
					var cm = FR.UI.contextMenu;
					if (cm.location == 'tree') {
						var tn = cm.target;
						if (tn.custom && tn.custom.notInfo) {
							nfo = tn.custom.notInfo;
						}
					} else {
						nfo = cm.target[0].notInfo;
					}
					if (nfo) {
						cRead = nfo.r;
						cWrite = nfo.w;
					}
					nW.setChecked(cWrite, true);
					nR.setChecked(cRead, true);
				}
			},
			items: [
				{xtype: 'menucheckitem', text: FR.T('Upload, Delete, Rename, etc.'), hideOnClick: false, checkHandler: function() {
					FR.contextMenuActions.saveNotif([this.ownerCt.items.items[0].checked, this.ownerCt.items.items[1].checked]);
				}},
				{xtype: 'menucheckitem', text: FR.T('Download, Preview, Copy, etc.'), hideOnClick: false, checkHandler: function() {
					FR.contextMenuActions.saveNotif([this.ownerCt.items.items[0].checked, this.ownerCt.items.items[1].checked]);
				}}
			]
		}
	});
	a.props= new FR.ContextAction({
		text: FR.T('Properties'), iconCls: 'fa-cog', action: 'props', requires: ['single', 'folder', 'non-empty', function (){return User.isAdmin;}]
	});
	a.copy= new FR.ContextAction({
		text: FR.T('Copy'), action: 'copy', requires: ['non-empty', 'section-not-trash', 'not-homefolder', 'download']
	});
	a.pasteCopied= new FR.ContextAction({
		text: FR.T('Paste copied'), action: 'pasteCopied', requires: ['single', 'folder', 'create', function() {return FR.copyingPaths.length;}]
	});
	a.pasteCopied2= new FR.ContextAction({
		text: FR.T('Paste copied'), action: 'pasteCopied', requires: ['empty', 'create', function() {return FR.copyingPaths.length;}]
	});
	a.alog= new FR.ContextAction({
		text: FR.T('Activity log'), iconCls: 'fa-archive', action: 'activityLog', requires: [
			'file', 'single',
			function() {
				if (!User.perms.file_history) {return false;}
				var s = FR.currentSection;
				if (s == 'myfiles') {
					return true;
				} else if (s == 'sharedFolder') {
					return Settings.filelog_for_shares;
				}
			}
		]
	});
	a.zip= new FR.ContextAction({
		text: FR.T('Add to zip'), iconCls: 'fa-file-zip-o', action: 'zip', requires: ['not-homefolder', 'create', 'download', function(cm){
			if (cm.target.length == 1 && cm.target[0].filetype == 'arch') {return false;}
			if (cm.location == 'tree') {
				return User.perms.download_folders;
			} else {
				return !(cm.target.length == 1 && cm.target[0].isFolder && !User.perms.download_folders);
			}
			return true;
		}]
	});
	a.extract= new FR.ContextAction({
		text: FR.T('Extract archive'), iconCls: 'fa-file-zip-o', action: 'extract', requires: ['file', 'single', function(cm){return (cm.target[0].filetype == 'arch');}]
	});
	a.unweblink= new FR.ContextAction({
		text: FR.T('Remove Web Links'), iconCls: 'fa-unlink', action: 'unweblink', requires: ['non-empty', 'in-grid',
			function(cm) {return (FR.currentSection == 'webLinked');}
		]
	});
	a.sepUnWebLink = new Ext.menu.Separator({requires: ['non-empty', 'in-grid', {sections: ['webLinked']}, function(cm) {return cm.countVisible;}]});
	a.lock= new FR.ContextAction({
		text: FR.T('Lock'),	iconCls: 'fa-lock', action: 'lock', requires: ['alter', function() {return !Settings.free_mode;}]
	});
	a.unlock= new FR.ContextAction({
		text: FR.T('Unlock'), iconCls: 'fa-unlock-alt', action: 'unlock', requires: ['alter', function() {return !Settings.free_mode;}]
	});



	a.versioning = new FR.ContextAction({
		text: FR.T('Versioning'), iconCls: 'fa-history', requires: ['single', 'file', function(){return (!Settings.disable_versioning);}],
		menu: new Ext.menu.Menu({
			id: 'myfiles-contextmenu-versioning',
			items: [
				new Ext.menu.Item({
					text: FR.T('Previous Versions'),
					handler: function() {return FR.actions.openVersions();}
				}),
				a.lock, a.unlock
			]
		})
	});


	a.more = new FR.ContextAction({
		text: FR.T('More options'), iconCls: 'fa-ellipsis-v', requires: ['non-empty', {sections:['myfiles', 'sharedFolder', 'starred', 'webLinked']},
		function(cm) {
			if (cm.location == 'tree') {
				return (['starred', 'webLinked', 'search'].indexOf(cm.target.section) == -1);
			}
			return true;
		}],
		menu: [a.zip, a.extract, a.versioning, a.alog, a.metadata, a.notifications, a.props]
	});
	a.share = new FR.ContextAction({
		text: FR.T('Share'), iconCls: 'fa-share-alt', requires: ['non-empty', 'not-homefolder', 'download', {sections: ['myfiles', 'sharedFolder', 'starred', 'search', 'webLinked']},
			function (){return (User.perms.share || User.perms.weblink);},
			function(cm) {
			if (cm.location == 'tree') {
				if (!cm.target.perms || cm.target.perms.share) {return true;}
			} else {
				return (!FR.currentFolderPerms || FR.currentFolderPerms.share);
			}
		}],
		menu: [a.weblink, a.email, a.shareWithUsers/*, a.btsync*/]
	});

	FR.UI.contextMenu = new FR.components.ContextMenu({
		items: [a.pasteCopied2, a.newOpt, a.newFolder, a.sep0, a.refresh, a.selectAll, a.sortItems, a.locate, a.sepLocate, a.unweblink, a.sepUnWebLink, a.download, a.preview, a.openWith, a.sep1, a.share, a.comment, a.label, a.addStar, a.removeStar, a.sep2, a.more, a.pasteCopied, a.copy, a.rename, a.restore, a.sep3, a.remove, a.emptyTrash]
	});

};

FR.components.ContextMenu = Ext.extend(Ext.menu.Menu, {
	target: false, location: false, countVisible: 0, reqChecks: new Ext.util.MixedCollection(),
	event: function(opts) {
		Ext.apply(this, opts);
		this.prepareItems();
		if (this.countVisible > 0) {
			this.showAtCursor();
		}
	},
	prepareItems: function() {
		this.countVisible = 0;
		this.reqChecks.clear();
		Ext.iterate(FR.UI.contextActions, function(key, item) {
			if (item.initialConfig.requires) {
				var meets = 0;
				Ext.each(item.initialConfig.requires, function (req) {
					var rs = false;
					var prevReqCheck = false;
					if (typeof req != 'function' && typeof req != 'object') {
						prevReqCheck = this.reqChecks.get(req);
					}
					if (prevReqCheck) {
						rs = prevReqCheck.met;
					}  else if (req == 'in-grid') {
						rs = (this.location == 'grid');
					} else if (req == 'in-tree') {
						rs = (this.location == 'tree');
					} else if (req == 'empty') {
						rs = (this.target.length == 0);
					} else if (req == 'non-empty') {
						rs = (this.location == 'tree' || this.target.length > 0);
					} else if (req == 'single') {
						rs = (this.location == 'tree' || this.target.length == 1);
					} else if (req == 'multiple') {
						rs = (this.target.length > 1);
					} else if (req == 'file') {
						rs = (this.location == 'grid' && this.target[0] && !this.target[0].isFolder);
					} else if (req == 'folder') {
						rs = (this.location == 'tree' || this.target[0].isFolder);
					} else if (req == 'section-myfiles') {
						if (this.location == 'grid') {
							var s = FR.currentSection;
						} else {
							var s = this.target.section;
						}
						rs = (s == 'myfiles');
					} else if (req == 'not-homefolder') {
						rs = (this.location == 'grid' || (this.location == 'tree' && !this.target.homefolder));
					} else if (req == 'section-not-trash') {
						rs = ((this.location == 'grid' && FR.currentSection != 'trash') || (this.location == 'tree' && this.target.section != 'trash'));
					} else if (req == 'section-myfiles-sharedFolder') {
						if (this.location == 'grid') {
							var s = FR.currentSection;
						} else {
							var s = this.target.section;
						}
						if (['myfiles', 'sharedFolder'].indexOf(s) != -1) {rs = true;}
					} else if (req == 'create') {
						if (User.perms.upload) {
							if (this.location == 'tree') {
								var t = this.target;
								if (['myfiles', 'sharedFolder'].indexOf(t.section) != -1) {
									if (!t.perms || (t.perms && t.perms.upload)) {rs = true;}
								}
							} else {
								if (FR.currentSection == 'myfiles' || FR.currentSection == 'sharedFolder') {
									rs = !(FR.currentFolderPerms && !FR.currentFolderPerms.upload);
								}
							}
						}
					} else if (req == 'download') {
						if (User.perms.download) {
							if (this.location == 'tree') {
								if (this.target.section == 'myfiles') {
									rs = true;
								} else if (this.target.section == 'sharedFolder') {
									if (this.target.perms.download) {rs = true;}
								}
							} else {
								if (['myfiles', 'starred', 'webLinked', 'search', 'photos'].indexOf(FR.currentSection) != -1) {
									rs = true;
								} else if (FR.currentSection == 'sharedFolder') {
									if (FR.currentFolderPerms.download) {rs = true;}
								}
							}
						}
					} else if (req == 'alter') {
						if (!User.perms.read_only) {
							if (this.location == 'tree') {
								var t = this.target;
								if (t.section == 'sharedFolder') {
									if (t.perms.alter) {rs = true;}
								} else if (t.section == 'myfiles') {rs = true;}
							} else {
								rs = !(FR.currentFolderPerms && !FR.currentFolderPerms.alter);
							}
						}
					} else if (typeof req == 'object') {
						if (req.sections) {
							if (this.location == 'grid') {
								var s = FR.currentSection;
							} else {
								var s = this.target.section;
							}
							if (req.sections.indexOf(s) != -1) {rs = true;}
						}
					}  else if (typeof req == 'function') {
						rs = req(this, item);
					} else {
						rs = true;
					}
					if (typeof req != 'function' && typeof req != 'object') {
						this.reqChecks.add(req, {met: rs});
					}
					if (rs) {
						meets++;
					} else {
						return false;
					}
				}, this);
				if (item.initialConfig.requires.length == meets) {
					item.show();
					this.countVisible++;
				} else {
					item.hide();
				}
			}
		}, this);
	},
	showAtCursor: function() {
		this.showAt([FR.UI.xy[0]+3, FR.UI.xy[1]+3]);
	}
});
FR.ContextAction = Ext.extend(Ext.Action, {
	constructor: function(config){
		if (config.action) {
			config.handler = function () {
				return FR.contextMenuActions[this.initialConfig.action](FR.UI.contextMenu, this);
			};
		} else {if (!config.handler){config.handler = function () {return false;}}}
		this.initialConfig = config;
		this.itemId = config.itemId = (config.itemId || config.id || Ext.id());
		this.items = [];
	}
});