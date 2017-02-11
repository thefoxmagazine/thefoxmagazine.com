var PlaylistsViewModel = (function () {

	var instance;


	return function ConstructSingletone() {
		
		this.config = new Config();
		this.items = ko.observableArray();
		this.fetchingItem = ko.observable(false);
		this.currentItem = ko.observable();
		this.itemsCollection = {};

		this.fetch = function (filters) {
			var request = getDataFromApi(filters);
			request.success(function (data) {
				if (data.success) {
					this.items(data.items);
					this.addToCollection(data.items);
				}
			}.bind(this));
			return request;
		};

		this.fetchOne = function (id) {

			var query = 'playlist/' + id;

			var request = getDataFromApi(query);

			request.success(function (data) {
				if (data.success) {
					this.currentItem(data.items);
					this.addToCollection(data.items);
				}
			}.bind(this));

			return request;
		}

		this.setCurrentItem = function(id) {

			this.currentItem();

			if (this.itemsCollection[id]) {
				this.currentItem(this.itemsCollection[id]);
			} else if(this.fetchingItem() === false){

				this.fetchingItem(true);

				this.fetchOne(id).then(function (response) {
					this.fetchingItem(false);
				}.bind(this));
			}
		}


		this.addToCollection = function (value) {

			if (value instanceof Array) {
				value.forEach(function (el) {
					if (el.id && !this.itemsCollection[el.id])
						this.itemsCollection[el.id] = el;
				}, this);
			} else if (value.id) {
				this.itemsCollection[value.id] = value;
			}


		}.bind(this);


		this.getCurrentItemThumbUrls = function () {
			var thumbs = [];
			var playlist = this.currentItem();
			if (playlist && playlist.videos) {
				playlist.videos.forEach(function (el) {
					thumbs.push(getThumbUrl(el));
				}, this);
			}

			return thumbs;

		}.bind(this);


		this.getCurrentItemVideoUrls = function () {
			var videos = [];
			var playlist = this.currentItem();
			if (playlist && playlist.videos) {
				playlist.videos.forEach(function (el) {
					videos.push(getVideoUrl(el));
				}, this);
			}

			return videos;

		}.bind(this);

		function getDataFromApi(query) {
			var URL = this.config.getApiUrl();
			return $.ajax({
				url: URL + query,
				method: 'get'
			});
		}
		
		if (instance) {
			return instance;
		}
		
		if (this && this.constructor === ConstructSingletone) {
			instance = this;
		} else {
			return new ConstructSingletone();
		}
	};

}());


ko.components.register('playlist-item', {
    viewModel: function (paramsObj) {

        var params = paramsObj.params;
        var $el = $(paramsObj.element);
        videeUtils.$root = paramsObj.viewModel;
		
		this.id = params.id;
		this.isPlaylist = true;
        this.title = params.name;
        this.thumbs = [];
        var _length = 0;
        var len = params.videos.length;
        this.videoSrc = [];
        
		
        for (var i = 0; i < len; i++) {
            var obj = params.videos[i];
			
			var thumUrl = getThumbUrl(obj);
            this.thumbs.push(thumUrl);
			
			var videoUrl = getVideoUrl(obj);
            this.videoSrc.push(videoUrl);
			
			_length += parseInt(obj.length);
        }
		
        this.length = videeUtils._formatTime(_length);

        $(document).ready(function () {
            var dragElement = $el.find('.videe-item-thumbnail');

            var dragOptions = {
                helper: 'clone',
                revert: false,
                appendTo: '#wpwrap',
                addClasses: false,
                start: function () {
                    $el[0].style.visibility = "hidden";
                    videeUtils.draggable = this;
                }.bind(this),
                stop: function () {
                    $el[0].style.visibility = "visible";
                },
                cursor: 'default',
                zIndex: 3000
            };
            dragElement.draggable(dragOptions).disableSelection();
        }.bind(this))
    },
    template:
        ['<div class="button-group videe-item-controls-field">',
            '<span class="button button-primary videe-item-button videe-item-insert-code"',
                'data-bind="click: $root.openModal.bind($data, false)">Insert</span>',
            '<span class="button videe-item-button videe-item-copy-code"',
                'data-bind="click: $root.openModal.bind($data, true)">Copy</span>',
            '<input type="hidden" value="[videe_widget width=640 height=480 autoplay=true videoId=" id="videe-playlist-code-" />',
        '</div>',
        '<div class="videe-item-body">',
            '<h4 class="videe-item-title" data-bind="text: title"></h4>',
            '<div class="videe-item-thumbnail playlist" data-bind="foreach: thumbs">',
                '<img class="videe-item-image playlist" height="24" width="24" src="" data-bind="setSrc: { src: $data }"/>',
            '</div>',
            '<div class="videe-item-stats">',
                '<span class="videe-item-stat length" data-bind="text: length"></span>',
            '</div>',
        '</div>'].join('')
});