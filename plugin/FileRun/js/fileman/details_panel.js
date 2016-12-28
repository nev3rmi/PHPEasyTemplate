FR.components.detailsPanel = Ext.extend(Ext.Panel, {
	metadataCache: new Ext.util.MixedCollection(),
	readMe: false,
	initComponent: function() {
		this.metaLoaderTask = new Ext.util.DelayedTask(function(){this.loadMeta()}, this);
		this.folderIcon = '<i class="fa fa-folder" style="font-size: 35px;color:#8F8F8F"></i>';
		Ext.apply(this, {
			title: '<i class="fa fa-fw fa-2x fa-info" style="font-size:2.1em"></i>',
			cls: 'fr-details-panel',
			autoScroll: true,
			html:
				'<div id="fr-details-previewbox" style="visibility:hidden">' +
					'<div class="title">' +
						'<div style="display:table-row">' +
						'<div id="fr-details-icon"></div>' +
						'<div id="fr-details-filename"></div>' +
						'</div>' +
					'</div>' +
					'<div style="clear:both;"></div>' +
					'<div class="thumb" id="fr-details-thumb" style="display:none"></div>' +
				'</div>'+
				'<div id="fr-details-info" style="display:none"></div>'+
				'<div id="fr-details-readme" style="display:none"></div>'+
				'<div id="fr-details-metadata" style="display:none"></div>',
			listeners: {
				'afterrender': function() {
					this.previewBox = Ext.get('fr-details-previewbox');
					this.fileNameEl = Ext.get('fr-details-filename');
					this.iconEl = Ext.get('fr-details-icon');
					this.thumbContainer = Ext.get('fr-details-thumb');
					this.thumbContainer.setVisibilityMode(Ext.Element.DISPLAY);
					this.thumbContainer.on('click', function(){
						if (this.item.filetype == 'img') {
							FR.UI.imagePreview.init(this.item);
						} else {
							FR.utils.showPreview(this.item);
						}
					}, this);
					this.infoEl = Ext.get('fr-details-info').enableDisplayMode();
					this.readMeEl = Ext.get('fr-details-readme').enableDisplayMode();
					this.metadataEl = Ext.get('fr-details-metadata').enableDisplayMode();

					this.body.first().on('contextmenu', function() {
						FR.UI.gridPanel.showContextMenu();
					});
				},
				'activate': function(p){
					p.active = true;
					this.gridSelChange();
				},
				'deactivate': function(p) {p.active = false;},
				'resize': function() {if (this.active && this.item) {this.updateQuickView();}},
				scope: this
			}
		});
		FR.components.detailsPanel.superclass.initComponent.apply(this, arguments);
	},
	onRender: function() {
		FR.components.detailsPanel.superclass.onRender.apply(this, arguments);
	},
	setReadMe: function(t) {this.readMe = t;},
	gridSelChange: function() {
		if (!this.active) {return false;}
		if (!FR.UI.tree.currentSelectedNode) {return false;}
		this.countSel = FR.UI.infoPanel.countSel;
		this.countAll = FR.UI.infoPanel.countAll;
		this.item = FR.UI.infoPanel.item;
		if (this.item) {
			this.itemPath = (this.item.data.path || FR.currentPath+'/'+this.item.data.filename);
		}
		this.updateQuickView();
	},
	reset: function() {
		this.metaLoaderTask.cancel();
		this.readMeEl.hide().update('');
		this.metadataEl.update('');
		this.infoEl.update('');
	},
	setItemTitle: function(itemTitle) {
		this.fileNameEl.update(itemTitle);
		this.previewBox.show();
	},
	setIcon: function(icon) {
		this.iconEl.update(icon);
	},
	updateQuickView: function() {
		if (!this.active) {return false;}
		this.reset();

		if (this.countSel == 1) {
			this.metadataEl.show();
			this.loadQuickView();
		} else {
			this.thumbContainer.hide();
			var iconCls = FR.UI.tree.currentSelectedNode.attributes.iconCls || 'fa-folder';
			this.setIcon('<i class="fa '+iconCls+'" style="font-size: 35px;color:#8F8F8F"></i>');
			this.setItemTitle(FR.UI.tree.currentSelectedNode.text);

			var size = '';
			if (this.countAll == 0) {
				var statusText = FR.T('There are no files in this folder.');
			} else {
				var sel;
				if (this.countSel == 0) {
					sel = FR.UI.gridPanel.store.data.items;
					if (this.countAll == 1) {
						statusText = FR.T('One item');
					} else if (this.countAll > 0) {
						statusText = FR.T('%1 items').replace('%1', this.countAll);
					}
				} else {
					sel = FR.UI.gridPanel.selModel.getSelections();
					statusText = FR.T('%1 items selected').replace('%1', this.countSel);
				}
				size = 0;
				Ext.each(sel, function (item) {
					if (item.data.isFolder) {
						size = false;
						return false;
					}
					size += parseInt(item.data.filesize);
				});
				if (size > 0) {
					size = Ext.util.Format.fileSize(size);
				} else {
					size = '';
				}
			}
			var info = '<div class="status">' +
				'<div class="text">'+statusText+'</div>' +
				'<div class="size">'+size+'</div>' +
				'<div style="clear:both"></div><div>';
			this.infoEl.update(info).show();
			if (this.readMe) {
				this.readMeEl.update(this.readMe).show();
			}
		}
	},
	loadQuickView: function() {
		var title = this.item.data.isFolder ? this.item.data.filename : FR.utils.dimExt(this.item.data.filename);
		if (this.item.data.isFolder) {
			this.setIcon(this.folderIcon);
			this.setItemTitle(title);
		} else {
			var iconSrc = 'images/fico/' + this.item.data.icon;
			this.setIcon('<img src="'+iconSrc+'" height="30" align="left" style="margin-right:5px;" />');
			this.setItemTitle(title);
		}
		this.thumbContainer.hide();
		this.thumbContainer.update('');
		if (this.item.data.thumb) {
			var imageSrc;
			if (this.item.data.thumbImg) {
				imageSrc = this.item.data.thumbImg.dom.src;
			} else {
				imageSrc = FR.UI.getThumbURL(this.item.data);
			}
			this.thumbImg = Ext.get(Ext.DomHelper.createDom({tag: 'img', cls:'detailsThumb'}));
			this.thumbImg.on('load', function() {
				if (this.thumbImg.dom) {
					var naturalWidth = this.thumbImg.dom.width;
					var maxWidth = FR.UI.infoPanel.getWidth();
					var w = maxWidth-45;
					if (naturalWidth < w) {w = naturalWidth;}
					this.thumbImg.set({width: w, height: 'auto'});
					this.thumbContainer.appendChild(this.thumbImg);
					this.thumbContainer.show();
				}
			}, this);
			this.thumbImg.set({src: imageSrc});
		}

		var info = '';
		if (['starred', 'search', 'webLinked', 'photos'].indexOf(FR.currentSection) !== -1) {
			var pInfo = FR.utils.pathInfo(this.item.data.path);
			info += '<tr>' +
			'<td class="fieldName">'+FR.T('Location')+'</td>' +
			'<td class="fieldValue"><a href="javascript:;" onclick="FR.utils.locateItem(\''+pInfo.dirname+'\', \''+pInfo.basename+'\')">'+FR.utils.humanFilePath(pInfo.dirname)+'</a></td>' +
			'</tr>';
		}

		if (FR.currentSection == 'trash') {
			info += '<tr>' +
			'<td class="fieldName">'+FR.T('Deleted from')+'</td>' +
			'<td class="fieldValue">'+this.item.data.trash_deleted_from+'</td>' +
			'</tr>';
		}

		if (!this.item.data.isFolder) {
			info += '<tr>' +
			'<td class="fieldName">' + FR.T('Size') + '</td>' +
			'<td class="fieldValue" title="' + Ext.util.Format.number(this.item.data.filesize, '0,000') + ' ' + FR.T('bytes') + '">' + this.item.data.nice_filesize + '</td>' +
			'</tr>';
		}

		info += '<tr>' +
		'<td class="fieldName">'+FR.T('Type')+'</td>' +
		'<td class="fieldValue">'+this.item.data.type+'</td>' +
		'</tr>';

		if (!this.item.data.isFolder) {
			if ((this.item.data.modified && this.item.data.created) && (this.item.data.modified.getTime() != this.item.data.created.getTime())) {
				info += '<tr>' +
				'<td class="fieldName">' + FR.T('Modified') + '</td>' +
				'<td class="fieldValue" ext:qtip="'+this.item.data.modified+'">' +
					(Settings.grid_short_date ? this.item.data.modifiedHuman : Ext.util.Format.date(this.item.data.modified, FR.T('Date Format: Files'))) +
				'</td>' +
				'</tr>';
			}
		}

		if (this.item.data.created) {
			info += '<tr>' +
			'<td class="fieldName">' + FR.T('Created') + '</td>' +
			'<td class="fieldValue" ext:qtip="'+this.item.data.created+'">' +
				(Settings.grid_short_date ? this.item.data.createdHuman : Ext.util.Format.date(this.item.data.created, FR.T('Date Format: Files'))) +
			'</td>' +
			'</tr>';
		}

		info = '<table cellspacing="1" width="100%">' + info + '</table>';
		this.infoEl.update(info).show();

		if (!this.item.data.isFolder) {
			if (this.metadataCache.containsKey(this.itemPath)) {
				this.metadataEl.update(this.metadataCache.get(this.itemPath));
			} else {
				this.metaLoaderTask.delay(500);
			}
		}
	},
	loadMeta: function() {
		if (!User.perms.metadata) {return false;}
		if (!this.item) {return false;}
		this.metadataEl.update('<span style="color:silver;font-size:9px;margin:5px;">'+FR.T('Loading metadata...')+'</span>');
		Ext.Ajax.request({
			url: FR.baseURL+'/?module=metadata&page=quick_view',
			params: {path: this.itemPath},
			callback: function(opts, succ, req) {
				if (req.responseText.length == 0) {return false;}
				if (!this.metadataCache.containsKey(this.itemPath)) {
					this.metadataCache.add(this.itemPath, req.responseText);
				} else {
					this.metadataCache.replace(this.itemPath, req.responseText);
				}
				this.metadataEl.update(req.responseText);
			}, scope: this
		});
	},
	editMeta: function() {
		FR.actions.openMetadata({title: this.item.data.filename, path: this.itemPath});
	}
});