FR.components.commentsPanel = Ext.extend(Ext.ux.ListPanel, {
	path: false,
	initComponent: function() {
		this.store = new Ext.data.Store({
			proxy: new Ext.data.HttpProxy({
				url: URLRoot+'/?module=comments&section=ajax&page=index&action=load'
			}),
			reader: new Ext.data.JsonReader({
				root: 'comments', totalProperty: 'totalCount', id: 'id',
				fields: ['id', 'date_added', 'timer', 'uid', 'val', 'username', 'fullName', 'self', 'followup']
			})
		});
		this.store.on('beforeload', function(store, records, opts) {
			this.grid.getEl().update('<div class="commentsEmpty">'+FR.T('Loading...')+'</div>');
		}, this);
		this.store.on('exception', function(misc) {
			this.grid.getEl().update('<div class="commentsEmpty">'+FR.T('Failed to load comments data')+'</div>');
		}, this);
		this.store.on('load', function(store, records, opts) {
			var d = store.reader.jsonData;
			if (d.msg) {
				if (d.totalCount > 0) {
					FR.UI.feedback(rs.msg);
					FR.UI.feedback(d.msg);
				} else {
					this.grid.getEl().update('<div class="commentsEmpty">'+d.msg+'</div>');
				}
			}
			FR.utils.applyFileUpdates(d.path, {comments: d.totalCount});
			this.grid.el.scroll('b', 10000, true);
		}, this);
		this.btns = {
			print: new Ext.Button({
				cls: 'printComments-trigger',
				iconCls: 'fa fa-print',
				handler: this.print, scope: this
			})
		};

		var tpl = '<div class="comments">'+
					'<div class="x-clear"></div>' +
					'<tpl for=".">'+
						'<div class="comment <tpl if="self">own</tpl> <tpl if="followup">followup</tpl>">'+

							'<tpl if="!uid"><div class="name">{fullName}</div></tpl>' +
							'<div class="time" title="{date_added:date("l, F jS, Y \\\\a\\\\t h:i A")}">{timer}</div>' +
							'<tpl if="uid"><div class="name">{fullName}</div></tpl>' +
							'<div class="x-clear"></div>'+
							'<tpl if="!followup"><div class="avatar" ext:qtip="{fullName}" style="background-image:url(a/?uid={uid})"></div></tpl>' +
							'<div class="text"><div class="inner">';
					if (User.perms.write_comments){
						tpl +=  '<div class="removeBtn"><a onclick="FR.UI.deleteComment(\''+this.id+'\', \'{id}\')"><i class="fa fa-close"></i></a></div>';
					}
						tpl +=  '{val}'+
							'</div></div>'+

						'</div>'+
						'<div class="x-clear"></div>'+

					'</tpl>'+
				'</div>'+
				'<div class="x-clear"></div>';

		tpl = new Ext.XTemplate(tpl);
		this.grid = new Ext.DataView({
			region: 'center',  autoScroll: true,
			flex:1, border: false, tpl: tpl,
			store: this.store,
			emptyText: ''
		});
		this.inputBox = new Ext.form.TextArea({
			flex: 1,
			emptyText: FR.T('Write a comment...'), enableKeyEvents: true,
			listeners: {
				'render': function() {
					this.inputBox.el.set({'placeholder': FR.T('Write a comment...')});
				},
				'keydown': function(field, e) {
					if (e.getKey() == e.ENTER) {if (!e.shiftKey) {this.addComment();}}
				}, scope: this
			}
		});
		this.writePanel = new Ext.Panel({
			region: 'south',
			layout: 'hbox', border: false,
			height: 60, layoutConfig: { align: "stretch" }, margins: "5 5 10 5",
			items: [
				this.inputBox,
				this.btns.print
			]
		});
		Ext.apply(this, {
			layout: 'border',
			items: [
				this.grid,
				this.writePanel
			],
			listeners: {
				'activate': function(p) {
					p.active = true;
					if (this.isSet()) {
						this.inputBox.focus();
						this.load();
					}
				},
				'deactivate': function(p) {p.active = false;}
			}
		});
		FR.components.commentsPanel.superclass.initComponent.apply(this, arguments);
	},
	onRender: function() {
		FR.components.commentsPanel.superclass.onRender.apply(this, arguments);
	},
	isSet: function() {
		return this.path;
	},
	setItem: function(path, cCount) {
		if (path == this.path) {return this;}
		this.store.removeAll(true);
		if (this.active) {
			this.grid.refresh();
		}
		if (FR.utils.canAddComments()) {
			this.writePanel.show();
		} else {
			this.writePanel.hide();
		}
		this.doLayout(true);
		this.setTitleNumber(cCount);
		this.path = path;
		this.store.setBaseParam('path', path);
		if (!this.collapsed) {
			this.load();
		}
		return this;
	},
	load: function() {
		if (!this.active) {return false;}
		this.store.load();
	},
	addComment: function() {
		var c = encodeURIComponent(this.inputBox.getValue());
		if (c.length > 0) {
			this.action('add', {comment: c});
		}
	},
	print: function() {
		window.open(URLRoot+'/?module=comments&page=print&path='+encodeURIComponent(this.path));
	},
	deleteComment: function(cid) {
		new Ext.ux.prompt({
			text: FR.T('Are you sure you want to remove the comment?'),
			confirmHandler: function() {
				this.action('remove', {commentId: encodeURIComponent(cid)});
			}, scope: this
		});
	},
	showLoading: function () {this.getEl().mask(FR.T('Loading...'));},
	hideLoading: function () {this.getEl().unmask();},
	action: function(action, params, callback) {
		this.showLoading();
		Ext.Ajax.request({
			url: URLRoot+'/?module=comments&section=ajax&page=index&action='+action,
			params: Ext.apply(this.store.baseParams, params),
			success: function(req) {
				this.hideLoading();
				try {
					var rs = Ext.decode(req.responseText);
				} catch (er){return false;}
				if (rs) {
					if (rs.rs) {
						if (callback) {callback.call();}
						this.inputBox.reset();
						this.load();
					}
					if (rs.msg) {
						FR.UI.feedback(rs.msg);
					}
				}
			},
			scope: this
		});
	}
});
FR.UI.deleteComment = function(panelId, cId) {Ext.getCmp(panelId).deleteComment(cId);}