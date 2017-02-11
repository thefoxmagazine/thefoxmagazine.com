ko.bindingHandlers.settingModal = {
	init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
		ko.bindingHandlers.value.init(element, valueAccessor, allBindings);
	},
	update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
		var config = new Config();
		var token = config.getToken();
		var accessor = valueAccessor();
		var data = accessor();
		this.isShowing = allBindings.get('modalVisibility');
		this.isCopy = allBindings.get('isCopy');
		
		if (data.isPlaylist) {
			this.mediaObj = PlaylistsViewModel();
			this.activeVideo = ko.observable(0);
			this.isPlaylist = true;
		} else {
			this.mediaObj = VideosViewModel();
			this.isPlaylist = false;
		}
			
		this.mediaObj.setCurrentItem(data.id);
		
		this.settings = {
			src: data.videoSrc,
			width: data.width || ko.observable(640),
			// smWidth: data.smWidth || ko.observable(),
			// mdWidth: data.mdWidth || ko.observable(),
			// lgWidth: data.lgWidth || ko.observable(),
			height: data.height || ko.observable(480),
			// smHeight: data.smHeight || ko.observable(),
			// mdHeight: data.mdHeight || ko.observable(),
			// lgHeight: data.lgHeight || ko.observable(),
			autoplay: data.autoplay || ko.observable(false),
			autosize: data.autosize || ko.observable(false),
			loop: data.loop || ko.observable(false),
			// playlistMode: data.playlistMode || ko.observable(''),
			volume: data.volume || ko.observable(100),
			oldVolum: data.oldVolum || ko.observable(100),
			mute: data.mute || ko.observable(false),
			async: data.async || ko.observable(true),
			background: data.background || ko.observable(true),
			loadSettings: true,
			loadPlaylist: true,
			type: this.isPlaylist ? 'playlist' : 'video',
		};
		
		if(this.isPlaylist){
			this.settings.thumbs = data.thumbs;
		} else {
			this.settings.thumb = data.thumb;
		}

		this.accordionActive = ko.observable("");
		
		this.modalDismiss = function () {
			this.isShowing(false);
			if (tinyMCE.activeEditor && $(tinyMCE.activeEditor.getContainer()).is(':visible')) {
				tinyMCE.activeEditor.nodeChanged();
			}
		}.bind(this);

		this.accordion = function (activeItem) {
			if (this.accordionActive() == activeItem) {
				this.accordionActive("");
			} else {
				this.accordionActive(activeItem);
			}
		}.bind(this);


		this.setVideo = function (index) {
			this.activeVideo(index());
		}.bind(this);
		



		this.setMute = function () {
			if (this.settings.mute()) {
				this.settings.oldVolum(this.settings.volume());
				this.settings.volume(0)
			} else {
				this.settings.volume(this.settings.oldVolum())
			}
			return true
		}.bind(this);

		this.changeVolume = function () {
			this.settings.oldVolum(this.settings.volume());
			if (this.settings.volume() > 0) {
				this.settings.mute(false);
			} else {
				this.settings.mute(true);
			}
		}.bind(this);

		this.modalSubmit = function () {

			if (data.edit) {
				var img = data.img;
				var settings = ko.toJS(this.settings);
				img.setAttribute("width", settings.width);
				img.setAttribute("height", settings.height);
				img.setAttribute("data-videe-sm", settings.smWidth + "x" + settings.smHeight);
				img.setAttribute("data-videe-md", settings.mdWidth + "x" + settings.mdHeight);
				img.setAttribute("data-videe-lg", settings.lgWidth + "x" + settings.lgHeight);
				img.setAttribute("data-videe-autoplay", settings.autoplay);
				img.setAttribute("data-videe-autosize", settings.autosize);
				img.setAttribute("data-videe-loop", settings.loop);
				img.setAttribute("data-videe-playlist-mode", settings.playlistMode);
				img.setAttribute("data-videe-volume", settings.oldVolum);
				img.setAttribute("data-videe-mute", settings.mute);
				img.setAttribute("data-videe-async", settings.async);
				img.setAttribute("data-videe-background", settings.background);
				img.style.width = settings.width + "px";
				img.style.height = settings.height + "px";
				img.parentNode.style.width = settings.width + "px";
				img.parentNode.style.height = settings.height + "px";
			} else {
			
				if (this.isCopy()) {
					prompt('Shortcode for:\n' + data.title + '', videeUtils.getTag(data.id, ko.toJS(this.settings)));
				} else {
					var tag = videeUtils.getTag(data.id, ko.toJS(this.settings));
					videeUtils.insertContentToEditor(tag);
				}
			}
			this.modalDismiss();
		}.bind(this);

		ko.bindingHandlers.value.update(element, valueAccessor);
		ko.renderTemplate("videe-modal", accessor, {}, element, 'replaceNode');
	}
};
