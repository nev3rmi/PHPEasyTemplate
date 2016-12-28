FR.UI.imagePreview = {
	UI: {}, items: new Ext.util.MixedCollection(),
	highDPIRatio: 2,
	blankSrc: 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==',
	init: function(clickedItem) {
		this.open = true;
		this.baseSrc = URLRoot+'/t.php';
		this.items.clear();
		if (!this.body) {
			this.body = Ext.getBody();
		}
		this.showMask();
		var startIndex = this.collectItems(clickedItem);
		this.createImageDOM();
		this.createUI();
		this.setItem(startIndex);
	},
	getNaturalSize: function (img) {
		if (Ext.isIE8) {
			var tmpImg = new Image();
			tmpImg.src = img.src;
			return {width: tmpImg.width, height: tmpImg.height};
		} else {
			return {width: img.naturalWidth, height: img.naturalHeight};
		}
	},
	createImageDOM: function() {
		if (!this.iconEl) {
			this.iconEl = Ext.DomHelper.append(this.body, {
				tag: 'img', alt: '', cls: 'fr-image-preview-icon', src: this.blankSrc
			}, true);
			this.iconEl.setVisibilityMode(Ext.Element.DISPLAY);
		}
		if (!this.loaderEl) {
			this.loaderEl = Ext.DomHelper.append(this.body, {tag: 'img'}, true);
			Ext.get(this.loaderEl).on('load', function () {
				if (this.loaderEl.settingBlankPixel) {
					this.loaderEl.settingBlankPixel = false;
					return false;
				}
				this.imgDOM.set({src: this.loaderEl.getAttribute('src')});
			}, this);
		}
		if (!this.imgDOM) {
			this.imgDOM = Ext.DomHelper.append(this.body,{
				tag: 'img', alt: '', cls: 'fr-image-preview', src: this.blankSrc
			}, true);
			this.imgDrag = new Ext.dd.DD(this.imgDOM, false, {moveOnly: true, scroll: false});
			this.imgDrag.lock();
			Ext.get(this.imgDOM).on('load', function () {
				if (this.imgDOM.settingBlankPixel) {
					this.imgDOM.settingBlankPixel = false;
					return false;
				}
				if (this.zoomed) {
					var min = this.body.getHeight();
					min = Math.round(min-10/100*min);
					var nSize = this.getNaturalSize(this.imgDOM.dom);
					this.UI.zoomSlider.setMinValue(min);
					this.UI.zoomSlider.setMaxValue(nSize.height);
					this.UI.zoomSlider.suspendEvents();
					this.UI.zoomSlider.setValue(nSize.height);
					this.UI.zoomSlider.resumeEvents();
				}
				this.adjustImageSize();
				if (this.isCached(true)) {
					this.iconEl.hide();
					this.imgDOM.setVisible(true);
				} else {
					this.iconEl.fadeOut({stopFx: true, duration: 0.2, useDisplay: true});
					this.imgDOM.fadeIn({stopFx: true});
				}
			}, this);
		}
	},
	createUI: function() {
		if (this.UI.tbarWrap) {
			if (FR.currentSection == 'trash'){
				this.commentsBtn.hide();
				this.downloadBtn.hide();
			} else {
				this.commentsBtn.show();
				this.downloadBtn.show();
			}
			this.hideUITask.cancel();
			this.UI.tbarWrap.show();
			this.UI.tbarEl.show();
			if (this.count > 1) {
				this.UI.navLeftWrap.show();
				this.UI.navLeft.show();
				this.UI.navRightWrap.show();
				this.UI.navRight.show();
			}
			this.nav.enable();
			return false;
		}
		this.UI.tbarWrap = Ext.DomHelper.append(this.body, {tag: 'div', cls: 'fr-prv-tbar-ct-wrap'}, true);
		this.UI.tbarEl = Ext.DomHelper.append(this.UI.tbarWrap, {tag: 'div', cls: 'fr-prv-tbar-ct'}, true);
		this.UI.icon = new Ext.Toolbar.Item({cls: 'fr-prv-tbar-icon'});
		this.UI.filename = new Ext.Toolbar.TextItem({cls: 'fr-prv-tbar-filename'});
		this.UI.pageInfo = new Ext.Toolbar.TextItem({cls: 'fr-prv-tbar-pageinfo', listeners: {'afterrender': FR.UI.tooltip('Use "Page Down" and "Page Up" to change the page')}});
		this.UI.status = new Ext.Toolbar.TextItem({cls: 'fr-prv-tbar-status'});
		this.UI.zoomSlider = new Ext.Slider({
			width: 110, minValue: 0, maxValue: 100, value: 100, hidden: true, cls: 'fr-prv-tbar-slider',
			listeners: {
				'change': function (s, v) {
					this.applyZoom.cancel();
					this.applyZoom.delay(50, false, this, [v]);
				},
				scope: this
			}
		});
		this.UI.zoomToggle = new Ext.Button({
			iconCls: 'fa fa-fw fa-arrows-alt fa-lg',
			enableToggle: true,
			toggleHandler: function(b, pressed) {
				if (pressed){
					this.initZoom();
				} else {
					this.cancelZoom();
					this.getMaxSize();
					this.loadThumb();
				}
			}, scope: this, listeners: {'afterrender': FR.UI.tooltip(FR.T('Zoom'))}
		});
		this.commentsBtn = new Ext.Button({
			iconCls: 'fa fa-fw fa-comments-o fa-lg',
			handler: this.showComments, scope: this, hidden: (!User.perms.read_comments || (FR.currentSection == 'trash')),
			listeners: {'afterrender': FR.UI.tooltip(FR.T('Comments'))}
		});
		this.downloadBtn = new Ext.Button({
			iconCls: 'fa fa-fw fa-download fa-lg',
			handler: this.download, scope: this, hidden: (FR.currentSection == 'trash'),
			listeners: {'afterrender': FR.UI.tooltip(FR.T('Download'))}
		});
		this.tbar = new Ext.Toolbar({
			autoCreate: {cls: 'fr-prv-tbar'},
			renderTo: this.UI.tbarEl,
			items: [
				this.UI.icon, this.UI.filename, this.UI.pageInfo,
				'->',
				this.UI.status,
				this.UI.zoomSlider,
				this.UI.zoomToggle,
				this.commentsBtn,
				this.downloadBtn,
				{
					iconCls: 'fa fa-fw fa-close fa-lg',
					handler: this.close, scope: this,
					listeners: {'afterrender': FR.UI.tooltip(FR.T('Close'))}
				}
			]
		});

		this.UI.navLeftWrap = Ext.DomHelper.append(this.body, {tag: 'div', cls: 'fr-prv-nav-left-wrap'}, true);
		this.UI.navLeft = Ext.DomHelper.append(this.UI.navLeftWrap, {tag: 'div', cls: 'fr-prv-nav-btn', style:'float:left', html: '<i class="fa fa-angle-left"></i>'}, true);
		this.UI.navLeft.on('click', this.previousItem, this);
		this.UI.navRightWrap = Ext.DomHelper.append(this.body, {tag: 'div', cls: 'fr-prv-nav-right-wrap'}, true);
		this.UI.navRight = Ext.DomHelper.append(this.UI.navRightWrap, {tag: 'div', cls: 'fr-prv-nav-btn', style:'float:right', html: '<i class="fa fa-angle-right"></i>'}, true);
		this.UI.navRight.on('click', this.nextItem, this);
		new Ext.ToolTip({
			target: this.UI.navRight, showDelay: 250,
			html: FR.T('Next'), anchor: 'left',
			baseCls: 'headerTbar-btn-tooltip'
		});
		new Ext.ToolTip({
			target: this.UI.navLeft, showDelay: 250,
			html: FR.T('Previous'), anchor: 'right',
			baseCls: 'headerTbar-btn-tooltip'
		});


		if (this.count == 1) {
			this.UI.navLeftWrap.hide();
			this.UI.navLeft.hide();
			this.UI.navRightWrap.hide();
			this.UI.navRight.hide();
		}
/*
		this.UI.tbarWrap.on('mouseleave', this.hideUI, this);
		this.UI.navLeftWrap.on('mouseleave', this.hideUI, this);
		this.UI.navRightWrap.on('mouseleave', this.hideUI, this);
		this.imgDOM.on('mouseleave', this.hideUI, this);

		this.UI.tbarWrap.on('mouseenter', this.showUI, this);
		this.imgDOM.on('mouseenter', this.showUI, this);
		this.UI.navLeftWrap.on('mouseenter', this.showUI, this);
		this.UI.navRightWrap.on('mouseenter', this.showUI, this);
*/
		this.setupKeys();
	},
	initZoom: function() {
		if (!this.zoomed) {
			this.UI.zoomSlider.show();
			this.imgDrag.unlock();
			this.maxW = 10000;
			this.maxH = 10000;
			this.zoomed = true;
			this.loadThumb();
		}
	},
	applyZoom: new Ext.util.DelayedTask(function(v) {
		this.imgDOM.setHeight(v).center();
	}),
	cancelZoom: function() {
		if (this.zoomed) {
			this.zoomed = false;
			this.UI.zoomSlider.hide();
			this.imgDrag.lock();
			this.UI.zoomToggle.toggle(false, true);
			this.getMaxSize();
		}
	},
	showUI: function() {this.showUITask.delay(50, false, this);},
	showUITask: new Ext.util.DelayedTask(function() {
		this.hideUITask.cancel();
		if (this.UI.hidden) {
			this.UI.hidden = false;
			this.UI.tbarEl.fadeIn({
				duration: .2,
				stopFx: true, /*callback: function () {
					this.UI.hidden = false;
				},*/ scope: this
			});
			if (this.count > 1) {
				this.UI.navLeft.fadeIn({duration: .2, stopFx: true});
				this.UI.navRight.fadeIn({duration: .2, stopFx: true});
			}
		}
	}),
	hideUI: function() {this.hideUITask.delay(1000, false, this);},
	hideUITask: new Ext.util.DelayedTask(function() {
		this.showUITask.cancel();
		if (!this.UI.hidden) {
			this.UI.hidden = true;
			this.UI.tbarEl.fadeOut({
				duration: 1,
				stopFx: true, /*callback: function () {
					this.UI.hidden = true;
				}, */scope: this
			});
			if (this.count > 1) {
				this.UI.navLeft.fadeOut({duration: 1, stopFx: true});
				this.UI.navRight.fadeOut({duration: 1, stopFx: true});
			}
		}
	}),
	setupKeys: function() {
		this.nav = new Ext.KeyNav(this.body, {
			'left' : function() {this.previousItem();},
			'right' : function(){this.nextItem();},
			'space' : function(){this.nextItem();},
			'up': function() {},
			'down': function() {},
			'pageUp': function() {this.previousPage();},
			'pageDown': function () {this.nextPage();},
			'enter' : function(){this.download();},
			'esc': function() {this.close();},
			scope : this
		});
	},
	collectItems: function(clickedItem) {
		var startIndex = 0;
		this.count = 0;
		FR.UI.gridPanel.store.each(function(item) {
			if (!item.data.isFolder && item.data.thumb && item.data.filetype != 'wvideo'){
				this.items.add(this.count, item.data);
				if (clickedItem == item) {
					startIndex = this.count;
				}
				this.count++;
			}
		}, this);
		return startIndex;
	},
	setItem: function(index, page) {
		this.hideComments();
		if (index != this.currentIndex) {
			this.cancelZoom();
		}
		this.currentIndex = index;
		this.currentItem = this.items.get(index);
		this.pageIndex = page || 0;
		this.currentPath = this.currentItem.path || FR.currentPath+'/'+this.currentItem.filename;
		this.fileSize = this.currentItem.filesize;
		this.UI.icon.update('<img src="images/fico/'+this.currentItem.icon+'" height="20" />');
		this.currentItem.extension = FR.utils.getFileExtension(this.currentItem.filename);
		this.UI.filename.setText(this.currentItem.filename);
		if (this.count > 1) {
			this.UI.status.setText(FR.T('%1 of %2 items').replace('%1', this.currentIndex+1).replace('%2', this.count));
		} else {
			this.UI.status.setText('&nbsp;');
		}
		var pageInfo = '&nbsp;';
		if (this.isMultiPage()) {
			if (this.pageIndex == 0) {
				pageInfo = FR.T('First page');
			} else {
				pageInfo = FR.T('Page %1').replace('%1', this.pageIndex + 1);
			}
		}
		this.UI.pageInfo.setText(pageInfo);
		if (!this.zoomed) {
			this.getMaxSize();
		}
		this.loadThumb();
	},
	nextItem: function() {
		var index = 0;
		if (this.currentIndex < this.count-1) {
			index = this.currentIndex+1;
		}
		this.setItem(index);
	},
	previousItem: function() {
		var index = this.count-1;
		if (this.currentIndex > 0) {
			index = this.currentIndex-1;
		}
		this.setItem(index);
	},
	nextPage: function() {
		if (!this.isMultiPage()) {return false;}
		this.setItem(this.currentIndex, this.pageIndex+1);
	},
	previousPage: function() {
		if (!this.isMultiPage()) {return false;}
		if (this.pageIndex > 0) {
			this.setItem(this.currentIndex, this.pageIndex-1);
		}
	},
	isCached: function(addNow) {
		var cacheId = this.maxW + ':' + this.maxH + ':' + this.pageIndex;
		if (!this.currentItem.cache) {
			this.currentItem.cache = new Ext.util.MixedCollection();
		} else {
			if (this.currentItem.cache.get(cacheId)) {
				return true;
			}
		}
		if (addNow) {
			this.currentItem.cache.add(cacheId, {});
		}
	},
	isMultiPage: function() {
		return (['pdf', 'tif'].indexOf(this.currentItem.extension) != -1);
	},
	loadThumb: function() {
		this.lastRequestedSize = {
			w: this.maxW,
			h: this.maxH
		};
		var src = this.baseSrc+'?p='+encodeURIComponent(this.currentPath);
		src += '&noCache=1';
		src += '&width='+this.maxW+'&height='+this.maxH;
		src += '&pageNo='+this.pageIndex;
		src += '&fsize='+this.fileSize;

		if (!this.isCached()) {
			if (this.currentItem.thumbURL) {
				//set thumb
				this.imgDOM.set({src: this.currentItem.thumbURL});
			} else {
				this.imgDOM.setVisible(false);
				this.iconEl.set({src: 'images/fico/' + this.currentItem.icon});
				this.iconEl.show();
				this.iconEl.center();
			}
			//load preview
			this.loaderEl.set({src: src});
			return false;
		}
		this.imgDOM.set({src: src});
	},
	showMask: function() {
		this.mask = this.body.mask();
		this.mask.addClass('darkMask');
		this.mask.on('click', function() {this.close();}, this);
	},
	hideMask: function() {
		this.body.unmask();
	},
	close: function() {
		this.hideComments();
		this.cancelZoom();
		this.hideUITask.cancel();
		this.UI.tbarWrap.hide();
		this.UI.tbarEl.hide();
		this.UI.navLeftWrap.hide();
		this.UI.navRightWrap.hide();
		this.UI.navLeft.hide();
		this.UI.navRight.hide();
		this.loaderEl.settingBlankPixel = true;
		this.loaderEl.set({src: this.blankSrc});
		this.imgDOM.settingBlankPixel = true;
		this.imgDOM.set({src: this.blankSrc});
		this.iconEl.set({src: this.blankSrc});
		this.nav.disable();
		this.iconEl.hide();
		this.imgDOM.setVisible(false);
		this.hideMask();
		this.open = false;
	},
	getMaxSize: function() {
		this.maxH = this.body.getHeight();
		this.maxW = this.body.getWidth();
		this.maxH = Math.round(this.maxH-10/100*this.maxH-30) * this.highDPIRatio;
		this.maxW = Math.round(this.maxW-10/100*this.maxW) * this.highDPIRatio;
	},
	adjustImageSize: function() {
		//if (!this.lastSize) {this.alignImage();return false;}
		//if (this.lastSize.h != this.maxH) {
			var h = Math.round(this.maxH / this.highDPIRatio);
			var nSize = this.getNaturalSize(this.imgDOM.dom);
			if (nSize.height < h) {
				h = nSize.height;
			}
			this.imgDOM.setHeight(h);
		//}
		this.alignImage();
	},
	alignImage: function() {
		this.imgDOM.alignTo(this.body, 'c-c', [0, 10]);
	},
	onWindowResize: function() {
		if (!this.open) {return false;}
		if (this.zoomed) {
			this.imgDOM.center();
			return false;
		}
		this.lastSize = {
			h: this.maxH,
			w: this.maxW
		};
		this.getMaxSize();
		if (this.maxH > this.lastRequestedSize.h || this.maxW > this.lastRequestedSize.w) {
			var nSize = this.getNaturalSize(this.imgDOM.dom);
			if (nSize.height < this.lastRequestedSize.h) {
			} else {
				this.newSize = 'larger';
			}
		} else {
			this.newSize = 'smaller';
		}
		this.adjustImageSize();
		if (this.newSize == 'larger') {
			this.loadThumb();
		}
	},
	download: function() {
		FR.actions.download([this.currentPath]);
	},
	showComments: function() {
		if (!this.cPanel) {
			this.cPanel = new FR.components.commentsPanel();
			this.cPanel.inputBox.on('focus', function() {this.nav.disable();}, this);
			this.cPanel.inputBox.on('blur', function() {this.nav.enable();}, this);
			this.cPanel.active = true;
			this.cWin = new Ext.Window({
				title: FR.T('Comments'), closeAction: 'hide',
				hideBorders: true, width: 300, height: 330, layout: 'fit',
				items: this.cPanel
			});
		}
		this.cWin.show();
		this.cWin.alignTo(this.body, 'br-br', [-50, -10]);
		this.cPanel.setItem(this.currentPath);
	},
	hideComments: function() {
		if (this.cWin) {
			this.cWin.hide();
		}
	}
};
Ext.EventManager.onWindowResize(function() {FR.UI.imagePreview.onWindowResize();});