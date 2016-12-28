FR.UI.reloadStatusBar = function() {
	if (!User.perms.space_quota_max) {return false;}
	FR.UI.quotaIndicator.getEl().mask();
	Ext.Ajax.request({
		url: FR.baseURL+'/?module=fileman&section=utils&page=status_bar',
		success: function(req) {
			FR.UI.quotaIndicator.getEl().unmask();
			try {
				var rs = Ext.util.JSON.decode(req.responseText);
			} catch (er){return false;}
			if (rs) {
				User.perms = Ext.apply(User.perms, rs);
				FR.UI.updateQuotaStatus();
			}
		}
	});
};
FR.UI.updateQuotaStatus = function() {
	FR.UI.quotaIndicator.update('%1 used (%2%)'.replace('%1', User.perms.space_quota_used).replace('%2', User.perms.space_quota_percent_used));
};
FR.UI.getViewIconCls = function(viewMode) {
	if (!viewMode) {viewMode = Settings.ui_default_view;}
	var icon = 'fa fa-fw ';
	if (viewMode == 'large') {
		icon += 'fa-list';
	} else if (viewMode == 'thumbnails') {
		icon += 'fa-th-large';
	} else {
		icon += 'fa-th';
	}
	return icon;
};
FR.UI.feedback = function(text) {
	if (!text) {return false;}
	if (Ext.util.Format.stripTags(text).length > 100) {
		new Ext.Window({width: 350, height: 160, layout: 'fit', items: {bodyStyle: 'padding:5px', html: text, autoScroll: true}}).show();
		return false;
	}
	var delay = Math.max(text.length/15, 2);
	if (!FR.UI.feedbackCt) {
		FR.UI.feedbackCt = Ext.DomHelper.append(document.body, {style:'position:absolute;width:300px;z-index:9999999;'}, true);
	}
	var m = Ext.DomHelper.append(FR.UI.feedbackCt, {html:'<div class="fr-feedback-msg">'+text+'</div>'}, true);
	FR.UI.feedbackCt.alignTo(Ext.getBody(), 't-t');
	m.on('click', function(e, node) {node.remove();});
	m.slideIn('t').pause(delay).ghost('b', {remove: true});
};
FR.UI.popupCount = 0;
FR.UI.popup = function(args) {
	var id;
	if (args.id) {
		id = args.id;
	} else {
		args.autoDestroy = true;
		id = 'popups_'+(++this.popupCount);
	}
	var frameId = id+'-iframe';
	if (!args.noId) {
		args.src += '&_popup_id='+id;
	}
	var w = args.width;
	var h = args.height;
	if (!w || !h) {
		var bsize = Ext.getBody().getSize();
		var gutter = 10;
		if (FR.isMobile) {gutter = 1;}
		w = Math.floor(bsize.width - gutter / 100 * bsize.width);
		h = Math.floor(bsize.height - gutter / 100 * bsize.height);
	}
	var dialog = new Ext.Window({
		id: id, stateful: false,
		autoDestroy: args.autoDestroy,
		closeAction: (args.autoDestroy ? 'close' : 'hide'),
		constrainHeader: true, layout: 'fit',
		width: w, height: h,
		title: args.title || false,
		tools: args.tools || false,
		resizable: args.resizable || false,
		collapsible: args.collapsible || false,
		maximizable: args.maximizable || false,
		maximized: args.maximized || false,
		constrain: args.constrain || false,
		iconCls: args.iconCls || false,
		closable: ((typeof args.closable == 'undefined') ? true : args.closable), plain: false, modal: (args.modal !== false),
		html: '<div style="background-color:white;height:100%;"><iframe src="'+(args.post ? 'about:blank' : args.src)+'" style="width:100%;height:100%;position:relative" marginheight="0" marginwidth="0" frameborder="0" id="'+frameId+'" name="'+id+'" allowfullscreen></iframe></div>'
	});
	dialog.frameId = frameId;
	args.centerTo = args.centerTo || false;
	dialog.show(args.centerTo);
	if (args.align) {
		dialog.alignTo(args.align.el, args.align.pos, args.align.offset);
	}
	if (args.loadingMsg) {
		Ext.get(dialog.getLayout().container.body.dom).mask(FR.T(args.loadingMsg)).addClass('lightMask');
		Ext.get(frameId).on('load', function() {
			Ext.get(dialog.getLayout().container.body.dom).unmask();
		});
	}
	FR.UI.popups[id] = dialog;
	if (args.autoDestroy) {
		dialog.on('afterhide', function(dlg){
			dlg.destroy(true);
			delete FR.UI.popups[dlg.id];
		});
	}
	if (args.post) {
		FR.UI.postToTarget(args, id);
	}
	return dialog;
};

