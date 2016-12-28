FR.components.gridPanel = Ext.extend(Ext.grid.GridPanel, {
	loadParams: {}, highlightOnDisplay: false,
	initComponent: function() {
		this.initStore();
		this.initColumns();
		this.setViewMode();
		Ext.apply(this, {
			region: 'center', border: false,
			stateful: true, stateId: 'filesGrid',
			stateEvents: ['columnresize', 'columnmove'],
			ddGroup : 'TreeDD', ds: this.store, cm: this.columnModel,
			enableDragDrop: !User.perms.read_only,
			stripeRows: false, trackMouseOver: false, enableColLock:false,
			selModel: new Ext.grid.RowSelectionModel({
				singleSelect:false,
				listeners: {
					'selectionchange': function() {this.onSelectionChange.delay(150, false, this);}, scope: this
				}
			}),
			autoExpandColumn: 'filename',
			view: new FR.components.thumbGridView({
				viewMode: this.viewMode,
				rowOverCls: 'tmbItemOver',
				sortAscText: FR.T('Sort Ascending'),
				sortDescText: FR.T('Sort Descending'),
				columnsText: FR.T('Columns')
			}),
			plugins: [new Ext.ux.GridDragSelector({dragSafe:true})],
			listeners: {
				'afterrender': function() {
					this.view.scroller.on('scroll', function() {this.loadThumbs.delay(300, false, this);}, this);
					this.setMetaCols();
				},
				'resize': function() {
					this.loadThumbs.delay(300, false, this);
				},
				'rowdblclick': function (grid, rowIndex, e){
					if (FR.currentSection != 'trash') {
						var s = this.store.getAt(rowIndex).data;
						if (!s) {return false;}
						var path = s.path;
						if (s.isFolder) {
							FR.utils.browseToPath(path);
						} else {
							var ext = FR.utils.getFileExtension(s.filename);
							if (FR.ext[ext] && FR.UI.contextActions[FR.ext[ext]]) {
								FR.UI.contextActions[FR.ext[ext]].execute();
							} else {
								if (Settings.ui_double_click == 'download') {
									FR.actions.download(path);
								} else if (Settings.ui_double_click == 'downloadb') {
									FR.actions.openFileInBrowser(path);
								} else if (Settings.ui_double_click == 'showmenu') {
									this.showContextMenu();
								} else {
									FR.utils.showPreview();
								}
							}
						}
					}
					e.stopEvent();
					return false;
				},
				'rowmousedown': function(p, rowIndex, e) {
					var trigger = Ext.Element.fly(e.target);
					if (trigger.hasClass('menuTrigger') || trigger.hasClass('triangle')) {
						this.showContextMenu();
					}
				},
				'containermousedown': function(p, e) {
					if (!e.ctrlKey) {
						this.selModel.clearSelections();
						this.countSel = 0;
					}
				},
				'containercontextmenu': function(p, e) {
					this.showContextMenu();
					e.stopEvent();
				},
				'rowcontextmenu': function(grid, rowIndex, e) {
					if (!this.selModel.isSelected(rowIndex)) {
						this.selModel.clearSelections();
						this.selModel.selectRow(rowIndex);
						this.countSel = 1;
						FR.currentSelectedFile = this.store.getAt(rowIndex);
					}
					if (this.countSel > 0) {
						this.showContextMenu();
					}
					e.stopEvent();
				},
				'render': function (grid) {
					if (User.perms.alter) {
						this.dropZone = new Ext.dd.DropZone(grid.getView().scroller, {
							ddGroup: grid.ddGroup,
							getTargetFromEvent: function (e) {
								var target = e.getTarget(grid.getView().rowSelector);
								if (target) {
									var rowIndex = grid.getView().findRowIndex(target);
									var r = grid.getStore().getAt(rowIndex);
									if (r.data.isFolder) {
										return {rowIndex: rowIndex, record: r};
									}
								}
							},
							onNodeEnter: function (target) {
								Ext.fly(grid.getView().getRow(target.rowIndex)).addClass('dragged-over');
							},
							onNodeOut: function (target) {
								Ext.fly(grid.getView().getRow(target.rowIndex)).removeClass('dragged-over');
							},
							onNodeOver: function (target) {
								if (target.record.data.isFolder) {
									return Ext.dd.DropZone.prototype.dropAllowed;
								}
							},
							onNodeDrop: function (target, dz, e, dropData) {
								if (target.record.data.isFolder) {
									FR.actions.move({data: dropData}, target.record.data.path);
								}
								return true;
							}
						});
					}

					if (User.perms.upload) {
						FlowUtils.DropZoneManager.add({
							domNode: this.getView().scroller.dom, overClass: 'dragged-over',
							findTarget: function(e) {
								if (['myfiles', 'sharedFolder'].indexOf(FR.currentSection) == -1) {
									return false;
								}
								if (FR.UI.tree.currentSelectedNode.attributes.perms && !FR.UI.tree.currentSelectedNode.attributes.perms.upload) {
									return false;
								}
								var n, cls,
									p = FR.UI.gridPanel,
									el = Ext.get(e.target);
								if (FR.UI.gridPanel.getView().viewMode == 'list') {
									cls = 'x-grid3-row';
								} else {
									cls = 'tmbItem';
								}
								if (el && !el.hasClass(cls)) {el = el.parent('div.'+cls);}
								if (!el) {
									return {el: this.getView().scroller.dom};
								}
								var rowIndex = p.getView().findRowIndex(el.dom);
								var r = p.getStore().getAt(rowIndex);
								if (r.data.isFolder) {
									return {el: el.dom, record: r};
								} else {
									return {el: this.getView().scroller.dom};
								}
							},
							onDrop: function (e, target) {
								if (!target.record) {
									if (
										(FR.currentFolderPerms && !FR.currentFolderPerms.upload) ||
										(FR.currentSection != 'myfiles' && FR.currentSection != 'sharedFolder')
									) {return false;}
									var path = FR.currentPath;
									var folderName = FR.UI.tree.currentSelectedNode.text;
								} else {
									var r = target.record;
									var path = r.data.path;
									var folderName = r.data.filename;

								}
								FR.UI.uploadWindow(FR.T('Upload to "%1"').replace('%1', folderName),
									new FR.components.uploadPanel({targetPath: path, dropEvent: e})
								);
							},
							scope: this
						});
					}
				},
				scope: this
			}
		});
		this.addEvents('folderChange');
		FR.components.gridPanel.superclass.initComponent.apply(this, arguments);
	},
	getDragDropText : function(){
		var count = this.selModel.getCount ? this.selModel.getCount() : 1;
		if (count == 1) {
			return  FR.T('One item');
		}
		return  FR.T('%1 items').replace('%1', count);
	},
	setViewMode: function() {
		this.viewMode = Settings.ui_default_view;
		var savedViewMode = Ext.state.Manager.getProvider().get('fr-view-mode', false);
		if (savedViewMode) {
			this.viewMode = savedViewMode;
			FR.UI.actions.toggleViewList.setIconClass(FR.UI.getViewIconCls(this.viewMode));
		}
	},
	initStore: function() {
		this.JsonReaderCols = [
			{name: 'uniqid', mapping: 'id'},
			{name: 'isFolder', mapping: 'dir'},
			{name: 'filename', mapping: 'n', sortFunction: function(r1, r2) {
				return compareAlphaNum(r1.get('filename').toLowerCase(), r2.get('filename').toLowerCase());
			}},
			{name: 'trash_deleted_from', mapping: 'tdf'},
			{name: 'filesize', mapping: 's', sortType: Ext.data.SortTypes.asInt},
			{name: 'nice_filesize', mapping: 'ns'},
			{name: 'icon', mapping: 'i'},
			{name: 'type', mapping: 't'},
			{name: 'thumb', mapping: 'th'},
			{name: 'filetype', mapping: 'ft'},
			{name: 'meta_filetype', mapping: 'mf'},
			{name: 'modified', mapping: 'm', type:'date', dateFormat:'m/d/Y h:i'},
			{name: 'modifiedHuman', mapping: 'mh'},
			{name: 'created', mapping: 'c', type:'date', dateFormat:'m/d/Y h:i'},
			{name: 'taken', mapping: 'dt', type: 'int'},
			{name: 'createdHuman', mapping: 'ch'},
			{name: 'isNew', mapping: 'new'},
			{name: 'hasWebLink', mapping: 'hW'},
			{name: 'share', mapping: 'sh'},
			{name: 'notInfo', mapping: 'fn'},
			{name: 'comments', mapping: 'cc'},
			{name: 'label', mapping: 'l'},
			{name: 'tags', mapping: 'tg'},
			{name: 'star', mapping: 'st'},
			{name: 'path', mapping: 'p', convert: function(v, r) {return v || FR.currentPath+'/'+r.n;}},
			{name: 'lockInfo', mapping: 'lI'},
			{name: 'version', mapping: 'v'}
		];
		Ext.each(FR.UI.grid.customColumns, function(col) {
			this.JsonReaderCols.push({name: col.dataIndex});
		}, this);

		this.store = new FR.components.thumbGridStore({
			proxy: new Ext.data.HttpProxy({url: this.myfilesBaseURL+'&page=grid'}),
			sortInfo: {field: 'filename', direction: 'ASC'},
			reader: new Ext.data.JsonReader({root: 'files', totalProperty: 'count', id: 'id'}, this.JsonReaderCols)
		});
		this.store.on('exception', function(p, t, a, opt) {
			var d = opt.reader.jsonData;
			if (d && d.authError) {
				new Ext.ux.prompt({
					title: FR.T('Error'),
					text: d.msg,
					confirmBtnLabel: FR.T('Refresh'),
					callback: function() {document.location.href = FR.logoutURL;}
				});
			}
			this.body.mask(FR.T('Failed to load file list.<br>Please check your network connection.'));
		}, this);
		this.store.on('beforeload', function(store, opts) {
			store.removeAll(true);
			this.selModel.clearSelections(true);
			this.view.mainBody.update('');
		}, this);
		this.store.on('load', function(store, records, opts) {
			var data = store.reader.jsonData;
			if (this.body.isMasked()) {
				this.body.unmask();
			}
			if (data.error) {
				new Ext.ux.prompt({title: FR.T('Error'), text: data.error});
				if (FR.currentPath == '/ROOT/HOME') {
					FR.UI.tree.panel.el.mask();
				} else {
					var currentSelectedNode = FR.UI.tree.panel.getSelectionModel().getSelectedNode();
					currentSelectedNode.parentNode.removeChild(currentSelectedNode);
					FR.UI.tree.homeFolderNode.select();
				}
			} else {
				this.fireEvent('folderChange', this, store);
				this.onSelectionChange.delay(0, false, this);
				if (this.highlightOnDisplay) {
					this.highlight(this.highlightOnDisplay);
					this.highlightOnDisplay = false;
				}
				if (store.reader.jsonData.countNewEvents) {
					FR.UI.activityPanel.updateStatus(parseInt(data.countNewEvents));
				}
				FR.UI.detailsPanel.setReadMe(data.readme);
			}
		}, this);
	},
	initColumns: function() {
		this.columns = [
			{
				header: false, renderer: function(v, m, r) {return r.data.iconHTML;},
				width: 58, align:'right', resizable: false, hideable: false, menuDisabled: true
			},{
				id: 'filename',
				header: FR.T("Name"), renderer: function(v, m, r) {return r.data.filenameHTML;},
				dataIndex: 'filename', width:220, hideable: false
			},{
				id:'icons', align: 'center', css: 'color:silver;',
				header: false, renderer: function(v, m, r) {return r.data.icons;},
				width:43, resizable: false, hideable: false, menuDisabled: true
			},{
				id: 'nice_filesize',
				header: FR.T("Size"), align: 'right',
				dataIndex: 'filesize', width: 65,
				renderer: function(v, m, r) {return r.data.nice_filesize;}
			},{
				id: 'type',
				header: FR.T("Type"),
				dataIndex: 'type', width: 120, hidden: true
			},{
				custom: true,
				id: 'meta_filetype',
				header: FR.T("Meta Type"),
				dataIndex: 'meta_filetype', width: 120, hidden: true
			},{
				id: 'modified',
				header: FR.T("Modified"), hidden: FR.isMobile,
				dataIndex: 'modified', width:120,
				renderer: function(v, col, row) {
					if (Settings.grid_short_date) {
						return row.data.modifiedHuman;
					}
					return Ext.util.Format.date(v, FR.T('Date Format: Files'));
				}
			},{
				id: 'created',
				header: FR.T("Created"),
				dataIndex: 'created', width:120, hidden:true,
				renderer: function(v, col, row) {
					if (Settings.grid_short_date) {
						return row.data.createdHuman;
					}
					return Ext.util.Format.date(v, FR.T('Date Format: Files'));
				}
			},{
				id: 'trash_deleted_from',
				header: FR.T("Deleted from"),
				dataIndex: 'trash_deleted_from',width:180, hidden: true
			},{
				id: 'path',
				header: FR.T("Location"),
				dataIndex: 'path', width:180, hidden: true,
				renderer: FR.utils.humanFilePath
			},{
				id: 'commentsCount',
				header: FR.T("Comments count"),
				dataIndex: 'comments', width:50, hidden:true,
				renderer: function(val) {return val>0?val:'';}
			},{
				custom: true,
				id: 'meta_tags',
				header: FR.T("Tags"),
				dataIndex: 'tags', hidden:true
			},{
				id: 'label',
				header: FR.T("Label"),
				dataIndex: 'label', width:50, hidden:true,
				renderer: function(v) {
					if (v) {
						var s = v.split('|');
						return '<span class="FRLabel" style="background-color:'+s[1]+'">'+s[0]+'</span>';
					}
				}
			},{
				id: 'star',
				header: FR.T("Star"),
				dataIndex: 'star', width:50, hidden:true,
				renderer: function(v) {return v ? FR.T('Yes') : '';}
			},{
				id: 'hasWebLink',
				header: FR.T("Web Link"),
				dataIndex: 'hasWebLink', width:50, hidden:true,
				renderer: function(v) {return v ? FR.T('Yes') : '';}
			},{
				id: 'lockInfo',
				header: FR.T("Locked by"),
				dataIndex: 'lockInfo', width:50, hidden:true,
				renderer: function(v) {return v ? v : '';}
			},{
				id: 'version',
				header: FR.T("Version"),
				dataIndex: 'version', width:40, hidden:true,
				renderer: function(v) {return (v && v != '1') ? v : '';}
			},{
				id: 'isNew',
				header: FR.T("Is new"),
				dataIndex: 'isNew', width:40, hidden:true,
				renderer: function(v) {return v ? FR.T('Yes') : '';}
			}
		];

		Ext.each(FR.UI.grid.customColumns, function(col) {
			col.custom = true;
			this.columns.push(col);
		}, this);

		this.columnModel = new Ext.grid.ColumnModel({
			defaults: {sortable: true},
			columns: this.columns
		});

		this.columnModel.on('hiddenchange', function(cm, colIndex, hideCol) {
			var column = cm.getColumnById(cm.getColumnId(colIndex));
			if (column.custom) {
				this.setMetaCols();
				if (!hideCol) {
					//this is causing problems because of the state restoring
					this.load(FR.currentPath);
				}
			}
		}, this);
	},
	setMetaCols: function() {
		var metadataCols = [];
		var cols = this.columnModel.getColumnsBy(function(colCfg, indx) {
			var col = this.columnModel.getColumnById(this.columnModel.getColumnId(indx));
			if (col.custom && !col.hidden) {
				metadataCols.push(col.dataIndex);
				return true;
			}
		}, this);

		this.loadParams.metadata = encodeURIComponent(metadataCols.join('|'));
	},
	getSelectedFiles: function() {
		var s = this.selModel.getSelections();
		var list = [];
		for(var i = 0, len = s.length; i < len; i++){
			var data = s[i].data;
			data.id = s[i].id;
			list.push(data);
		}
		return list;
	},
	getOneSel: function() {
		var selection = this.selModel.getSelections();
		return selection[0];
	},
	countSelected: function() {
		return this.selModel.getCount();
	},
	getByPath: function(path) {
		var rowIdx = this.store.findBy(function(r) {
			if (r.data.path == path) {return true;}
		});
		return (rowIdx != -1) ? this.store.getAt(rowIdx) : false;
	},
	highlight: function(filename) {
		var rowIdx = this.store.findBy(function(record) {
			if (record.data.filename == filename) {return true;}
		});
		if (rowIdx > -1) {
			this.selModel.selectRow(rowIdx);
			this.getView().focusRow(rowIdx);
		}
	},
	load: function(path) {
		if (path == '/ROOT/TRASH') {
			this.store.proxy.conn.url = FR.baseURL+'/?module=trash&section=ajax&page=grid';
		} else if (path == '/ROOT/STARRED') {
			this.store.proxy.conn.url = FR.baseURL+'/?module=stars&page=grid';
		} else if (path == '/ROOT/PHOTOS') {
			this.store.proxy.conn.url = FR.baseURL+'/?module=media&section=photos&page=grid';
		} else if (path == '/ROOT/MUSIC') {
			this.store.proxy.conn.url = FR.baseURL+'/?module=media&section=music&page=grid';
		} else if (path == '/ROOT/WLINKED') {
			this.store.proxy.conn.url = FR.baseURL+'/?module=weblinks&section=ajax&page=grid';
		} else if (path == '/ROOT/SEARCH') {
			this.store.proxy.conn.url = FR.baseURL+'/?module=search&section=ajax&page=grid';
		} else {
			this.store.proxy.conn.url = FR.myfilesBaseURL+'&page=grid';
		}
		if (FR.pushState) {
			if (typeof window.history.pushState !== 'undefined') {
				window.history.pushState({path: path}, '', '#' + encodeURI(path.substring(5)));
			}
		}
		FR.pushState = true;
		this.loadParams.path = encodeURIComponent(path);
		this.store.load({params: this.loadParams});
		FR.currentPath = path;
	},
	onSelectionChange: new Ext.util.DelayedTask(function(){
		this.countSel = this.countSelected();
		if (this.countSel == 0) {
			FR.currentSelectedFile = false;
		} else  {
			if (this.countSel == 1) {
				FR.currentSelectedFile = this.getOneSel();
			}
		}
		FR.UI.infoPanel.gridSelChange();
		this.showTopMenu();
	}, this),
	showTopMenu: function() {
		var count=0;
		if (FR.isMobile) {
			count++;
		} else {
			Ext.iterate(['weblink', 'shareWithUsers', 'preview', 'remove', 'more', 'moreSep'], function (k) {
				FR.UI.actions[k].hide();
			});

			if (FR.currentSection == 'myfiles') {
				if (this.countSel > 0) {
					if (this.countSel == 1) {
						if (User.perms.download) {
							if (!FR.currentSelectedFile.data.isFolder) {
								FR.UI.actions.preview.show();
								count++;
							}
							if (User.perms.weblink) {
								FR.UI.actions.weblink.show();
								count++;
							}
							if (User.perms.share && FR.currentSelectedFile.data.isFolder) {
								FR.UI.actions.shareWithUsers.show();
								count++;
							}
						}
					}
					if (!User.perms.read_only) {
						FR.UI.actions.remove.show();
						count++;
					}
				}
			} else if (['webLinked', 'starred', 'photos'].indexOf(FR.currentSection) != -1) {
				if (this.countSel == 1) {
					if (User.perms.download) {
						if (!FR.currentSelectedFile.data.isFolder) {
							FR.UI.actions.preview.show();
							count++;
						}
					}
					FR.UI.actions.weblink.show();
					count++;
				}
			} else if (FR.currentSection == 'trash') {
				if (this.countSel > 0) {
					if (!User.perms.read_only) {
						FR.UI.actions.remove.show();
						count++;
					}
				}
			} else if (FR.currentSection == 'search') {
				if (this.countSel > 0) {
					count++;
				}
			} else if (FR.currentSection == 'sharedFolder') {
				var f = FR.currentFolderPerms;
				if (this.countSel == 1) {
					if (User.perms.download && f && f.download) {
						if (!FR.currentSelectedFile.data.isFolder) {
							FR.UI.actions.preview.show();
							count++;
						}
						if (f.share && User.perms.weblink) {
							FR.UI.actions.weblink.show();
							count++;
						}
						if (f.alter && !User.perms.read_only) {
							FR.UI.actions.remove.show();
							count++;
						}
					}
				}
			}
		}
		if (count > 0) {FR.UI.actions.more.show();if (!FR.isMobile) {FR.UI.actions.moreSep.show();}}
	},
	showContextMenu: function() {
		FR.UI.contextMenu.event({
			location: 'grid',
			target: FR.UI.gridPanel.getSelectedFiles()
		});
	},
	toggleView: function(dontSave) {
		var m = 'list';
		if (this.viewMode == 'list') {
			m = 'thumbnails';
		} else if (this.viewMode == 'thumbnails') {
			m = 'large';
		}
		this.changeView(m, dontSave);
	},
	changeView: function(viewMode, dontSave) {
		if (dontSave) {
			this.previousViewMode = this.viewMode;
		}
		FR.UI.actions.toggleViewList.setIconClass(FR.UI.getViewIconCls(viewMode));
		this.viewMode = viewMode;
		var v = this.getView();
		v.viewMode = this.viewMode;
		v.refresh(true);
		if (!dontSave) {
			Ext.state.Manager.getProvider().set('fr-view-mode', viewMode);
		}
	},
	restorePreviousView: function() {
		if (!FR.UI.gridPanel.previousViewMode) {return false;}
		this.changeView(FR.UI.gridPanel.previousViewMode, true);
		this.previousViewMode = false;
	},
	loadThumbs: new Ext.util.DelayedTask(function() {
		if (this.viewMode == 'list') {return false;}
		var scroller = this.view.scroller.dom;
		var scrollerRect = scroller.getBoundingClientRect();
		this.getStore().each(function(item) {
			if (!item.data.thumb) {return true;}
			if (item.data.thumbLoading) {return true;}
			if (item.data.thumbLoaded) {return true;}
			var idx = this.store.indexOfId(item.id);
			if (idx == -1) {return true;}
			var el = this.view.getRow(idx);
			if (!el) {return true;}
			if (!FR.utils.elementInView(el.getBoundingClientRect(), scrollerRect, 2)) {return true;}
			var elId = 'itemIcon_'+item.data.uniqid + item.data.filesize;
			var iconEl = Ext.get(elId);
			if (!iconEl) {return true;}
			var t = Ext.get(new Image());
			t.on('load', function() {
				item.data.thumbLoading = false;
				item.data.thumbLoaded = true;
				if (!iconEl.dom) {return false;}
				var bgSize = FR.UI.gridPanel.thumbBGSize(this.dom.width, this.dom.height);
				if (bgSize) {
					item.data.thumbBgSize = bgSize;
					iconEl.setStyle('background-size', bgSize);
				}
				iconEl.setStyle('background-image', 'url(\''+item.data.thumbURL+'\')');
				this.remove();
			});
			t.on('error', function() {
				item.data.thumbLoading = false;
				item.data.thumb = false;
			});
			item.data.thumbURL = FR.UI.getThumbURL(item.data);
			t.set({src: item.data.thumbURL});
			item.data.thumbLoading = true;
		}, this);
	}),
	thumbBGSize: function(w, h) {
		var m = Settings.thumbnail_size;
		if (w <= m || h <= m) {
			if (w <= m && h <= m) {
				return  w+'px'+' '+h+'px';
			} else {
				return 'contain';
			}
		}
	},
	filter: function(keyword) {
		FR.UI.gridPanel.getStore().filterBy(function(r){
			return (r.data.filename.toLowerCase().indexOf(keyword.toLowerCase()) != -1);
		}, this);
		this.loadThumbs.delay(300, false, this);
	},
	onRender: function() {
		FR.components.gridPanel.superclass.onRender.apply(this, arguments);
	}
});


