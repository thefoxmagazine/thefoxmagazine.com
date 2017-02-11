var videeUtils = {
	_formatExp: function (number) {
		return (number | 0) + (number > (number | 0) ? '.' + (((number * 10) % 10) | 0) : '');
	},
	_formatTime: function (length) {
		return ((length / 60) | 0) + ':' + (((length % 60) / 10) | 0) + length % 10;
	},
	_formatViews: function (views) {
		return views < 1e3 ?
			views :
			views < 1e6 ?
			this._formatExp(views / 1e3) + 'k' :
				views < 1e9 ?
				this._formatExp(views / 1e6) + 'm' :
				this._formatExp(views / 1e9) + 'g';
	},
	videoRequest: function (id, token, success, error) { //TODO Error
		$.ajax({
			url: Config().getApiUrl() + 'playlists?auth_token=' + token,
			method: 'post',
			contentType: 'application/json; charset=utf-8',
			dataType: 'json',
			data: JSON.stringify({
				name: (Math.random() + 1).toString(36).substring(7) + id,
				videos: [id],
				hidden: true
			}),

			success: success
		});
	},
	getTag: function ($id, settings) {
		return '[videe_widget width=' + settings.width +
			' height=' + settings.height +
			// ' sm=' + (settings.smWidth || "") + "x" + (settings.smHeight || "") +
			// ' md=' + (settings.mdWidth || "") + "x" + (settings.mdHeight || "") +
			// ' lg=' + (settings.lgWidth || "") + "x" + (settings.lgHeight || "") +
			' autosize=' + settings.autosize +
			' autoplay=' + settings.autoplay +
			' loop=' + settings.loop +
			' volume=' + settings.oldVolum +
			' thumbnail=' + (settings.thumb || settings.thumbs) +
			// ' playlist-mode=' + settings.playlistMode +
			' mute=' + settings.mute +
			' async=' + settings.async +
			' background=' + settings.background +
			' ' + settings.type +
			'Id=' +  $id + ']';
	},
	insertTextAtCursor: function (el, text) {
		var val = el.value, endIndex, range;
		if (typeof el.selectionStart != "undefined" && typeof el.selectionEnd != "undefined") {
			endIndex = el.selectionEnd;
			el.value = val.slice(0, el.selectionStart) + text + val.slice(endIndex);
			el.selectionStart = el.selectionEnd = endIndex + text.length;
		} else if (typeof document.selection != "undefined" && typeof document.selection.createRange != "undefined") {
			el.focus();
			range = document.selection.createRange();
			range.collapse(false);
			range.text = text;
			range.select();
		}
	},
	insertContentToEditor: function (text) {
		if (tinyMCE.activeEditor && $(tinyMCE.activeEditor.getContainer()).is(':visible')) {
			tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
		} else {
			videeUtils.insertTextAtCursor(document.getElementById('content'), text);
		}
	},

	draggable: null,
	$root: null
};

jQuery(document).ready(function () {
	videeUtils.dropElement = jQuery('body #wp-content-editor-container');
	var dropOptions = {
		drop: function () {
			videeUtils.$root.openModal(false, videeUtils.draggable);
		}
	};
	if (videeUtils.dropElement.droppable) {
		videeUtils.dropElement.droppable(dropOptions);
	}
});

ko.bindingHandlers.enterkey = {
	init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
		var callback = valueAccessor();
		$(element).keypress(function (event) {
			var keyCode = (event.which ? event.which : event.keyCode);
			if (keyCode === 13) {
				callback.call(bindingContext.$data, event);
				return false;
			}
			return !(keyCode < 48 || keyCode > 57);
		});
		element.onpaste = function (e) {
			e.preventDefault();
		};
	}
};

ko.bindingHandlers.preventchars = {
	init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
		var value = valueAccessor();
		$(element).keypress(function (event) {
			var keyCode = (event.which ? event.which : event.keyCode);
			var val = value();
			return !(keyCode < 48 || keyCode > 57) && (val && val.toString().length + 1 <= 4);
		});
		element.onpaste = function (e) {
			e.preventDefault();
		};
	}
};

