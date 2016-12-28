FR = {
	UI: {}, urn: false, access_token: false,
	init: function() {
		this.pbar = new Ext.ProgressBar({style: 'font-family:Roboto;font-size:11px', animate: true, width:480, hidden: true});
		this.viewport = new Ext.Viewport({
			layout: 'card', border:false, activeItem: 0,
			defaults: {border:false},
			items: [
				{
					html: '<div id="status" style="font-family:Roboto"></div>',
					tbar: [this.pbar]
				},
				{
					html: '<div id="viewer" style="width:100px;height:100px;"></div>'
				}
			],
			listeners: {'afterrender': function() {this.start();}, scope: this}
		});
	},
	start: function(format) {
		this.log(FR.T('Sending data to AutoDesk...'));
		Ext.Ajax.request({
			url: URLRoot+'/?module=custom_actions&action=autodesk&method=start',
			params: {path: path},
			callback: function(opts, succ, req) {
				try {
					var rs = Ext.util.JSON.decode(req.responseText);
				} catch (er){return false;}
				if (rs.msg) {
					window.parent.FR.UI.feedback(rs.msg);
					FR.urn = rs.urn;
					FR.access_token = rs.access_token;
					this.log(rs.msg);
					if (FR.access_token) {
						window.setTimeout(function () {
							FR.pbar.show();
							FR.getStatus();
						}, 2000);
					}
				}
			},
			scope: this
		});
	},
	getStatus: function() {
		var progress = 0;
		Ext.Ajax.request({
			url: URLRoot+'/?module=custom_actions&action=autodesk&method=checkStatus',
			params: {
				path: path,
				urn: FR.urn,
				access_token: FR.access_token
			},
			callback: function(opts, succ, req) {
				try {
					var rs = Ext.util.JSON.decode(req.responseText);
				} catch (er){return false;}
				if (rs.msg) {
					this.log(rs.msg);
				}
				if (rs.success) {
					if (rs.data.status == 'inprogress' || rs.data.status == 'pending') {
						progress = rs.percent/100;
						this.pbar.updateProgress(progress, rs.data.progress);
						window.setTimeout(function(){FR.getStatus();}, 2000);
					} else if (rs.data.status == 'success') {
						this.pbar.updateProgress(1, rs.data.progress);
						this.viewport.getLayout().setActiveItem(1);
						FR.loadViewer();
					}
				}
			},
			scope: this
		});
	},
	log: function(txt) {
		Ext.DomHelper.append('status', {tag: 'div', html: txt});
	},
	loadViewer: function() {
		var options = {
			'document' : 'urn:'+FR.urn,
			'env':'AutodeskProduction',
			'getAccessToken': function(){return FR.access_token;},
			'refreshToken': function(){return FR.access_token;}
		};
		var viewerElement = document.getElementById('viewer');
		var viewer = new Autodesk.Viewing.Private.GuiViewer3D(viewerElement, {});
		Autodesk.Viewing.Initializer(options,function() {
			viewer.initialize();
			FR.loadDocument(viewer, options.document);
		});
	},
	loadDocument: function(viewer, documentId) {
		Autodesk.Viewing.Document.load(documentId,
			function (doc) {// onLoadCallback
				var rootItem = doc.getRootItem();
				var geometryItems = [];
				//check 3d first
				geometryItems = Autodesk.Viewing.Document.getSubItemsWithProperties(rootItem, {
					'type': 'geometry',
					'role': '3d'
				}, true);
				//no 3d geometry, check 2d
				if (geometryItems.length == 0) {
					geometryItems = Autodesk.Viewing.Document.getSubItemsWithProperties(rootItem, {
						'type': 'geometry',
						'role': '2d'
					}, true);
				}
				//load the first geometry
				if (geometryItems.length > 0) {
					viewer.load(doc.getViewablePath(geometryItems[0]),
						null,           //sharedPropertyDbPath
						function () {},//onSuccessCallback
						function () {} //onErrorCallback
					);
				}
			}, function (errorMsg) {// onErrorCallback
				alert("Load Error: " + errorMsg);
			});
	}
};
Ext.onReady(FR.init, FR);