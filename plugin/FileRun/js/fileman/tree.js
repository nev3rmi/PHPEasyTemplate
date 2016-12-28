FR.initTree = function() {
	var t = new Ext.tree.TreePanel({
		id: 'FR-Tree-Panel', region: 'center',
		enableDD: !User.perms.read_only, ddGroup: 'TreeDD', dropConfig: {appendOnly:true}, bodyStyle: 'padding-top:1px;padding-bottom:30px;',
		animate: true, autoScroll: true, rootVisible: false, lines: false, useArrows: true,
		listeners: {
			'afterrender': function () {
				if (User.perms.upload) {
					FlowUtils.DropZoneManager.add({
						domNode: FR.UI.tree.panel.el.dom, overClass: 'x-tree-drag-append',
						findTarget: function (e) {
							var n,
								el = Ext.get(e.target);
							if (el && !el.hasClass('x-tree-node-el')) {el = el.parent('div.x-tree-node-el');}
							if (!el) {return false;}
							var treeNodeId = el.getAttribute('tree-node-id', 'ext');
							if (!treeNodeId) {return false;}
							var treeNode = FR.UI.tree.panel.getNodeById(treeNodeId);
							if (!treeNode) {return false;}
							if (['myfiles', 'sharedFolder'].indexOf(treeNode.attributes.section) != -1) {
								if (!treeNode.attributes.perms || treeNode.attributes.perms.upload) {
									return {el: el.dom, node: treeNode};
								}
							}
						},
						onDrop: function (e, target) {
							var up = new FR.components.uploadPanel({
								targetPath: target.node.getPath('pathname'), dropEvent: e
							});
							FR.UI.uploadWindow(FR.T('Upload to "%1"').replace('%1', target.node.text), up);
						},
						scope: this
					});
				}

				if (User.perms.alter) {
					FR.UI.tree.panel.on('nodedragover', function (e) {
						if (FR.currentSection == 'trash' || FR.currentSection == 'starred' || FR.currentSection == 'search' || FR.currentSection == 'webLinked' ||
							(e.dropNode && e.dropNode.attributes.readonly) ||
							(e.target.attributes.perms && (!e.target.attributes.perms.alter && !e.target.attributes.perms.upload)) ||
							(FR.currentPath == e.target.getPath('pathname'))
						) {
							e.cancel = true;
							return false;
						}
					});

					FR.UI.tree.panel.on('beforenodedrop', function (drop) {
						FR.actions.move(drop, drop.target.getPath('pathname'));
						return false;
					});
				}
			}, scope: this
		}
	});
	FR.UI.tree.panel = t;

	var ui = FR.components.TreeNodeCustomUI;
	var r = new Ext.tree.TreeNode({pathname: 'ROOT', allowDrag: false, allowDrop: false});
	t.setRootNode(r);
	FR.UI.tree.root = r;
	
	t.getSelectionModel().on('selectionchange', function(selectionModel, treeNode) {
		FR.UI.tree.onSelectionChange(selectionModel, treeNode);
	});
	t.getSelectionModel().on('beforeselect', function(selectionModel, treeNode) {
		if (treeNode.attributes.section == 'userWithShares') {treeNode.expand();if (treeNode.loaded) {treeNode.firstChild.select();}return false;}
		if (!treeNode.attributes.pathname) {return false;}
	});
	FR.UI.tree.loader = new Ext.tree.TreeLoader({
		dataUrl: this.myfilesBaseURL+'&page=tree',
		baseAttrs: {uiProvider: ui},
		listeners: {'beforeload': function(loader, node){loader.baseParams.path = node.getPath('pathname');}}
	});

	FR.UI.tree.searchResultsNode = new Ext.tree.TreeNode({
		text: FR.T('Search Results'), readonly: true, uiProvider: ui,
		leaf: false, allowDrag: false, allowDrop: false, hidden: true,
		iconCls: 'fa-search icon-gray', pathname: 'SEARCH', section: 'search'
	});
	r.appendChild(FR.UI.tree.searchResultsNode);
	FR.UI.tree.homeFolderNode = new Ext.tree.AsyncTreeNode({
		text: FR.T('My Files'), pathname: 'HOME', section: 'myfiles',
		iconCls: 'fa-folder', homefolder: true,
		allowDrag: false, allowDrop: !User.perms.read_only,
		custom: FR.homeFolderCfg.customAttr,
		loader: FR.UI.tree.loader,
		uiProvider: ui
	});
	r.appendChild(FR.UI.tree.homeFolderNode);

	r.appendChild(new Ext.tree.TreeNode({
		text: FR.T('Photos'), pathname: 'PHOTOS', section: 'photos',
		iconCls: 'fa-picture-o', uiProvider: ui, hidden: Settings.hidePhotos,
		leaf: false, allowDrag: false, allowDrop: false, readonly: true
	}));
	//text: FR.T('Music'), pathname: 'MUSIC', section: 'music', iconCls: 'fa-headphones',

	FR.UI.tree.sharesLoader = new Ext.tree.TreeLoader({
		dataUrl:FR.myfilesBaseURL+'&page=tree_shares&nocache=1',
		baseAttrs: {uiProvider: ui},
		listeners: {'beforeload': function(loader, node){loader.baseParams.path = node.getPath('pathname');}}
	});
	Ext.each(AnonShares, function(fld) {
		r.appendChild(new Ext.tree.AsyncTreeNode(Ext.apply(fld, {
			readonly: true, allowDrag: false, allowDrop: fld.perms.upload,
			loader: FR.UI.tree.sharesLoader, section: 'sharedFolder', uiProvider: ui
		})));
	});
	Ext.each(Sharing, function(usr) {
		r.appendChild(new Ext.tree.AsyncTreeNode({
			text: usr.name, pathname: usr.id, section: 'userWithShares',
			uid: usr.id, iconCls: 'avatar',
			allowDrag: false, allowDrop: false,
			loader: FR.UI.tree.sharesLoader, uiProvider: ui
		}));
	});
	FR.UI.tree.starredNode = new Ext.tree.TreeNode({
		text: FR.T('Starred'), readonly: true, uiProvider: ui,
		leaf: false, allowDrag: false, allowDrop: false, hidden: User.perms.read_only,
		iconCls: 'fa-star icon-gray', pathname: 'STARRED', section: 'starred'
	});
	r.appendChild(FR.UI.tree.starredNode);
	FR.UI.tree.webLinksNode = new Ext.tree.TreeNode({
		text: FR.T('Shared links'), readonly: true, uiProvider: ui,
		leaf: false, allowDrag: false, allowDrop: false, hidden: !User.perms.weblink,
		iconCls: 'fa-link icon-gray', pathname: 'WLINKED', section: 'webLinked'
	});
	r.appendChild(FR.UI.tree.webLinksNode);
	FR.UI.tree.trashNode = new Ext.tree.TreeNode({
		text: FR.T('Trash'), readonly: true, uiProvider: ui,
		leaf: false, allowDrag: false, allowDrop: false,
		iconCls: 'fa-trash icon-gray', pathname: 'TRASH', section: 'trash'
	});
	r.appendChild(FR.UI.tree.trashNode);
	if (!User.trashCount || User.perms.read_only) {
		FR.UI.tree.trashNode.getUI().hide();
	}

	FR.UI.tree.getCurrentPath = function() {
		return this.currentSelectedNode.getPath('pathname');
	};

	FR.UI.tree.onSelectionChange = function(selectionModel, treeNode) {
		FR.UI.tree.currentSelectedNode = treeNode;
		if (!treeNode) {return false;}
		var path = treeNode.getPath('pathname');
		if (path != FR.currentPath) {
            var section = treeNode.attributes.section;
			FR.UI.gridPanel.load(path);
			FR.currentFolderPerms = treeNode.attributes.perms ? treeNode.attributes.perms : false;
			FR.currentSection = treeNode.attributes.section;
			if (section == 'myfiles' || section == "sharedFolder") {
				treeNode.expand();
				FR.UI.actions.searchField.setSearchFolder(FR.currentPath, treeNode.text);
				if (FR.UI.gridPanel.dropZone) {
					FR.UI.gridPanel.dropZone.unlock();
				}
			} else {
				if (FR.UI.gridPanel.dropZone) {
					FR.UI.gridPanel.dropZone.lock();
				}
				FR.UI.actions.searchField.setSearchFolder('/ROOT/HOME', FR.T('My Files'));
			}
			if (section == 'search') {
				FR.UI.tree.searchResultsNode.getUI().show();
				FR.UI.tree.searchResultsNode.ensureVisible();
			}
			if (section == 'photos') {
				FR.UI.gridPanel.changeView('large', true);
			} else {
				FR.UI.gridPanel.restorePreviousView();
			}
			var gridCM = FR.UI.gridPanel.getColumnModel();
			gridCM.setHidden(gridCM.getIndexById('path'), (['starred', 'webLinked', 'search'].indexOf(section) == -1));
			gridCM.setHidden(gridCM.getIndexById('trash_deleted_from'), (section != 'trash'));
		}
	};

	FR.UI.tree.panel.on('contextmenu', function(node, e) {FR.UI.tree.showContextMenu(node,e);});
};

