FR.addRole = {};
FR.addRole.formPanel = new FR.components.editForm({
	title: FR.T('Add Role'),
	items: {
		xtype: 'tabpanel',
		activeTab: 0, border: false, deferredRender: false,
		defaults: {autoScroll: true, bodyStyle:'padding:10px', listeners: {'render': function() {this.doLayout(false, true);}}},
		listeners: {'render': function() {if (!FR.user.isSuperuser) {this.hideTabStripItem(1);}}},
		items: [
			{
				title: FR.T('Permissions'),
				items: [
					{
						xtype: 'fieldset',
						title: FR.T('Basic Information'),
						width: 500,
						defaults: {width: 200},
						items: [
							{
								xtype: 'textfield',
								fieldLabel: FR.T('Role name'),
								name: 'name',
								value: ''
							},{
								xtype: 'textarea',
								fieldLabel: FR.T('Description'),
								name: 'description',
								value: ''
							}
						]
					},
					{
						ref: 'permsFieldset',
						xtype: 'fieldset',
						title: FR.T('Permissions'),
						width: 500,
						defaults: {xtype: 'checkbox'},
						items: [
							{
								xtype: 'textfield', hidden: !FR.userCanSetHomeFolder,
								fieldLabel: FR.T('Home folder<br> template'),
								name: 'homefolder', width: 300,
								helpText: (FR.user.isAdmin && !FR.user.isIndep) ?
		FR.T('Type the path to a folder on your server. This is the user\'s personal working space.<br><br>Examples: 	c:/users/group_name<br>/home/users/group_name')+'<br>'+
		FR.T('{USERNAME} will be automatically replaced with each user\'s login name.')+'<br>'+
		FR.T('{NAME} will be automatically replaced with this user\'s name.')+'<br>'+
		FR.T('{EMAIL} will be automatically replaced with this user\'s e-mail.')+'<br>'+
		FR.T('{COMPANY} will be automatically replaced with this user\'s company name.')
		: FR.T('This folder will be automatically created in your home folder, if it doesn\'t exists already.'),
								value: FR.user.isIndep ? '' : FR.adminHomeFolderPath
							},
							{
								boxLabel: FR.T('Automatically create the folder if it doesn\'t exists.'), value: 1,
								name: 'create_folder', checked: true, hidden: !FR.userCanSetHomeFolder
							},
							{xtype: 'displayfield', value: ''},
							{
								boxLabel: FR.T('User can upload files'), value: 1,
								name: 'upload', checked: true
							},
							{
								boxLabel: FR.T('User can download files'), value: 1,
								name: 'download', checked: true
							},
							{
								boxLabel: FR.T('User can zip and download folders'), value: 1,
								name: 'download_folders', checked: true
							},
							{
								boxLabel: FR.T('User can read file comments'), value: 1,
								name: 'read_comments', checked: true
							},
							{
								boxLabel: FR.T('User can write file comments'), value: 1,
								name: 'write_comments', checked: true
							},
							{
								boxLabel: FR.T('User can publish files as "Web Links"'),
								name: 'weblink', checked: true
							},/*
							{
								boxLabel: FR.T('User can sync folders with BitTorrent Sync'),
								name: 'btsync', checked: true
							},*/
							{
								boxLabel: FR.T('User can email files as attachments'), value: 1,
								name: 'email', checked: true
							},
							{
								boxLabel: FR.T('User can share folders'), value: 1,
								name: 'share', checked: true
							},
							{
								boxLabel: FR.T('User can access metadata'), value: 1,
								name: 'metadata', checked: true
							},
							{
								boxLabel: FR.T('User can access the files\' activity logs'), value: 1,
								hidden: FR.settings.disable_file_history,
								name: 'file_history', checked: true
							},
							{
								boxLabel: FR.T('User can change the password'), value: 1,
								name: 'change_pass', checked: true
							},
							{
								boxLabel: FR.T('User can change personal information'), value: 1,
								name: 'edit_profile', checked: true
							},
							{xtype: 'displayfield', value: ''},
							{
								boxLabel: FR.T('User cannot move, rename delete, etc..'), value: 1,
								name: 'readonly', checked: false
							},
							{xtype: 'displayfield', value: ''},
							{
								xtype: 'userslistfield', allowAll: true, showSelf: true,
								allItemsText: FR.T('[All users]'), tcfg: {height: 150, width: 230},
								name: 'users_may_see', value: '-ALL-',
								helpText: FR.T('Use this setting to allow the user to access folders shared by other users.'),
								fieldLabel: FR.T('Can see and exchange files with')
							}
						]
					},
					{
						xtype: 'fieldset',
						title: FR.T('Quotas'),
						width: 500, labelWidth: 150,
						defaultType: 'textfield', defaults: {width: 60},
						items: [
							{
								
								fieldLabel: FR.T('Space quota'),
								name: 'space_quota_max',
								helpText: FR.T('The values are in megabytes. The value of 0 disables the limitation.'),
								value: ''
							},
							{
								fieldLabel: FR.T('Upload max file size'),
								name: 'upload_max_size',
								helpText: FR.T('The values are in megabytes. The value of 0 disables the limitation.'),
								value: ''
							},
							{
								fieldLabel: FR.T('Traffic quota'),
								name: 'traffic_quota_max', 
								value: ''
							}
						]
					}
				]
			},
			{
				title: FR.T('Admin'),
				ref: 'adminTab',
				items: [
					{
						xtype: 'fieldset',
						width: 500,
						items: [
							{
								xtype: 'radiogroup',
								fieldLabel: FR.T('Admin'),
								columns: 1,
								items: [
									{boxLabel: FR.T('No'), name: 'admin_type', inputValue: '', value: '', checked: true},
									{boxLabel: FR.T('Yes'), name: 'admin_type', inputValue: 'simple', value: 'simple', checked: false},
									{boxLabel: FR.T('Yes, Independent'), name: 'admin_type', inputValue: 'indep', value: 'indep', checked: false, helpText: FR.T('- Can only see and manage users, groups, etc. created by himself.')+'<br>'+FR.T('- He can assign space quotas within a total of his own space quota.')}
								],
								listeners: {
									'change': function(f, checked) {
										f.ownerCt.ownerCt.adminPermsFieldset.setVisible((checked.value == 'simple'));
										f.ownerCt.ownerCt.adminHomeFolderFieldset.setVisible((checked.value != ''));
										f.ownerCt.ownerCt.maxUsersFieldset.setVisible((checked.value == 'indep'));
									}
								}
							}
						]
					},
					{
						ref: 'adminPermsFieldset',
						xtype: 'fieldset',
						title: FR.T('Admin permissions'),
						width: 500, hidden: true,
						defaults: {xtype: 'checkbox'},
						items: [
							{
								boxLabel: FR.T('User can create new user accounts.'), inputValue: 1,
								name: 'admin_users', checked: true
							},
							{
								boxLabel: FR.T('User can manage roles.'), inputValue: 1,
								name: 'admin_roles', checked: true
							},
							{
								boxLabel: FR.T('User can manage notifications.'), inputValue: 1,
								name: 'admin_notifications', checked: true
							},
							{
								boxLabel: FR.T('User can access the activity logs.'), inputValue: 1,
								name: 'admin_logs', checked: true
							},
							{
								boxLabel: FR.T('User can change the metadata settings.'), inputValue: 1,
								name: 'admin_metadata', checked: true
							},
							{xtype: 'displayfield', value: ''},
							{xtype: 'userslistfield', only: 'groups', allowAll: true, allItemsText: FR.T('[All groups]'),
								name: 'admin_over', value: '-ALL-',
								tcfg: {title: FR.T('Can manage'),  height: 150, width: 300}
							}
						]
					},
					{
						ref: 'maxUsersFieldset',
						xtype: 'fieldset',
						width: 500, hidden: true,
						items: [
							{
								xtype: 'textfield',
								fieldLabel: FR.T('Max users'),
								name: 'admin_max_users', width: 60,
								helpText: FR.T('This is the maximum number of user accounts this independent admin user can create.')+
								'<br>'+
								FR.T('Setting the value to zero will allow the admin to add an unlimited number of users.'),
								value: ''
							}
						]
					},
					{
						ref: 'adminHomeFolderFieldset',
						xtype: 'fieldset',
						width: 500, hidden: true,
						items: [
							{
								xtype: 'textfield',
								fieldLabel: FR.T('Home folder<br>template path'),
								name: 'admin_homefolder_template', width: 300,
								helpText:
									FR.T('The users created by this administrator will have their home folders set depending on this template.')+'<br>'+
									FR.T('Leave the field empty to allow the admin to set the paths manually.')+'<br><br>'+
									FR.T('Example: /home/users/{USERNAME}')+'<br><br>'+
									FR.T('{USERNAME} will be automatically replaced with each user\'s login name.')+'<br>'+
									FR.T('{ADMUSERNAME} will be automatically replaced with this user\'s login name.')+'<br>'+
									FR.T('{NAME} will be automatically replaced with this user\'s name.')+'<br>'+
									FR.T('{EMAIL} will be automatically replaced with this user\'s e-mail.')+'<br>'+
									FR.T('{COMPANY} will be automatically replaced with this user\'s company name.'),
								value: FR.user.isIndep ? '' : FR.adminHomeFolderPath
							}
						]
					}
				]
			}
		]},
		tbar: [
		{
			text: FR.T('Add Role'),
			iconCls: 'fa fa-fw fa-plus-circle',
			ref: 'saveBtn',
			handler: function() {
				var editForm = this.ownerCt.ownerCt;
				var opts = {
					url: FR.URLRoot+'/?module=user_roles&section=cpanel&page=add&action=add',
					maskText: 'Saving changes...'
				};
				editForm.submitForm(opts);
			}
		}
	]
});
Ext.getCmp('gridTabPanel').add(FR.addRole.formPanel);
FR.addRole.formPanel.show();