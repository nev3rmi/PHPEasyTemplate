FR = {
	initialized: false, 
	pageIndex: false, fitToPage: true, paused: true, thumbDrag: false, volume: 50,
	init: function() {
		Ext.QuickTips.init();
		this.toolbar = new Ext.Toolbar({
			items: [
			{
				tooltip: 'Previous',  style: 'font-size:1.2em',
				iconCls: 'fa fa-fw fa-step-backward',
				id: 'fr-prev-btn', disabled: true,
				handler: this.previousFile, scope: this			
			},{
				tooltip: 'Play', style: 'margin-left:5px;font-size:1.2em',
				iconCls: 'fa fa-fw fa-play', handler: this.playPause,
				id: 'fr-play-btn', scope: this
			},{
				tooltip: 'Stop', iconCls: 'fa fa-fw fa-stop',
				id: 'fr-stop-btn', disabled: true,  style: 'margin-left:5px;font-size:1.2em',
				handler: this.stopPlayback, scope: this
			},{
				tooltip: 'Next',  style: 'margin-left:5px;font-size:1.2em',
				id: 'fr-next-btn', iconCls: 'fa fa-fw fa-step-forward',
				handler: this.nextFile, scope: this
			},
			'->',
			'<div id="volSlider" style="width:140px;"></div>',
			'<li class="fa fa-fw fa-large fa-lg fa-volume-up" style="color:#B0B0B0"></li>'
		]});
		
		var ds = new Ext.data.ArrayStore({fields: ['url', 'name']});
		ds.loadData(FR.files);
		var cm = new Ext.grid.ColumnModel({
			columns: [{id: 'name', header: "File name", dataIndex: 'name', width: 400}]
		});
	
		this.grid = new Ext.grid.GridPanel({
			ds: ds,
			cm: cm,
			selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
			autoExpandColumn: 'name', hideHeaders: true
		});
		
		this.grid.on('rowclick', function (grid, rowIndex, e){this.loadFile(rowIndex);}, this);
		this.grid.on('rowcontextmenu', function(grid, rowIndex, e) {e.stopEvent();return false;});

		this.progress = new Ext.Slider({
			style: 'margin:2px;',
			value: 0,
			minValue: 0,
			maxValue: 100,
			listeners: {
				dragstart: function() {FR.thumbDrag = true;},
				dragend: function() {FR.thumbDrag = false;},
				changecomplete: function(s, newValue) {
					if (!this.paused && !this.flac) {
						this.song.setPosition(newValue/100*FR.duration);
					}
				}, scope: this
			}
		});

		this.viewport = new Ext.Viewport({
			layout: 'border',
			items: [
				{
					region: 'north',
					layout: 'border',
					height: 125,
					items: [
						{
							region: 'north',
							html: '<div style="margin:5px;"><div style="position:relative;height:40px;"><div style="position:absolute;top:0px;right:0px;text-align:right;color:gray;width:90px;" id="loadInfo">&nbsp;</div><div style="font-size:25px;color:gray;overflow:hidden;position:absolute;left:0px;top:0px;width:180px;background-color:white;" id="songDur">00:00 / 00:00</div></div><div style="font-size: 11px" id="songInfo">&nbsp;</div></div>',
							height: 60
						},
						{
							region: 'center',
							bbar: this.toolbar,
							items: this.progress
						}
					]
				},
				{
					region: 'center',
					layout: 'fit',
					bodyStyle: 'padding-top:20px',
					items: this.grid
				}
			]
		});
		this.vol = new Ext.Slider({
			renderTo: 'volSlider',
			tooltip: 'Adjust Volume',
			value: 50, minValue: 0, maxValue: 100,
			listeners: {change: function(s, newValue) {FR.setVolume(newValue);}}
		});
		this.grid.getEl().mask('Loading...');
		soundManager.setup({
			url: 'customizables/custom_actions/audio_player/swf',
			flashVersion: 9,
			preferFlash: false,
			useFlashBlock: false,
			useHTML5Audio: true,
			debugMode: false,
			onready: function() {
				FR.loadFile(FR.currentIndex, Ext.isAndroid);
				FR.grid.getEl().unmask();
			},
			ontimeout: function(status) {
				alert('The audio player failed to start. Is Flash missing or blocked in your browser?');
			}
		});
	},
	setVolume: function(v) {
		if (this.flac) {
			this.song.volume = v;
		} else {
			this.song.setVolume(v);
		}
		this.volume = v;
	},
	stopPlayback: function() {
		if (this.flac) {return this.playPause();}
		Ext.getCmp('fr-play-btn').setIconClass('fa fa-fw fa-play');
		Ext.getCmp('fr-stop-btn').disable();
		this.song.stop();
		this.paused = true;
		this.progress.setValue(0);
	},
	playPause: function() {
		if (this.paused) {
			this.play();
		} else {
			Ext.getCmp('fr-play-btn').setIconClass('fa fa-fw fa-play');
			this.song.pause();
			this.paused = true;
		}
	},
	play: function() {
		Ext.getCmp('fr-stop-btn').enable();
		Ext.getCmp('fr-play-btn').setIconClass('fa fa-fw fa-pause');
		if (this.flac) {
			this.song.play();
			this.song.on('buffer', function(p) {
				if (p < 100) {
					Ext.get('loadInfo').update('Loading: ' + Math.round(p) + '%');
				} else {
					Ext.get('loadInfo').update('');
				}
			});
			this.song.on('duration', function(duration) {
				FR.setDuration(duration);
			});
			this.song.on('progress', function(p) {
				FR.setProgress(p);
				if (!FR.thumbDrag) {
					FR.updateProgress();
				}
			});
			this.song.on('metadata', function(data) {
				if (data.artist || data.title) {
					Ext.get('songInfo').update(data.artist + ' - ' + data.title);
				}
			});
			this.song.on('error', function() {FR.nextFile();});
			this.song.on('end', function() {FR.nextFile();});
		} else {
			soundManager.play('song', {
				onload: function () {
					if (this.readyState == 2) {
						FR.nextFile();
					}
				},
				whileloading: function () {
					var perc = Math.round(this.bytesLoaded / this.bytesTotal * 100);
					if (perc < 100) {
						Ext.get('loadInfo').update('Loading: ' + perc + '%');
					} else {
						Ext.get('loadInfo').update('');
					}
				},
				onfinish: function () {
					FR.nextFile();
				},
				whileplaying: function () {
					FR.setProgress(this.position);
					FR.setDuration(FR.getDurationEstimate(this));
					if (!FR.thumbDrag) {
						FR.updateProgress();
					}
				},
				onid3: function () {
					var i = '';
					if (this.id3.TPE1) {
						i += this.id3.TPE1;
					}
					if (this.id3.TIT2) {
						if (i.length > 0) {
							i += ' - ';
						}
						i += this.id3.TIT2;
					}
					Ext.get('songInfo').update(i);
				}
			});
		}
		this.paused = false;
	},
	setProgress: function(p) {
		FR.pgrs = p;
	},
	setDuration: function(d) {
		FR.duration = d;
	},
	updateProgress: function() {
		if (FR.duration) {
			if (FR.progress.disabled) {
				FR.progress.enable();
			}
			var perc = FR.pgrs / FR.duration * 100;
			FR.progress.setValue(perc);
			Ext.get('songDur').update(FR.formatTime(FR.pgrs) + ' / ' + FR.formatTime(FR.duration));
		} else {
			Ext.get('songDur').update(FR.formatTime(FR.pgrs) + ' / &infin;');
			if (!FR.progress.disabled) {
				FR.progress.disable();
			}
		}
	},
	formatTime: function(ms){
		var s=ms/1000;
		var min=parseInt(s/60);
		var sec=parseInt(s%60);
		return String.leftPad(min,2,'0')+':'+String.leftPad(sec,2,'0');
	},
	getDurationEstimate: function(song) {
		if (song.instanceOptions.isMovieStar) {
			return (song.duration);
		} else {
			return song.durationEstimate || (song.duration || 0);
		}
	},
	loadFile: function(index, noAutoPlay) {
		this.currentIndex = index;
		if (this.song) {
			if (this.flac) {
				this.song.stop();
			} else {
				soundManager.destroySound('song');
			}
		}
		if (FR.files[index][1].indexOf('flac') != -1) {
			if (Ext.isIE) {
				return FR.nextFile();
			}
			this.song = AV.Player.fromURL(FR.files[index][0]);
			this.song.volume = this.volume;
			this.flac = true;
		} else {
			this.song = soundManager.createSound({
				id: 'song',
				url: FR.files[index][0],
				volume: this.volume,
				stream: true
			});
			this.flac = false;
		}
		if (!noAutoPlay) {
			this.play();
		}
		window.parent.FR.UI.popups[FR.popupId].setTitle(FR.files[index][1]);
		Ext.get('songInfo').update(FR.files[index][1]);
		
		if (this.currentIndex < this.countFiles-1) {
			Ext.getCmp('fr-next-btn').enable();
		} else {
			Ext.getCmp('fr-next-btn').disable();
		}
		if (this.currentIndex > 0) {
			Ext.getCmp('fr-prev-btn').enable();
		} else {
			Ext.getCmp('fr-prev-btn').disable();
		}
		this.grid.getSelectionModel().selectRow(index);
	},
	nextFile: function() {
		this.grid.getSelectionModel().selectNext();
		if (this.currentIndex < this.countFiles-1) {
			this.loadFile(this.currentIndex+1);
		}
	},
	previousFile: function() {
		this.grid.getSelectionModel().selectPrevious();
		if (this.currentIndex > 0) {
			this.loadFile(this.currentIndex-1);
		}
	}
};