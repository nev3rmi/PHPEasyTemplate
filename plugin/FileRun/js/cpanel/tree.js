FR.initTree = function () {
	var displayed = [];
	var opts = {
		usersOnline: {
			text: FR.T('Users online'), hidden: !FR.system.enableUsersOnline,
			icon: FR.iconURL+'online.png', leaf: true,
			appURL: FR.URLRoot+'/?module=cpanel&section=tools&page=users_online'
		},
		actLogs: {
			text: FR.T('Activity logs'),
			id: 'alogs', leaf: true,
			icon: FR.iconURL+'report.png',
			module: FR.modules.logs
		},
		uTools: {
			text: FR.T('Tools'), id: 'tools', expanded: true, cls: 'adminSection',
			icon: FR.iconURL+'wrench.png',
			children: [
				{
					text: FR.T('Web Links'),
					icon: FR.iconURL+'link.png', leaf: true,
					module: FR.modules.weblinks
				},
				{
					text: FR.T('File space quota usage'), hidden: FR.system.isFree,
					icon: FR.iconURL+'chart_bar.png', leaf: true,
					appURL: FR.URLRoot+'/?module=cpanel&section=tools&page=space_quota'
				},
				{
					text: FR.T('Traffic quota usage'), hidden: FR.system.isFree,
					icon: FR.iconURL+'chart_bar.png', leaf: true,
					appURL: FR.URLRoot+'/?module=cpanel&section=tools&page=traffic_quota'
				},
				{
					text: FR.T('Import users'), hidden: (FR.system.isFree || !FR.user.isSuperuser),
					icon: FR.iconURL+'import.png', leaf: true,
					appURL: FR.URLRoot+'/?module=cpanel&section=tools&page=import_users'
				},
				{
					text: FR.T('Export users'), hidden: (FR.system.isFree || !FR.user.isSuperuser),
					icon: FR.iconURL+'export.png', leaf: true,
					appURL: FR.URLRoot+'/?module=cpanel&section=tools&page=export_users'
				}
			]
		},
		users: {
			text: FR.T('Users'), id: 'users',
			icon: FR.iconURL+'user.png',
			hidden: ((!FR.user.isAdmin && !FR.user.isIndep) || !FR.user.perms.adminUsers),
			module: FR.modules.users, expanded: true,
			children: [
				{
					text: FR.T('Groups'),
					icon: FR.iconURL+'group.png', leaf: true,
					module: FR.modules.groups, hidden: (FR.system.isFree || !FR.user.perms.adminUsers)
				},
				{
					text: FR.T('Roles'),
					icon: FR.iconURL+'role.png', leaf: true,
					module: FR.modules.roles, hidden: (FR.system.isFree || !FR.user.perms.adminRoles)
				}
			]
		},
		sysConf: {
			text: FR.T('System configuration'), expanded: true, id: 'sysconf',
			cls: 'sysConfMenuItem', hidden: !(FR.user.isSuperuser || FR.user.perms.adminNotif  || FR.user.perms.adminMetadata),
			children: [
				{
					text: FR.T('Interface'), expanded: true, hidden: !FR.user.isSuperuser,
					icon: FR.iconURL+'display.png', cls: 'adminSection',
					children: [
						{
							text: FR.T('Options'), leaf: true,
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=interface'
						}
					]
				},
				{
					text: FR.T('E-mail'), cls: 'adminSection', hidden: !(FR.user.isSuperuser || FR.user.perms.adminNotif),
					icon: FR.iconURL+'email.png',  expanded: true,
					children: [
						{
							text: FR.T('Settings'), leaf: true, hidden: !FR.user.isSuperuser,
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=email'
						},
						{
							text: FR.T('Notifications'), leaf: true,
							module: FR.modules.notifications
						},
						{
							text: FR.T('Logs'), leaf: true, hidden: !FR.user.isSuperuser,
							module: FR.modules.notif_logs
						}
					]
				},
				{
					text: FR.T('Files'),  expanded: true, cls: 'adminSection', hidden: !(FR.user.isSuperuser || FR.user.perms.adminMetadata),
					icon: FR.iconURL+'files.png',
					children: [
						{
							text: FR.T('Image preview'), leaf: true, hidden: !FR.user.isSuperuser,
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=image_preview'
						},
						{
							text: FR.T('Plugins'), expanded: true, hidden: !FR.user.isSuperuser,
							module: FR.modules.openWith,
							children: [
								{
									text: FR.T('Defaults'), leaf: true,
									module: FR.modules.defaultOpenWith
								}
							]
						},

						{
							text: FR.T('Indexing'), leaf: true, hidden: (!FR.user.isSuperuser || FR.system.isFree),
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=file_search'
						},
						{
							text: FR.T('Misc options'), leaf: true, hidden: !FR.user.isSuperuser,
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=files'
						},
						{
							text: FR.T('Metadata'), expanded: true, id: 'metadata',
							children: [
								{
									text: FR.T('File types'), leaf: true,
									module: FR.modules.metadata_filetypes
								},
								{
									text: FR.T('Field sets'), leaf: true,
									module: FR.modules.metadata_fieldsets
								}
							]
						}
					]
				},
				{
					text: FR.T('Security'),  expanded: true, cls: 'adminSection', hidden: !FR.user.isSuperuser,
					icon: FR.iconURL+'lock.png',
					children: [
						{
							text: FR.system.isFree ? FR.T('User login') : FR.T('User login and registration'), leaf: true,
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=login_registration'
						},
						{
							text: FR.T('Password policy'), leaf: true, hidden: FR.system.isFree,
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=passwords'
						},
						{
							text: FR.T('API (OAuth2)'), id: 'oauth', expanded: true,
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=oauth',
							children: [
								{
									text: FR.T('Clients'), leaf: true,
									module: FR.modules.oauth2_clients
								}
							]
						}
					]
				},
				{
					text: FR.T('More'), expanded: true, cls: 'adminSection', hidden: !FR.user.isSuperuser,
					icon: FR.iconURL+'cog.png',
					children: [
						{
							text: FR.T('Misc options'), leaf: true,
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=misc'
						},
						{
							text: FR.T('Third party services'), leaf: true,
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=third_party'
						},
						{
							text: FR.T('Software update'), leaf: true,
							appURL: FR.URLRoot+'/?module=software_update&section=cpanel'
						},
						{
							text: FR.T('Software licensing'), leaf: true,
							appURL: FR.URLRoot+'/?module=cpanel&section=settings&page=license'
						}
					]
				}
			]
		}
	};
	if (FR.user.isIndep || FR.user.perms.adminUsers) {
		displayed.push(opts.users);
		if (FR.user.perms.adminLogs) {
			opts.uTools.children.unshift(opts.actLogs);
			if (FR.system.enableUsersOnline) {
				opts.uTools.children.unshift(opts.usersOnline);
			}
		}
		opts.users.children.push(opts.uTools);
	} else {
		if (FR.user.perms.adminLogs) {
			if (FR.system.enableUsersOnline) {
				displayed.push(opts.usersOnline);
			}
			displayed.push(opts.actLogs);
		}
	}
	displayed.push(opts.sysConf);

	this.tree = {
		init: function() {
			this.panel = new Ext.tree.TreePanel({
				autoScroll: true, containerScroll: true, rootVisible: false, trackMouseOver: false,
				listeners: {
					'contextmenu': function (tree, e) {e.stopEvent();return false;},
					'beforecollapsenode': function() {return false;}
				},
				root: {
					expanded: true,
					id: 'root',
					children: displayed
				}
			});
			this.panel.getSelectionModel().on('selectionchange', function(selectionModel, treeNode) {
				FR.tsel = treeNode.attributes;
				if (FR.tsel.module) {
					if (FR.tsel.module.type == 'grid') {
						Ext.getCmp('cardDisplayArea').getLayout().setActiveItem(0);
						FR.grid.loadModule(FR.tsel.module);
					} else {
						if (FR.tsel.module.activeItem) {
							Ext.getCmp('cardDisplayArea').getLayout().setActiveItem(FR.tsel.module.activeItem);
						}
					}
				} else {
					if (FR.tsel.appURL) {
						Ext.getCmp('cardDisplayArea').getLayout().setActiveItem(1);
						Ext.getCmp('appTab').removeAll(true);
						FR.tempPanel.load({
							url: FR.tsel.appURL,
							nocache: true,
							scripts: true
						});
					}
				}
			});
			this.panel.getSelectionModel().on('beforeselect', function(selectionModel, treeNode) {
				if (treeNode.attributes.cls == 'adminSection' || ['sysconf', 'tools', 'metadata'].indexOf(treeNode.attributes.id) != -1) {
					if (treeNode.attributes.id == 'metadata') {
						treeNode.firstChild.select();
					}
					return false;
				}
			});
			this.panel.getRootNode().on('load', function (node) {
				window.setTimeout(function () {
					if (FR.user.perms.adminUsers) {
						FR.tree.panel.getNodeById('users').select();
					}
				}, 200);
			});
		}
	};
	this.tree.init();
}