FR.UI.openInPopup = function(args) {
	var id = 'fr-tabs-'+Ext.id();
	var frameId = id+'-frame';
	args.src = args.src+'&_tab_id='+id;

	var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
	var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
	var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
	var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
	var w = Math.round(width-15/100*width);
	var h = Math.round(height-15/100*height);
	var left = ((width / 2) - (w / 2)) + dualScreenLeft;
	var top = ((height / 2) - (h / 2)) + dualScreenTop;
	var win = window.open(args.src, frameId, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
	if (args.post) {
		FR.UI.postToTarget(args, frameId);
	}
	return win;
};

FR.UI.backgroundPost = function(path, url) {
	if (!path) {
		var item = FR.UI.contextMenu.target[0];
		var path = item.path ? item.path : FR.currentPath+'/'+item.filename;
	}
	var args = {
		src: url,
		post: [{name: 'path', value: path}]
	};
	FR.UI.postToTarget(args, FR.UI.createDisposableIFrame());
}

FR.UI.createDisposableIFrame = function() {
	var ifr = document.createElement('IFRAME');
	var iframeName = 'hidden-iframe-'+Ext.id();
	ifr.setAttribute('name', iframeName);
	ifr.setAttribute('style', 'width:0px;height:0px;position:absolute;top:-100px;left:-100px;');
	Ext.get('theBODY').appendChild(ifr);
	Ext.get(ifr).on('load', function() {
		Ext.get(ifr).remove();
	});
	return iframeName;
}

FR.UI.postToTarget = function(args, target) {
	var frm = document.createElement('FORM');
	frm.action = args.src;
	frm.method = 'POST';
	frm.target = target;
	Ext.each(args.post, function(param) {
		var inpt = document.createElement('INPUT');
		inpt.type = 'hidden';
		inpt.name = param.name;
		inpt.value = encodeURIComponent(param.value);
		frm.appendChild(inpt);
	});
	Ext.get('theBODY').appendChild(frm);
	frm.submit();
	Ext.get(frm).remove();
};


FR.UI.showUploadForm = function(type) {
	if (FR.currentFolderPerms && !FR.currentFolderPerms.upload) {
		FR.UI.feedback(FR.T("You are not allowed to upload files in this folder."));
		return false;
	}
	var useFlow = false;

	if (type == 'folder' && FlowUtils.browserSupport.folders) {
		useFlow = true;
	} else if (type == 'files') {
		if (FlowUtils.browserSupport.files) {
			useFlow = true;
		}
	}
	var targetTreeNode = FR.UI.tree.currentSelectedNode;
	if (!FR.utils.currentFolderAllowsUpload()) {
		targetTreeNode = FR.UI.tree.homeFolderNode;
	}
	var targetPath = targetTreeNode.getPath('pathname');
	var title = FR.T('Upload to "%1"').replace('%1', targetTreeNode.text);
	if (!useFlow) {
		FR.UI.feedback('For an improved experience, we recommend you to use a <a href="%1" target="_blank">modern web browser</a>.'.replace('%1', 'http://whatbrowser.org'));
		return false;
	}

	FlowUtils.browseFiles({
		entireFolder: (type == 'folder'),
		onSelect: function(files, e) {
			if (files.length > 0) {
				var up = new FR.components.uploadPanel({
					closable: false, targetPath: targetPath, files: files
				});
				FR.UI.uploadWindow(title, up);
			}
		}, scope: this
	});
	return false;

};
FR.UI.uploadWindow = function(title, uploader) {
	var bsize = Ext.getBody().getSize();
	var h = Ext.min([Math.floor(bsize.height-20/100*bsize.height), 300]);
	var width = Ext.min([bsize.width, 800]);
	var w = new Ext.Window({
		title: FR.T('Upload to "%1"').replace('%1', FR.UI.tree.currentSelectedNode.text),
		items: uploader, width: width, height: h, closable: false, modal: true, layout: 'fit'
	});
	uploader.window = w;
	w.show();
};
FR.UI.persistentWindow = function(args) {
	var win = FR.UI.popups[args.id];
	if (!win) {
		win = FR.UI.popup(args);
		Ext.get(win.getLayout().container.body.dom).mask(args.initMsg).addClass('lightMask');
	} else {
		win.setTitle(args.title);
		win.syncSize();
		win.show();
		Ext.get(win.frameId).dom.contentWindow.FR.update(args);
	}
};
FR.UI.showLoading = function(msg, onlyTreePane) {
	if (onlyTreePane) {
		FR.UI.tree.panel.el.mask(msg);
	} else {
		FR.UI.window.getEl().mask(msg);
	}
};
FR.UI.doneLoading = function() {
	FR.UI.tree.panel.el.unmask();
	FR.UI.window.getEl().unmask();
};
FR.UI.tooltip = function(html) {
	return function() {
		new Ext.ToolTip({
			target: this.getEl(), showDelay: 250,
			html: html, anchor: 'top',
			baseCls: 'headerTbar-btn-tooltip'
		});
	}
};
FR.UI.getTextLogo = function(text) {
	var logoCls = 'logo3d';
	if (Settings.ui_logo_link_url) {
		text = '<a href="'+Settings.ui_logo_link_url+'" draggable="false">'+text+'</a>';
	} else {
		logoCls += ' unselectable';
	}
	return '<div class="'+logoCls+'" unselectable="on">'+text+'</div>';
};
FR.UI.getImageLogo = function(URL) {
	var html = '<img src="'+URL+'" border="0" alt="" draggable="false" />';
	var cls = '';
	if (Settings.ui_logo_link_url) {
		html = '<a href="'+Settings.ui_logo_link_url+'" draggable="false">'+html+'</a>';
	} else {
		cls = 'unselectable';
	}
	return '<div id="logoContainer" class="'+cls+'" draggable="false">'+html+'</div>';
};

FR.components.SearchBox = Ext.extend(Ext.form.ComboBox, {
	searchParams: {}, searchPath: false,
	initComponent: function() {
		Ext.apply(this, {
			ctCls:'search-field', minChars: 2, queryParam: 'keyword', listWidth: 258,
			emptyText: FR.T('Search')+' '+FR.T('My Files'), itemSelector: 'div.fr-search-field-item',
			listClass: 'fr-search-list', hideTrigger: true, tpl: new Ext.XTemplate(
				'<tpl for=".">' +
					'<div class="fr-search-field-item" title="{path}">' +
					'<div style="float:left;">' +
						'<div class="ico fr-thumbnail" style="background-image:url(\'{[this.getIcon(values)]}\')"></div>' +
						'<div class="filename">{filename}</div>' +
					'</div>' +
					'<div class="size">{nice_filesize}</div><div style="clear:both;"></div>' +
					'</div>' +
				'</tpl>', {
				getIcon: function(values) {
					if (values.isFolder) {
						return 'images/fico/folder-gray.png';
					} else {
						if (values.hasThumb) {
							return FR.UI.getThumbURL({path: values.path+'/'+values.filename, extra: 'width=100&height=100&exactSize=1'});
						} else {
							return 'images/fico/'+values.icon;
						}
					}
				}}),
			store: new Ext.data.JsonStore({
				searchBox: this,
				url: FR.baseURL+'/?module=search&section=ajax&page=quicksearch',
				root: 'files', idProperty: 'id',
					fields: [
					{name: 'id', mapping: 'id'},
					{name: 'filename', mapping: 'n'},
					{name: 'isFolder', mapping: 'dir'},
					{name: 'hasThumb', mapping: 'th'},
					{name: 'path', mapping: 'p'},
					{name: 'nice_filesize', mapping: 'ns'},
					{name: 'icon', mapping: 'i'}
				],
				listeners: {
					'beforeload': function() {
						this.setBaseParam('path', this.searchBox.searchPath);
						this.searchBox.showMoreBtn.hide();
					},
					'load': function() {
						this.searchBox.showMoreBtn.show();
					}
				}
			}),
			valueField: 'n', displayField: 'n',
			listeners: {
				'select': function(c, r) {this.customReset();FR.utils.locateItem(r.data.path, r.data.filename);},
				'render': function() {this.el.removeClass('x-form-text');},
				'keyup': function() {FR.UI.gridPanel.filter(this.getRawValue());}
			}}
		);
		FR.components.SearchBox.superclass.initComponent.apply(this, arguments);
	},
	doSearch: function(searchType) {
		if (searchType) {this.searchParams.searchType = searchType;}
		this.searchParams.keyword = this.getRawValue();
		this.searchParams.searchPath = this.searchPath;
		FR.UI.gridPanel.loadParams = this.searchParams;

		var title = FR.T('Search results');
		if (this.searchParams.keyword) {
			title = this.searchParams.keyword;
		}
		FR.UI.tree.searchResultsNode.setText(title);
		if (FR.currentSection == 'search') {
			FR.utils.reloadGrid();
		} else {
			FR.UI.tree.searchResultsNode.select();
		}
		this.customReset();
	},
	customReset: function() {
		this.reset();
		this.clearValue();
		this.lastQuery = false;
		this.hasFocus = false;
		this.postBlur();
		FR.UI.gridPanel.getStore().clearFilter();
	},
	initList: function() {
		FR.components.SearchBox.superclass.initList.apply(this, arguments);
		this.extr = this.list.createChild({});
		this.showMoreBtn = new Ext.Toolbar.Button({
			text: FR.T('Show more...'), cls:'fr-btn-smaller',
			handler: function(){this.searchParams.searchType = 'filename';this.doSearch();}, scope: this
		});
		new Ext.Toolbar({
			renderTo: this.extr, style: 'padding:3px',
			items: ['&nbsp;', '->', this.showMoreBtn]
		});
		this.assetHeight += this.extr.getHeight();
	},
	setSearchFolder: function(path, text) {
		this.blur();
		this.searchPath = path;
		this.setRawValue();
		this.emptyText = FR.T('Search')+' '+text;
		this.applyEmptyText();
	}
});

FR.UI.getThumbURL = function(itemData) {
	var path = (itemData.path || FR.currentPath+'/'+itemData.filename);
	var url = FR.baseURL+'/t.php?sn=1&g=cover&p='+FR.utils.encodeURIComponent(path);
	if (itemData.filesize) {
		url += '&s='+itemData.filesize;
	}
	if (itemData.modified) {
		var timestamp = new Date(itemData.modified).getTime()/1000;
		url += '&t='+timestamp;
	}
	if (itemData.extra) {
		url += '&'+itemData.extra;
	}
	return url;
};