ko.bindingHandlers.queryset = {
	init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
		var callback = valueAccessor();
		$(element).keypress(function (event) {
			var keyCode = (event.which ? event.which : event.keyCode);
			if (keyCode === 13) {
				callback.call(bindingContext.$data, event);
				return false;
			}
			return true;
		});
	}
};

ko.bindingHandlers.setSrc = {
	update: function (element, valueAccessor) {
		var options = valueAccessor();
		var src = ko.unwrap(options.src);
		$('<img />').attr('src', src).on('load', function () {
			$(element).attr('src', src);
		}).on('error', function () {
			$(element).attr('src', Config().getPluginUrl() + '_inc/images/no-preview-available.png');
		});
	}
};

ko.bindingHandlers.select2 = {
	init: function (el, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
		var apiUrl = Config().getApiUrl();
		ko.utils.domNodeDisposal.addDisposeCallback(el, function () {
			$(el).select2('destroy');
		});
		var allBindings = allBindingsAccessor();
		var select2 = ko.utils.unwrapObservable(allBindings.select2);
		select2.data = ko.utils.unwrapObservable(allBindings.options || allBindings.select2.options);

		$(el).select2(select2).on("select2:unselecting", function (e) {
			$(this).select2("val", "");
			e.preventDefault();
		});
	},
	update: function (el, valueAccessor, allBindingsAccessor, viewModel) {
		var allBindings = allBindingsAccessor();
		var selected = allBindings.selectedOptions && allBindings.selectedOptions();
		var value = allBindings.value && allBindings.value();
		$(el).val(selected || value);
		$(el).trigger('change');
	}
};

ko.bindingHandlers.datepicker = {
	init: function (element, valueAccessor, allBindingsAccessor) {
		var options = allBindingsAccessor().datepickerOptions || {};
		options.format = 'Y-m-d';
		options.hide_on_select = true;
		options.change = function () {
			var observable = valueAccessor();
			observable($(element).pickmeup('get_date', true));
		};
		ko.utils.domNodeDisposal.addDisposeCallback(element, function () {
			$(element).pickmeup("destroy");
		});
		$(element).pickmeup(options);
	},
	update: function (element, valueAccessor) {
		var value = ko.utils.unwrapObservable(valueAccessor());
		var current = $(element).pickmeup("get_date");
		if (value && value - current !== 0) {
			$(element).pickmeup("set_date", value);
		}
	}
};

ko.bindingHandlers.optionsBind = {
	preprocess: function (value, key, addBinding) {
		addBinding('optionsAfterRender', 'function(option, item) { if(!option){return} ko.bindingHandlers.optionsBind.applyBindings(option, item, ' + value + ') }');
	},
	applyBindings: function (option, item, bindings) {
		if (item !== undefined) {
			option.setAttribute('data-bind', bindings);
			ko.applyBindings(ko.contextFor(option).createChildContext(item), option);
		}
	}
};

function getThumbUrl(obj) {
	var url = obj.domestic ? obj.coverCdnPath + obj.image : obj.image;
	return url.replace("public3cdn1.videe.tv/upload", "static3cdn1.videe.tv/upload")
		.replace('video1source1.videe.tv','cdn-auth.videe.tv');
}

function getVideoUrl(obj) {
	var path;
	
	if(obj.video_source === 'remote') {
		return obj.mp4;
	}

	if(parseInt(obj.domestic) == 0) {
		path = obj.ownerid;	
	} else if(parseInt(obj.domestic) == 1) {
		path =  'pvideo/hdvideo';
	}

	var videoFile = obj.file_name || obj.mp4;
	if(typeof videoFile === 'string') {
		videoFile = videoFile.substring(videoFile.lastIndexOf('/')+1);
	}

	var videoUrl = '//cdn-auth.videe.tv/' + path + '/' + videoFile;
	return videoUrl.replace("public3cdn1.videe.tv/upload/", "public3cdn1.videe.tv/");			
}

function isPositiveNumber(value) {
	var value = parseInt(value);
	return !isNaN(value) && isFinite(value) && value > 0;
}