FR.UI.tree.reloadNode = function(treeNode, callback) {
	treeNode.loaded = false;
	treeNode.collapse();
	treeNode.expand(false, true, callback);
};

FR.UI.tree.updateIcon = function(treeNode) {
	treeNode.getUI().updateIcons();
};

FR.UI.tree.showContextMenu = function(node, e) {
	FR.UI.contextMenu.event({
		location: 'tree',
		target: node.attributes
	});
	if (e) {e.stopEvent();}
	return false;
};

FR.components.TreeNodeCustomUI = Ext.extend(Ext.tree.TreeNodeUI, {
	getIcons: function() {
		var n = this.node;
		var icons = '';
		if (n.attributes.custom) {
			if (n.attributes.custom.share) {
				icons += ' <i class="fa fa-user-plus"></i>';
			}
			if (n.attributes.custom.weblink) {
				icons += ' <i class="fa fa-link"></i>';
			}
			if (n.attributes.custom.sync) {
				icons += ' <i class="fa fa-retweet"></i>';
			}
			if (n.attributes.custom.notInfo) {
				icons += ' <i class="fa fa-bell-o"></i>';
			}
			if (n.attributes.custom.star) {
				icons += ' <i class="fa fa-star-o"></i>';
			}
		}
		if (n.attributes['new']) {
			icons += ' <i class="fa fa-asterisk icon-red"></i>';
		}
		return icons;
	},
	updateIcons: function() {
		this.frIconsNodeEl.update(this.getIcons());
	},
	renderElements : function(n, a, targetNode, bulkRender){
		this.indentMarkup = n.parentNode ? n.parentNode.ui.getChildIndent() : '';
		var showMenuTrigger = (n.attributes.section == 'myfiles' || n.attributes.section == 'sharedFolder' || n.attributes.section == 'trash');
		var icons = this.getIcons();
		var nel,
			buf =
			'<li class="x-tree-node">' +
				'<div ext:tree-node-id="'+n.id+'" class="x-tree-node-el x-tree-node-leaf x-unselectable '+(a.cls || '')+'" unselectable="on">' +
					'<span class="x-tree-node-indent">'+this.indentMarkup+"</span>"+
					'<i class="x-tree-ec-icon fa fa-caret-right"></i>'+
					'<i '+(a.uid ?
						'style="background-image:url(\'a/?uid='+a.uid+'\')" class="avatar"' :
						'class="x-tree-node-icon fa fa-lg fa-fw icon-silver '+(a.iconCls || "fa-folder")+'"'
					)+' unselectable="on"></i>'+
					'<a hidefocus="on" class="x-tree-node-anchor">' +
						'<span unselectable="on">'+n.text+"</span>" +
						'<span style="color:silver">' + icons + '</span>' +
					"</a>" +
				(showMenuTrigger ? '<div class="nodeMenu"><div class="triangle"></div></div>':'') +
				'</div>'+
				'<ul class="x-tree-node-ct" style="display:none;"></ul>'+
			'</li>';

		if (bulkRender !== true && n.nextSibling && (nel = n.nextSibling.ui.getEl())) {
			this.wrap = Ext.DomHelper.insertHtml("beforeBegin", nel, buf);
		} else {
			this.wrap = Ext.DomHelper.insertHtml("beforeEnd", targetNode, buf);
		}
		this.elNode = this.wrap.childNodes[0];
		this.ctNode = this.wrap.childNodes[1];
		var cs = this.elNode.childNodes;
		this.indentNode = cs[0];
		this.ecNode = cs[1];
		this.iconNode = cs[2];
		this.iconNodeEl = Ext.get(this.iconNode);
		this.anchor = cs[3];
		this.textNode = cs[3].firstChild;
		this.frIconsNodeEl = Ext.get(cs[3].lastChild);
		if (showMenuTrigger) {
			this.menuTriggerNode = cs[4];
			Ext.get(this.menuTriggerNode).on('click', function(e) {
				FR.UI.tree.showContextMenu(n, e);
				e.stopEvent();
			});
		}
	},
	updateExpandIcon : function(){
		if(this.rendered){
			var n = this.node,hasChild = n.hasChildNodes();
			if(hasChild || n.attributes.expandable){
				if (n.expanded){
					Ext.fly(this.ecNode).replaceClass('fa-caret-right', 'fa-caret-down');
				} else {
					Ext.fly(this.ecNode).replaceClass('fa-caret-down', 'fa-caret-right');
				}
			} else {
				Ext.fly(this.ecNode).removeClass('fa-caret-right');
			}
		}
	},
	beforeLoad : function() {
		var i = this.iconNodeEl;
		i.origCSSClass = i.getAttribute('class');
		i.set({'class': 'x-tree-node-icon fa fa-lg fa-fw icon-silver fa-refresh fa-spin'});
	},
	afterLoad : function() {
		this.iconNodeEl.set({'class': this.iconNodeEl.origCSSClass});
	}
});