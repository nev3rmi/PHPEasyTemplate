FR.customActions.crypt = {
	run: function() {
		var item = FR.UI.gridPanel.getOneSel().data;
		this.fileName = item.filename;
		this.filePath = item.path ? item.path : FR.currentPath+'/'+item.filename;
		this.action = FR.utils.getFileExtension(this.fileName) == 'aes' ? 'decrypt' : 'encrypt';
		if (this.action == 'encrypt') {
			var title = '<?php echo self::t('Encrypt "%1"', array('%1'));?>'.replace('%1', this.fileName);
		} else {
			var title = '<?php echo self::t('Decrypt "%1"', array('%1'));?>'.replace('%1', this.fileName);
		}
		if (!this.prompt) {
			this.prompt = new Ext.Window({
				title: title,
				layout : 'form', width: 380, height: 230,
				closable: false, closeAction: 'hide', resizable: false,
				bodyStyle:'padding:10px;background-color:white',
				labelAlign: 'right', labelWidth: 140,
				items: [
					{
						xtype: 'textfield',
						fieldLabel: '<?php echo self::t("Password");?>',
						name: 'password', inputType: 'password'
					},
					{
						xtype: 'textfield',
						fieldLabel: '<?php echo self::t("Confirm password");?>',
						labelStyle: 'white-space:nowrap',
						name: 'password', inputType: 'password', hidden: (this.action == 'decrypt')
					},
					{xtype: 'checkbox', fieldLabel: '', boxLabel: '<?php echo self::t("Delete the source file");?>'}
				],
				buttonAlign: 'left',
				buttons: [{
					text : FR.T('Ok'), cls: 'fr-btn-default fr-btn-primary',
					handler: function() {this.doAction();}, scope: this
				}, {
					text : FR.T('Cancel'), cls: 'fr-btn-default', style: 'margin-left:15px',
					handler : function() {this.prompt.hide();}, scope: this
				}]
			});
		} else {
			this.prompt.setTitle(title);
			//reset pass fields
			this.prompt.items.first().setValue('');
			this.prompt.items.get(1).setValue('');
			this.prompt.items.get(1).setVisible((this.action != 'decrypt'));
		}
		this.prompt.show();
	},
	doAction: function(pass) {
		var pass = FR.customActions.crypt.prompt.items.first().getValue();
		var repass = FR.customActions.crypt.prompt.items.get(1).getValue();
		if (pass.length == 0) {
			new Ext.ux.prompt({text: '<?php echo self::t("Please type the password");?>'});
			return false;
		}
		if (this.action == 'encrypt' && pass != repass) {
			new Ext.ux.prompt({text: '<?php echo self::t("Please confirm the password");?>'});
			return false;
		}
		var deleteSrc = FR.customActions.crypt.prompt.items.get(2).getValue();
		var pars = {path: this.filePath, pass: pass, deleteSrc: deleteSrc};
		var url = FR.baseURL+'/?module=custom_actions&action=crypt&method=run';
		FR.UI.showLoading('<?php echo self::t("Processing file...");?>');
		Ext.Ajax.request({
			url: url,
			method: 'post',
			params: pars,
			callback: function(opts, succ, req) {
				FR.UI.doneLoading();
				try {
					var rs = Ext.util.JSON.decode(req.responseText);
				} catch (er){return false;}
				if (rs.success) {FR.utils.reloadGrid();}
				FR.UI.feedback(rs.msg);
				this.prompt.hide();
			}, scope: this
		});
	}
}