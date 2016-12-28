FR.spaceQuota = {
	store: new Ext.data.SimpleStore({
        fields: [
           {name: 'id', type: 'integer'}, {name: 'username'}, {name: 'name'}, {name: 'max'},
		   {name: 'maxNice'}, {name: 'used'}, {name: 'usedNice'}, {name: 'percent'}
        ]
    }),
	sb: new Ext.ux.StatusBar({id: 'my-status', defaultText:'&nbsp;'})
}
FR.spaceQuota.store.loadData(FR.users);
FR.spaceQuota.grid = new Ext.grid.GridPanel({
	title: FR.T('File space quota usage'), border: false,
	store: FR.spaceQuota.store,
	cm: new Ext.grid.ColumnModel({
		defaults: {sortable: true},
		columns: [
			{header: FR.T("Id"), width: 30, dataIndex: 'id', hidden: true},
			{header: '&nbsp;', width: 28, resizable: false, sortable: false, renderer: function(v, m, r) {return '<img src="a/?uid='+r.data.id+'" class="avatar-xs">';}},
			{id:'uname', header: FR.T("Name"), width: 150, dataIndex: 'name'},
			{id:'usrname', header: FR.T("Username"), width: 150, dataIndex: 'username'},
			{header: FR.T("Quota"), width: 75, renderer: function(v, m, r) {return r.data.maxNice;}, dataIndex: 'max'},
			{header: FR.T("Used"), width: 75, renderer: function(v, m, r) {
				if (r.data.max > 0 && (v >= r.data.max)) {
					return '<span style="color:red;">' +r.data.usedNice+ '</span>';
				} else {
					return r.data.usedNice;
				}
			}, dataIndex: 'used'},
			{header: FR.T("Usage"), width: 85, renderer: function(v, m, r) {
				if (v > FR.highlightLimit) {
					return '<span style="color:red;">' +v+ '%</span>';
				} else {
					if (v) {return v+'%';} else {return '';}
				}
			}, dataIndex: 'percent'}
		]
	}),
	bbar: FR.spaceQuota.sb,
	listeners: {
		'afterrender': function() {
			var data = FR.spaceQuota.store.data;
			if (data.length > 0) {
				FR.currentUID = 0;
				FR.spaceQuota.getQuota(data.items[FR.currentUID].data.id, data.items[FR.currentUID]);
			}
		},
		'destroy': function() {
			Ext.Ajax.abort(FR.spaceQuota.ajaxReq);
			FR.spaceQuota = false;
		}
	}
});

FR.spaceQuota.getQuota = function(uid, r) {
	if (FR.spaceQuota.sb) {
		FR.spaceQuota.sb.showBusy(FR.T('Calculating quota usage for "%1"...').replace('%1', r.data.name));
		this.ajaxReq = Ext.Ajax.request({
			url: FR.URLRoot+'/?module=cpanel&section=tools&page=space_quota&action=get&uid='+uid,
			method: 'GET',
			success: function(result, request) {
				try {
					rs = Ext.util.JSON.decode(result.responseText);
				} catch(er) {}
				if (rs && FR.spaceQuota) {
					r.set('max', rs.max);
					r.set('maxNice', rs.maxNice);
					r.set('used', rs.used);
					r.set('usedNice', rs.usedNice);
					r.set('percent', rs.percent);
					r.commit();
					FR.currentUID++;
					var data = FR.spaceQuota.store.data;
					if (data.items[FR.currentUID] && FR.spaceQuota) {
						FR.spaceQuota.getQuota(data.items[FR.currentUID].data.id, data.items[FR.currentUID]);
					} else {
						FR.spaceQuota.sb.clearStatus();
					}
				}
			}
		});
	}
}
Ext.getCmp('appTab').add(FR.spaceQuota.grid);
Ext.getCmp('appTab').doLayout();