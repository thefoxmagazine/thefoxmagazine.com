var VideosViewModel = (function () {

	var instance;

	return function ConstructSingletone() {
		

		this.items = ko.observableArray();
		this.page = ko.observable(0);
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

			var query = 'video/' + id;

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


		this.getCurrentItemThumbUrl = function () {
			var video = this.currentItem();
			if(video !== null && typeof video === 'object') {
				return getThumbUrl(video);
			}
		}.bind(this);


		this.getCurrentItemVideoUrl = function () {
			var video = this.currentItem();
			if(video !== null && typeof video === 'object') {
				return getVideoUrl(video);
			}
			
		}.bind(this);



		function getDataFromApi(query) {
			var URL = Config().getApiUrl();
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

ko.components.register('video-item', {
    viewModel: function (paramsObj) {
        var params = paramsObj.params;
        var $el = $(paramsObj.element);
        videeUtils.$root = paramsObj.viewModel;
		this.id = params.id;
        this.title = params.title;
        this.views = videeUtils._formatViews(params.views);
        this.length = videeUtils._formatTime(params.length);
        this.isPlaylist = false;
		
		
		this.thumb = getThumbUrl(params);
		this.videoSrc = getVideoUrl(params);

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
        }.bind(this));
    },
    template: ['<div class="button-group videe-item-controls-field">',
                    '<span class="button button-primary videe-item-button videe-item-insert-code"',
                        'data-bind="click: $root.openModal.bind($data, false)">Insert</span>',
                    '<span class="button videe-item-button videe-item-copy-code"',
                        'data-bind="click: $root.openModal.bind($data, true)">Copy</span>',
                '</div>',
                '<div class="videe-item-body">',
                    '<h4 class="videe-item-title" data-bind="text: title"></h4>',
                    '<div class="videe-item-thumbnail">',
                        '<img class="videe-item-image" width="106" height="100" src="" ',
                            'data-bind="setSrc: { src: thumb }"/>',
                    '</div>',
                    '<div class="videe-item-stats">',
                        '<span class="videe-item-stat length" data-bind="text: length"></span>',
                        '<span class="videe-item-stat views" data-bind="text: views"></span>',
                    '</div>',
                '</div>'].join('')
});
