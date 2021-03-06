FR.editSettings = {};
FR.editSettings.formPanel = new FR.components.editForm({
	title: FR.T('Google'),
	layout: 'form', bodyStyle: 'padding:10px;',
	autoScroll: true, defaults: {width: 500},
	items: [
		{
			xtype: 'fieldset',
			labelWidth: 1,
			items:[
				{xtype: 'displayfield', hidden: !Ext.isIE},
				{
					xtype: 'checkbox',
					boxLabel: FR.T('Use Gravatar.com for users profile images.'),
					width: 400, value: 1,
					name: 'settings[gravatar_enabled]', checked: parseInt(FR.settings.gravatar_enabled)
				}
			]
		},
		{
			xtype: 'fieldset',
			title: FR.T('Google'),
			items: [
				{
					xtype: 'fieldset', labelWidth: 150,
					title: FR.T('URL Shortener'),
					items: [
						{
							xtype: 'textfield',
							fieldLabel: FR.T('API "browser" key'), width: 280,
							name: 'settings[google_shortener_key]', value: FR.settings.google_shortener_key
						},
						{
							xtype: 'displayfield',
							hideLabel: true,
							style: 'color:gray',
							value: FR.T('Get it from %1').replace('%1', '<a href="https://console.developers.google.com/apis/credentials" target="_blank">Google API Manager</a>')
						}
					]
				},
				{
					xtype: 'fieldset',
					hidden: FR.system.isFree,
					checkboxToggle: {
						tag: 'input',
						type: 'checkbox',
						name: this.checkboxName || this.id + '-checkbox',
						id: 'settings[captcha]'
					},
					checkboxName: 'settings[captcha]',
					title: FR.T('Enable <a href="%1" target="_blank">reCAPTCHA</a>').replace('%1', 'https://www.google.com/recaptcha'),
					animCollapse: true,
					collapsed: !parseInt(FR.settings.captcha),
					labelWidth: 150,
					items: [
						{
							xtype: 'textfield',
							fieldLabel: FR.T('Site key'), width: 280,
							name: 'settings[recaptcha_site_key]', value: FR.settings.recaptcha_site_key
						},
						{
							xtype: 'textfield',
							fieldLabel: FR.T('Secret key'), width: 280,
							name: 'settings[recaptcha_secret_key]', value: FR.settings.recaptcha_secret_key
						},
						{
							xtype: 'displayfield',
							hideLabel: true,
							style: 'color:gray',
							value: FR.T('Get them from %1').replace('%1', '<a href="https://www.google.com/recaptcha/admin" target="_blank"">https://www.google.com/recaptcha/admin</a>')
						}
					]
				}
			]
		},
		{
			xtype: 'fieldset',
			title: FR.T('Pusher.com'), hidden: FR.system.isFree,
			items: [
				{
					xtype: 'fieldset',
					checkboxToggle: {tag: 'input', type: 'checkbox', name: this.checkboxName || this.id + '-checkbox', id: 'settings[pushercom_enable]'},
					checkboxName: 'settings[pushercom_enable]',
					title: FR.T('Enable Pusher.com Integration'), animCollapse: true,
					collapsed: !parseInt(FR.settings.pushercom_enable),
					labelWidth: 100, defaults: {width: 200},
					items: [
						{
							xtype: 'textfield',
							fieldLabel: FR.T('App ID'), width: 100,
							name: 'settings[pushercom_app_id]', value: FR.settings.pushercom_app_id
						},
						{
							xtype: 'textfield',
							fieldLabel: FR.T('App Key'),
							name: 'settings[pushercom_app_key]', value: FR.settings.pushercom_app_key
						},
						{
							xtype: 'textfield',
							fieldLabel: FR.T('App Secret'),
							name: 'settings[pushercom_app_secret]', value: FR.settings.pushercom_app_secret,
							inputType: 'password', listeners: {
								'focus': function() {
									this.getEl().dom.setAttribute('type', 'text');
								},
								'blur': function() {
									this.getEl().dom.setAttribute('type', 'password');
								}
							}
						},
						{
							xtype: 'displayfield',
							hideLabel: true,
							style: 'color:gray',
							value: FR.T('Get them from %1').replace('%1', '<a href="https://pusher.com" target="_blank"">https://pusher.com</a>')
						},
						{
							xtype: 'checkbox', value: 1,
							boxLabel: FR.T('Show online users'), helpText: FR.T('After enabling this option, a section entitled "Users online" will show in the menu next time the control panel is loaded.'),
							name: 'settings[pushercom_show_online]', checked: parseInt(FR.settings.pushercom_show_online)
						}
					]
				}
			]
		}
	],
	tbar: [
		{
			text: FR.T('Save Changes'),
			iconCls: 'fa fa-fw fa-save',
			ref: 'saveBtn',
			handler: function() {
				var editForm = this.ownerCt.ownerCt;
				var params = editForm.form.getFieldValues();
				var extra = {};
				if (!FR.system.isFree) {
					extra['settings[captcha]'] = Ext.get('settings[captcha]').dom.checked ? 1 : 0;
					extra['settings[pushercom_enable]'] = Ext.get('settings[pushercom_enable]').dom.checked ? 1 : 0;
				}
				Ext.apply(params, extra);
				var opts = {
					url: FR.URLRoot+'/?module=cpanel&section=settings&action=save',
					maskText: 'Saving changes...',
					params: params
				};
				editForm.submitForm(opts);
			}
		}
	]
});
Ext.getCmp('appTab').add(FR.editSettings.formPanel);
Ext.getCmp('appTab').doLayout();