FR.components.thumbGridStore = Ext.extend(Ext.data.Store, {
	applySort : function() {
		var sortInfo = this.sortInfo;
		if (FR.currentSection == 'photos') {
			sortInfo = {field: 'taken', direction: 'DESC'};
		}
		this.multiSortInfo = {
			sorters: [
				{field:'isFolder', direction: 'DESC'},
				{field: sortInfo.field, direction: sortInfo.direction}
			],
			direction: sortInfo.direction
		};
		this.hasMultiSort = true;
		FR.components.thumbGridStore.superclass.applySort.apply(this, arguments);
	}
});


FR.components.thumbGridView = Ext.extend(Ext.grid.GridView, {
	onRowSelect : function(row){
		this.addRowClass(row, (this.viewMode == 'list')?"x-grid3-row-selected":"tmbItemSel");
	},
	onRowDeselect : function(row){
		this.removeRowClass(row, (this.viewMode == 'list')?"x-grid3-row-selected":"tmbItemSel");
	},
	prepareData : function(r) {
		var icons = [];
		if (r.data.isNew) {
			icons.push('<i class="fa fa-asterisk icon-red" ext:qtip="'+FR.T('This file was created or modified since your last access.')+'"></i>');
		}
		if (r.data.hasWebLink) {
			icons.push('<i class="fa fa-link"></i>');
		}
		if (r.data.share) {
			icons.push('<i class="fa fa-user-plus"></i>');
		}
		if (r.data.comments == 1) {
			icons.push('<i class="fa fa-comment-o"></i>');
		} else if (r.data.comments > 1) {
			icons.push('<i class="fa fa-comments-o"></i>');
		}
		if (r.data.lockInfo) {
			icons.push('<i class="fa fa-lock" ext:qtip="'+FR.T('This file is locked by %1.').replace('%1', r.data.lockInfo)+'"></i>');
		}
		if (r.data.star) {
			icons.push('<i class="fa fa-star-o"></i>');
		}
		if (r.data.notInfo) {
			icons.push(' <i class="fa fa-bell-o"></i>');
		}
		r.data.icons = icons.join('');
		var iconsHolder = '<div class="iconsHolder">'+r.data.icons+'</div>';
		var filename = r.data.filename;
		if (!r.data.isFolder) {
			var name = FR.utils.stripFileExtension(r.data.filename);
			var ext = FR.utils.getFileExtension(r.data.filename).toUpperCase();
			r.data.filenameHTML = name +'<span class="ext-list">'+ ext +'</span>';
		} else {
			r.data.filenameHTML = filename;
		}

		if (this.viewMode == 'list') {
			if (r.data.isFolder) {
				r.data.iconHTML = '<i class="fa fa-folder fa-fw fileIcon"></i>';
			} else {
				r.data.iconHTML = '<img src="images/fico/'+r.data.icon+'" width="32" height="32" align="absmiddle" />';
			}
		} else {
			if (r.data.isFolder) {
				return '' +
					'<table cellspacing="0" cellpadding="0" border="0" class="thumbFolder"><tr>' +
					'<td class="thumb"><i class="fa fa-folder fa-lg fa-fw"></i></td>' +
					'<td class="filename"><div class="wrap">' +
					'<span ext:qtip="'+r.data.filename+'">' + r.data.filename + '</span>' + iconsHolder +
					'</div></td>' +
					'</tr></table>';
			} else {
				var img = '';
				if (r.data.thumbURL) {
					img = r.data.thumbURL;
				} else {
					img = 'images/fico/'+r.data.icon;
				}
				var itemId = 'itemIcon_'+ r.data.uniqid + r.data.filesize;
				var iconStyle = 'background-image:url(\''+img+'\')';
				if (r.data.thumbBgSize) {
					iconStyle += ';background-size:'+r.data.thumbBgSize;
				}
				r.data.labelHTML = '';
				if (r.data.label) {
					var s = r.data.label.split('|');
					r.data.labelHTML = '<div style="position:relative"><span class="FRLabel" style="position:absolute;top:5px;left:-5px;background-color:'+s[1]+'">'+s[0]+'</span></div>';
				}
				if (this.viewMode == 'thumbnails') {
					return '<div class="tmbInner" id="'+itemId+'" style="'+iconStyle+'">' +
						r.data.labelHTML +
						'</div>' +
						'<div class="title">' +
						'<div class="name" ext:qtip="'+r.data.filename+'&lt;br&gt; '+r.data.nice_filesize+'" style="max-width:'+(Settings.thumbnail_size-44)+'px;">'+r.data.filenameHTML+'</div>' +
						iconsHolder +
						'</div>';
				} else {
					return '<div class="tmbInner large" id="'+itemId+'" style="'+iconStyle+'" ext:qtip="'+r.data.filename+'">'+r.data.labelHTML+'</div>';
				}
			}
		}
	},
	getRows : function() {
		return this.hasRows() ? this.mainBody.query(this.rowSelector) : [];
	},
	doRender : function(cs, rs, ds, startRow, colCount, stripe) {
		if (this.viewMode == 'list') {
			Ext.each(rs, function(r) {this.prepareData(r);}, this);
			return FR.components.thumbGridView.superclass.doRender.apply(this, arguments);
		}
		var buf1 = '';
		var buf2 = '';
		var cls = this.viewMode == 'thumbnails' ? 'tmbItem' : 'tmbItem largeItem';
		Ext.each(rs, function(r) {
			if (r.data.isFolder) {
				buf1 += '<div class="tmbItem typeFolder x-unselectable">'+this.prepareData(r)+'</div>';
			} else {
				buf2 += '<div class="'+cls+' x-unselectable">'+this.prepareData(r)+'</div>';
			}
		}, this);
		return buf1 + '<div style="clear:both"></div>' + buf2 + '<div style="clear:both"></div>';
	},
	refresh: function(headersToo) {
		if (this.viewMode == 'list') {
			this.el.removeClass('thumbMode');
			this.rowSelector = 'div.x-grid3-row';
			this.mainHd.setStyle('display', 'block');
		} else {
			this.rowSelector = 'div.tmbItem';
			this.el.addClass('thumbMode');
			this.mainHd.setStyle('display', 'none');
		}
		FR.components.thumbGridView.superclass.refresh.apply(this, arguments);
		this.grid.loadThumbs.delay(0, false, this.grid);
	},
	updateAllColumnWidths : function(){
		if (this.viewMode == 'list') {
			return FR.components.thumbGridView.superclass.updateAllColumnWidths.apply(this);
		}
		var tw = this.getTotalWidth();
		var clen = this.cm.getColumnCount();
		var ws = [];
		for(var i = 0; i < clen; i++){
			ws[i] = this.getColumnWidth(i);
		}
		this.innerHd.firstChild.firstChild.style.width = tw;
		for(i = 0; i < clen; i++){
			var hd = this.getHeaderCell(i);
			hd.style.width = ws[i];
		}
		this.onAllColumnWidthsUpdated(ws, tw);
	},
	updateColumnWidth : function(col, width){
		if (this.viewMode == 'list') {
			return FR.components.thumbGridView.superclass.updateColumnWidth.apply(this, arguments);
		}
		var w = this.getColumnWidth(col);
		var tw = this.getTotalWidth();
		this.innerHd.firstChild.firstChild.style.width = tw;
		var hd = this.getHeaderCell(col);
		hd.style.width = w;
		this.onColumnWidthUpdated(col, w, tw);
	},
	updateColumnHidden : function(col, hidden){
		if (this.viewMode == 'list') {
			return FR.components.thumbGridView.superclass.updateColumnHidden.apply(this, arguments);
		}
		var tw = this.getTotalWidth();
		this.innerHd.firstChild.firstChild.style.width = tw;
		var display = hidden ? 'none' : '';
		var hd = this.getHeaderCell(col);
		hd.style.display = display;
		this.onColumnHiddenUpdated(col, hidden, tw);
		delete this.lastViewWidth;
		this.layout();
	},
	applyEmptyText : function() {
		var t;
		if (FR.utils.currentFolderAllowsUpload() && !FR.isMobile) {
			t = '<div class="dropIcon"><i class="fa fa-reply fa-rotate-270 fa-5x"></i></div>' +
				'<div style="float:left;">' +
				'<div style="font-size:26px;">' + FR.T('Drop files here') + '</div>' +
				'<div style="font-size:13px;margin-top:10px;">' + FR.T('or use the red "New" button') + '</div>' +
				'</div>';
		} else {
			t = 'There are no files in this folder.';
			if (FR.currentSection == 'starred') {
				t = 'Add stars to files to easily find them later.';
			} else if (FR.currentSection == 'webLinked') {
				t = 'You have not shared any file through web links.';
			} else if (FR.currentSection == 'search') {
				t = 'No file was found matching your search criteria.'
			}
			t = '<div style="font-size:26px;">' + FR.T(t) + '</div>';
		}
		this.emptyText = t;
		FR.components.thumbGridView.superclass.applyEmptyText.apply(this, arguments);
	},
	layout: function () {
		FR.components.thumbGridView.superclass.layout.apply(this);
		this.mainBody.setStyle('width', 'auto');
		this.mainBody.setStyle('height', '100%');
	}
});