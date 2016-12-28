FR = {
	UI: {},
	init: function() {

		this.charsetSelector = new Ext.form.ComboBox({
			width: 160, emptyText: FR.T('Charset for saving'),
			mode: 'local', triggerAction: 'all', editable: false,
			store: new Ext.data.ArrayStore({
				id: 0,
				fields: ['text'],
				data: charsets
			}),
			valueField: 'text',
			displayField: 'text', value: (charset || 'UTF-8'),
			listeners: {
				'select': function() {
					new Ext.ux.prompt({
						text: FR.T('Would you like to reload the file using the selected charset? Any unsaved changes will be lost.'),
						confirmHandler: function() {FR.changeCharset(this.getValue());},
						scope: this
					});
				}
			}
		});

		this.viewport = new Ext.Viewport({
			layout: 'fit',
			items: {
				layout: 'fit',
				border: false,
				html: '<div id="editor" style="position: absolute;top: 0;right: 0;bottom: 0;left: 0;"></div>',
				tbar: [{
					text: FR.T("Save"), cls: 'fr-btn-default fr-btn-primary fr-btn-smaller fr-btn-icon-white',
					handler: function(){this.save(false);}, scope: this
					},
					{
					text: FR.T("Save and close"), cls: 'fr-btn-default fr-btn-primary fr-btn-smaller fr-btn-icon-white', style: 'margin-left:10px;padding-left:5px;padding-right:5px;',
					handler: function(){this.save(true);}, scope: this
					},
					'->',
					{
						xtype: 'button',
						enableToggle: true,
						text: FR.T('Word wrap'),
						toggleHandler: function(b, pressed) {
							FR.editor.getSession().setUseWrapMode(pressed);
						}
					},' ',
					this.charsetSelector, '&nbsp;'
				]
			},
			listeners: {
				'afterrender': function() {
					FR.editor = ace.edit("editor");
					var modelist = ace.require('ace/ext/modelist');
					var mode = modelist.getModeForPath(filename).mode;
					if (!mode) {mode = 'ace/mode/html'}
					FR.editor.setTheme("ace/theme/eclipse");
					FR.editor.getSession().setMode(mode);
					FR.editor.getSession().setUseWrapMode(false);
					FR.editor.getSession().setValue(FR.getText());
					FR.editor.commands.addCommand({
						name: 'Save',
						bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
						exec: function(editor) {FR.save();}
					});
				}
			}
		});
	},
	changeCharset: function(charset) {
		var frm = document.createElement('FORM');
		frm.action = URLRoot+'/?module=custom_actions&action=code_editor&_popup_id='+windowId;
		frm.method = 'POST';
		var postArgs = [
			{name: 'path', value: path},
			{name: 'filename', value: filename},
			{name: 'charset', value: charset}
		];
		Ext.each(postArgs, function(param) {
			inpt = document.createElement('INPUT');
			inpt.type = 'hidden';
			inpt.name = param.name;
			inpt.value = encodeURIComponent(param.value);
			frm.appendChild(inpt);
		});
		Ext.get('theBODY').appendChild(frm);
		frm.submit();
	},
	getText: function() {
		return Ext.get('textContents').dom.value;
	},
	save: function(close) {
		this.closeAfterSave = close;
		this.viewport.getEl().mask(FR.T('Saving...'));
		Ext.Ajax.request({
			url: URLRoot+'/?module=custom_actions&action=code_editor&method=saveChanges',
			params: {
				path: path,
				filename: filename,
				charset: this.charsetSelector.getValue(),
				textContents: FR.editor.getSession().getValue()
			},
			success: function(req) {
				this.viewport.getEl().unmask();
				try {
					var rs = Ext.util.JSON.decode(req.responseText);
				} catch (er){return false;}
				if (rs.msg) {
					window.parent.FR.UI.feedback(rs.msg);
				}
				if (rs.rs && this.closeAfterSave) {
					window.parent.FR.UI.popups[windowId].close();
				}
			},
			scope: this
		});
	}
}