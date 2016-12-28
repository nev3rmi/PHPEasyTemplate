FR.editSettings = {};
FR.editSettings.formPanel = new FR.components.editForm({
	title: FR.T('OAuth2'),
	layout: 'form', bodyStyle: 'padding:10px;',
	autoScroll: true, defaults: {width: 500},
	items: [
		{
			xtype: 'fieldset',
			items:[
				{xtype: 'displayfield', hidden: !Ext.isIE},
				{
					xtype: 'checkbox', hideLabel: true,
					boxLabel: FR.T('Enable OAuth2'), value: 1,
					name: 'settings[oauth2]', checked: parseInt(FR.settings.oauth2)
				}
			]
		},
		{
			xtype: 'displayfield', hideLabel: true, style: 'color:gray', value: FR.T('Requires a HTTPS-configured web server')
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
				var opts = {
					url: FR.URLRoot+'/?module=cpanel&section=settings&page=oauth&action=save',
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