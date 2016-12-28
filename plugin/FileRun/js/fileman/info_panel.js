FR.components.infoPanel = Ext.extend(Ext.Panel, {//if using TabPanel directly, there are layout problems
	baseCls: 'fr-info-panel',
	initComponent: function() {
		Ext.apply(this, {
			listeners: {
				'collapse': function() {
					FR.UI.actions.info.toggle(false, true);
				},
				'expand': function() {
					FR.UI.actions.info.toggle(true, true);
					this.gridSelChange();
				}
			}
		});
		FR.components.infoPanel.superclass.initComponent.apply(this, arguments);
	},
	customCollapse: function() {
		FR.localSettings.set('infoPanelState', 'collapsed');
		this.collapse();
	},
	customExpand: function() {
		FR.localSettings.set('infoPanelState', 'expanded');
		this.expand();
	},
	onRender: function() {
		FR.components.infoPanel.superclass.onRender.apply(this, arguments);
	},
	gridSelChange: function() {
		if (this.collapsed) {return false;}
		this.countSel = FR.UI.gridPanel.countSel;
		this.countAll = FR.UI.gridPanel.store.getCount();
		var showActivityTab = false;
		var hideCommentsTab = !User.perms.read_comments;
		if (this.countSel == 1) {
			this.item = FR.currentSelectedFile;
			if (FR.currentSection == 'trash' || this.item.data.isFolder) {
				hideCommentsTab = true
			} else {
				FR.UI.commentsPanel.setItem(this.item.data.path, this.item.data.comments);
			}
		} else {
			hideCommentsTab = true;
			this.tabPanel.unhideTabStripItem(1);
			if (FR.currentSection == 'myfiles') {
				this.tabPanel.unhideTabStripItem(1);
				showActivityTab = User.perms.file_history;
			} else if (FR.currentSection == 'sharedFolder') {
				showActivityTab = Settings.filelog_for_shares;
			}
			this.item = null;
		}
		if (!showActivityTab) {
			if (this.tabPanel.getActiveTab() == FR.UI.activityPanel) {
				this.tabPanel.setActiveTab(0);
			}
			this.tabPanel.hideTabStripItem(1);
		}
		if (hideCommentsTab) {
			if (this.tabPanel.getActiveTab() == FR.UI.commentsPanel) {
				FR.UI.infoPanel.tabPanel.setActiveTab(0);
			}
			this.tabPanel.hideTabStripItem(2);
		} else {
			this.tabPanel.unhideTabStripItem(2);
		}
		FR.UI.detailsPanel.gridSelChange();
	},
	folderChange: function() {
		FR.UI.detailsPanel.metadataCache.clear();
		if (this.tabPanel.getActiveTab() == FR.UI.activityPanel) {
			FR.UI.activityPanel.load();
		}
	}
});