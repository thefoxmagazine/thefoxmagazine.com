(function ($) {
    "use strict";

    var _events = {};
    var tab = 'videos';
    var type = 'video';

    var $widget = $('#videe-widget');
    var $tabs = $('#videe-widget-tabs');
    var $videosList = $('#videe-videos-list');
    var $playlistsList = $('#videe-playlists-list');
    var $filter = $('#videe-filter');
    var $filterInput = $('.videe-filter-input');
    var $pager = $('#videe-pager');
    var $pagerFirst = $('#videe-pager-first-page');
    var $pagerPrev = $('#videe-pager-prev-page');
    var $pagerTotal = $('#videe-pager-total');
    var $pagerNext = $('#videe-pager-next-page');
    var $pagerLast = $('#videe-pager-last-page');
    var $pagerCurrentPage = $('#videe-pager-current');
    var $itemControlsSize = $('.videe-item-controls-size');

    var SearchFilter = {
        limit: 20,
        start: 0,
        domestic: 1,
        query: '',
        total: 0,
        page: 0,
        maxPages: 0,
        property: "date_created",
        asc: 'DESC',
        source: null
    };

    var API = {
        URL: Config().getApiUrl(),

        videos: function () {
            var query = 'videos?auth_token=' + Config().getToken()
                + '&query=' + SearchFilter.query.trim()
                + '&domestic=' + SearchFilter.domestic
                + '&limit=' + SearchFilter.limit
                + '&start=' + SearchFilter.start
				+ '&relevance=1'
                + '&sort=[{"property":"' + SearchFilter.property + '","direction":"' + SearchFilter.asc + '"}]';

            if (SearchFilter.source === 'premium') {
                query += '&premium=true';
            }
			
			if (SearchFilter.query.trim().length > 0) {
				query += '&relevance=1';
			}

            return query;

        },

        playlists: function () {
            return 'playlists?auth_token=' + Config().getToken()
                + '&query=' + SearchFilter.query
                + '&limit=' + SearchFilter.limit
                + '&start=' + SearchFilter.start;
        }
    };

    var modal = new Modal({
        title: 'Edit Video',
    }, $);

    function RENDER(tab, data) {
        var html;
        var _items;
        var _formatExp = function (number) {
            return (number | 0) + (number > (number | 0) ? '.' + (((number * 10) % 10) | 0) : '');
        };
        var _formatTime = function (length) {
            return ((length / 60) | 0) + ':' + (((length % 60) / 10) | 0) + length % 10;
        };
        var _formatViews = function (views) {
            return views < 1e3 ?
                views :
                views < 1e6 ?
                _formatExp(views / 1e3) + 'k' :
                    views < 1e9 ?
                    _formatExp(views / 1e6) + 'm' :
                    _formatExp(views / 1e9) + 'g';
        };
        switch (tab) {
            case "videos":
                $videosList.empty();
                $playlistsList.empty();
                _items = '';

                if (data.success && data.total > 0) {
                    for (var i = 0; i < data.items.length; i++) {
                        data.items[i].image = data.items[i].image.replace("public3cdn1.videe.tv/upload", "static3cdn1.videe.tv/upload");
                        data.items[i].mp4 = data.items[i].mp4.replace("public3cdn1.videe.tv/upload/", "public3cdn1.videe.tv/");

                        _items +=
                            '<div data-videe-video-id="' + data.items[i].id + '" data-videe-src="' + data.items[i].videoCdnPath + data.items[i].mp4 + '" class="videe-item ' + (i % 2 ? 'even' : '') + '">\
                                <div class="button-group videe-item-controls-field">\
                                    <span data-videe-video-id="' + data.items[i].id + '" class="button button-primary videe-item-button videe-item-insert-code">Insert</span>\
                                    <span data-videe-video-id="' + data.items[i].id + '" class="button videe-item-button videe-item-copy-code">Copy</span>\
                                </div>\
                                <div class="videe-item-body">\
                                    <h4 class="videe-item-title">' + escapeHtml(data.items[i].title) + '</h4>\
                                    <div class="videe-item-thumbnail">\
                                        <img class="videe-item-image" width="109" height="100" src="' + (data.items[i].domestic ? data.items[i].coverCdnPath + data.items[i].image : data.items[i].image) + '" onerror="this.src=window.videePluginUrl+\'_inc/images/no-preview-available.png\';this.onerror=\'\';" />\
                                    </div>\
                                    <div class="videe-item-stats">\
                                        <span class="videe-item-stat length">' + _formatTime(data.items[i].length) + '</span>\
                                        <span class="videe-item-stat views">' + _formatViews(data.items[i].views) + '</span>\
                                    </div>\
                                </div>\
                            </div>';
                    }
                } else {
                    _items = '<h4 class="videe-no-videos">No videos added yet.</h4>';
                }
                html = _items;
                break;
            case "playlists":
                $videosList.empty();
                $playlistsList.empty();
                _items = '';

                if (data.success && data.total > 0) {

                    for (var i = 0; i < data.items.length; i++) {
                        var _thumbs = '',
                            _length = 0;

                        for (var j = 0; j < data.items[i].videos.length; j++) {

                            data.items[i].videos[j].image = data.items[i].videos[j].image.replace("public3cdn1.videe.tv/upload", "static3cdn1.videe.tv/upload");
                            data.items[i].videos[j].mp4 = data.items[i].videos[j].mp4.replace("public3cdn1.videe.tv/upload/", "public3cdn1.videe.tv/");
                            var url = (data.items[i].videos[j].coverCdnPath || "") + data.items[i].videos[j].image;
                            var video = (data.items[i].videos[j].videoCdnPath || "") + data.items[i].videos[j].mp4;
                            _thumbs += '<img class="videe-item-image playlist" height="24" width="24" src="' + url + '" data-videe-video="' + video + '" onerror="this.src=Config().getPluginUrl()+\'_inc/images/no-preview-available.png\';this.onerror=\'\';" />';
                        }

                        for (var l = 0; l < data.items[i].videos.length; l++) {
                            _length = _length + parseInt(data.items[i].videos[l].length);
                        }

                        _items +=
                            '<div data-videe-playlist-id="' + data.items[i].id + '" class="videe-item ' + (i % 2 ? 'even' : '') + '">\
                                <div class="button-group videe-item-controls-field">\
                                    <span data-videe-playlist-id="' + data.items[i].id + '" class="button button-primary videe-item-button videe-item-insert-code">Insert</span>\
                                    <span data-videe-playlist-id="' + data.items[i].id + '" class="button videe-item-button videe-item-copy-code">Copy</span>\
                                    <input type="hidden" value="[videe_widget width=640 height=480 autoplay=true videoId=' + data.items[i].id + '" id="videe-playlist-code-' + data.items[i].id + '" />\
                                </div>\
                                <div class="videe-item-body">\
                                    <h4 class="videe-item-title">' + escapeHtml(data.items[i].name) + '</h4>\
                                    <div class="videe-item-thumbnail playlist">' + _thumbs + '</div>\
                                    <div class="videe-item-stats">\
                                        <span class="videe-item-stat length">' + _formatTime(_length) + '</span>\
                                    </div>\
                                </div>\
                            </div>';
                        _thumbs = '';
                        _length = '';
                    }
                } else {
                    _items = '<h4 class="videe-no-videos">No playlists have been added.<br><a target="_blank" href="/wp-admin/admin.php?page=videe-key-config#tab=library&showpl=true">Create</a> you first playlist.</h4>';
                }
                html = _items;
                break;
            case "premium":
                SearchFilter.total = 0;
                renderPager();
                html = '<h4 class="videe-no-videos">Coming soon! Collection of high-quality videos in the most popular categories to impress your users. </h4>'

        }
        return html;
    }

    function insertTextAtCursor(el, text) {
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
    }

    function insertContentToEditor(text) {
        if (tinyMCE.activeEditor && $(tinyMCE.activeEditor.getContainer()).is(':visible')) {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
        } else {
            insertTextAtCursor(document.getElementById('content'), text);
        }
    }

    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function (m) {
            return map[m];
        });
    }

    function renderPager() {
        if (SearchFilter.total > 0) {
            $pager.show();
            $pagerTotal.text(SearchFilter.maxPages);
            $pagerCurrentPage.val(SearchFilter.page + 1);

            $pagerPrev.attr('data-page', SearchFilter.page - 1);
            $pagerNext.attr('data-page', SearchFilter.page + 1);
            $pagerLast.attr('data-page', SearchFilter.maxPages - 1);

            (SearchFilter.page === 0 ? $pagerFirst.addClass('disabled') : $pagerFirst.removeClass('disabled'));
            (SearchFilter.page === 0 ? $pagerPrev.addClass('disabled') : $pagerPrev.removeClass('disabled'));

            /// (SearchFilter.maxPages - 1) - because first page is 0
            ((SearchFilter.page == (SearchFilter.maxPages - 1) || SearchFilter.maxPages === 1) ? $pagerNext.addClass('disabled') : $pagerNext.removeClass('disabled'));
            ((SearchFilter.page == (SearchFilter.maxPages - 1) || SearchFilter.maxPages === 1) ? $pagerLast.addClass('disabled') : $pagerLast.removeClass('disabled'));
        } else {
            $pager.hide()
        }
    }

    function setPage(page) {
        SearchFilter.start = (page === 0 ? 0 : page * SearchFilter.limit);
        SearchFilter.page = page;
        if (SearchFilter.domestic == 2) {
            $('#videe-' + tab + '-list').html(RENDER('premium'));

        } else {
            getData(tab);
        }
    }

    function _pagerHelper(event) {
        event.stopPropagation();
        var _el = $(event.target);
        return _el.hasClass('disabled')
    }

    function getTag(id, externalId, settings) {
        var _width = settings.width;
        var _height = settings.height;
        var _autoPlay = settings.autoPlay;
        var _loop = settings.loop;
        var _playlistMode = settings.playlistMode;
        var _volume = settings.volume;
        var _mute = settings.mute;
        var _async = settings.async;
        var _background = settings.background;
        var _id = (externalId ? externalId : id);
        var _loadSettings = settings.loadSettings;
        var _loadPlaylist = settings.loadPlaylist;

        var type = tab.substring(0, tab.length - 1);
        var $video = $(".videe-item[data-videe-" + type + "-id='" + id + "']");
        var $videoSrc = $video.attr("data-videe-src");
        var thumb = $video.find(".videe-item-image").attr("src");

        return '[videe_widget width=' + _width +
            ' height=' + _height +
            ' autoplay=' + _autoPlay +
            ' loop=' + _loop +
            ' volume=' + _volume +
            " " + type +
            'Id=' + _id +
            ' thumbnail=' + thumb +
            ' src=' + $videoSrc +
            ' playlist-mode=' + _playlistMode +
            ' mute=' + _mute +
            ' async=' + _async +
            ' background=' + _background +']';
    }

    function getData(tab) {
        var $spinner = $('<div class="videe-spinner-rotator"></div>');
        var $list = $('#videe-' + tab + '-list');
        $list.empty().addClass('videe-spinner videe-spinner-tiny').append($spinner);

        jQuery.ajax({
            url: API.URL + API[tab](),
            method: 'get',

            success: function (data) {
                setTimeout(function () {
                    $spinner.remove();
                    $list.removeClass('videe-spinner videe-spinner-tiny');
                    if (data.success) {
                        SearchFilter.total = data.total;
                        SearchFilter.maxPages = Math.ceil(data.total / SearchFilter.limit);
                        $list.html(RENDER(tab, data));
                        renderPager();
                    } else if (data.errors == "forbidden") {
                        $("#videe-widget").addClass("unauthorized")
                    }
                }, 500);
            }
        });
    }

    function videoRequest(id, success, error) { //TODO Error
        jQuery.ajax({
            url: Config().getApiUrl() + 'playlists?auth_token=' + Config().getToken(),
            method: 'post',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify({
                name: (Math.random() + 1).toString(36).substring(7) + id,
                videos: [id],
                hidden: true
            }),

            success: success
        })
    }

    $tabs.on('click', 'a', function (event) {
        event.preventDefault();

        var _el = $(this),
            _currentTab = _el.attr('data-videe-tab');

        tab = _currentTab;
        type = tab.substring(0, tab.length - 1);

        $widget.find('.tabs-panel').hide();
        $widget.find('li.tabs').removeClass('tabs');

        _el.parent().addClass('tabs');
        $('#videe-widget-' + _currentTab).show();


        $filterInput.val("");

        SearchFilter = {
            limit: 20,
            start: 0,
            domestic: 1,
            query: '',
            total: 0,
            page: 0,
            maxPages: 0,
            property: "date_created",
            asc: 'DESC'
        };

        getData(tab);
    });

    $pager.on('click', 'a[data-page]', function (event) {
        if (!_pagerHelper(event)) {
            var _el = $(this),
                page = parseInt(_el.attr('data-page'), 10);

            setPage(page);
        }
    });

    $filter.on('click', '.button', function () {
        var _el = $(this);

        $filter.find('.active').removeClass('active');
        _el.addClass('active');
        SearchFilter.domestic = _el.attr('data-domestic');
        SearchFilter.source = _el.attr('data-source');
        setPage(0);

        // getData(tab);
    });

    $filterInput.on('keypress', function (event) {
        var _el = $(this);
        SearchFilter.query = _el.val();

        if (event.which == 13) {
            getData(tab);
            return false;
        }
    });

    $filterInput.on('focus', function () {
        var $post = $('form#post');
        _events = $post.data('events');

        $post.on('submit', function () {
            return false;
        });
    });

    $filterInput.on('focusout', function () {
        $('form#post').off();
        if (_events) {
            $.each(_events, function () {
                $.each(this, function () {
                    $('form#post').on(this.type, this.handler);
                });
            });
        }
    });

    $pagerCurrentPage.on('keyup', function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();
        event.stopPropagation();

        var _el = $(this),
            _page = parseInt(_el.val());

        if (_page == 0 || isNaN(_page) || _page == '') {
            _page = 1;
        }
        if (_page > SearchFilter.maxPages) {
            _page = SearchFilter.maxPages;
        }
        
        SearchFilter.page = _page - 1;

        SearchFilter.start = SearchFilter.page * SearchFilter.limit;

        if (event.which == 13) {
            getData(tab);
        }
    });

    $pagerCurrentPage.on('focus', function () {
        var _form = $('form#post');

        _events = _form.data('events');

        _form.on('submit', function () {
            return false;
        });
    });

    $pagerCurrentPage.on('focusout', function () {
        $('form#post').off();

        $.each(_events, function () {
            $.each(this, function () {
                $('form#post').on(this.type, this.handler);
            });
        });
    });

    $widget.on('keyup', 'input.videe-item-control-input-size', function () {
        var _id = $(this).attr('data-videe-' + type + '-id');
        $('#videe-' + type + '-code-' + _id).val(getTag(_id));
    });

    $widget.on('click', '.videe-item-control-checkbox', function () {
        var _id = $(this).attr('data-videe-' + type + '-id');
        $('#videe-' + type + '-code-' + _id).val(getTag(_id));
    });

    $widget.on('click', '.videe-item-control-icon-volume', function () {
        var _el = $(this),
            _id = _el.attr('data-videe-' + type + '-id'),
            _vol = _el.data('videe-volume'),
            _range = $('#videe-' + type + '-volume-' + _id);

        if (_vol) {
            _range.val(_vol);
            _range.prop('disabled', false);
            _el.data('videe-volume', false);
        } else {
            _el.data('videe-volume', _range.val());
            _range.val(0);
            _range.prop('disabled', true);
        }
        _range.trigger(navigator.userAgent.match(/MSIE 10/) ? 'change' : 'input');
    });

    $widget.on(navigator.userAgent.match(/MSIE 10/) ? 'change' : 'input', '.videe-item-control-input-volume', function () {
        var _el = $(this),
            _id = _el.attr('data-videe-' + type + '-id'),
            $value = $('#videe-' + type + '-volume-value-' + _id),
            _icon = $value.siblings('i');

        $value.text(_el.val());
        $('#videe-' + type + '-code-' + _id).val(getTag(_id));

        if (_el.val() == 0) {
            _icon.addClass('mute');
        } else {
            _icon.removeClass('mute');
        }
    });

    $videosList.on('click', '.videe-item-copy-code', function () {
        var _el = $(this),
            _id = _el.attr('data-videe-video-id');


        videoRequest(_id, function (response) {
            _el.text('Load...');
            if (response.success) {
                var title = _el.closest(".videe-item").find(".videe-item-title").text();
                prompt('Shortcode for:\n' + title + '', getTag(_id, response.items.id))
            }
            _el.text('Copy');
        });
    });

    $videosList.on('click', '.videe-item-insert-code', function () {
        var $el = $(this);
        var id = $el.attr('data-videe-video-id');
        var src = $el.parent().parent().attr('data-videe-src');

        var spinner =
            $('<div class="videe-spinner videe-spinner-tiny" style="top: 140px;\
                    position: absolute; left: 0; right: 0;\
                    z-index: 100;"><div class="videe-spinner-rotator"></div></div>');

        /*var editor = tinyMCE.activeEditor;
        editor.fire('editVidee', {
            requestCallback: requestCB,
            videeSrc: src
        });*/
        
        modal.open({
            src: src,
            width: 780,
            height: 310,
            onsubmit: function (settings) {
                // console.log(arguments);
                requestCB(settings);
                return;
                // var $el = jQuery(this.getEl());

                // data-videe-sm=""
                // data-videe-md=""
                // data-videe-lg=""
                // data-videe-autoplay="false"
                // data-videe-loop="false"
                // data-videe-playlist-mode=""
                // data-videe-volume="100"
                // data-videe-mute="false"
                // data-videe-load-settings="true" - invisible
                // data-videe-load-playlist="true" - invisible
                // data-videe-async="true"
                // data-videe-background="false"

                /*var settings = {
                    width: $el.find(".width").val(),
                    height: $el.find(".height").val(),
                    autoPlay: !!$el.find(".autoplay").attr("checked"),
                    loop: !!$el.find(".loop").attr("checked"),
                    playlistMode: $el.find('#playlist-type').val(),
                    volume: $el.find(".volume").val(),
                    mute: !!$el.find(".mute").attr("checked"),
                    loadSettings: true,
                    loadPlaylist: true,
                    async: !!$el.find(".async").attr("checked"),
                    background: !!$el.find(".background").attr("checked")
                };*/
                /*if (img) {
                    img.setAttribute("width", settings.width);
                    img.setAttribute("height", settings.height);
                    img.setAttribute("data-videe-autoplay", settings.autoplay);
                    img.setAttribute("data-videe-loop", settings.loop);
                    img.setAttribute("data-videe-playlist-mode", settings.playlistMode);
                    img.setAttribute("data-videe-volume", settings.volume);
                    img.setAttribute("data-videe-mute", settings.mute);
                    img.setAttribute("data-videe-async", settings.async);
                    img.setAttribute("data-videe-background", settings.background);

                    img.style.width = settings.width + "px";
                    img.style.height = settings.height + "px";
                    img.parentNode.style.width = settings.width + "px";
                    img.parentNode.style.height = settings.height + "px";
                    editor.nodeChanged();
                } else {
                    console.log(settings);
                    callback(settings);
                }*/
            }
        });

        function requestCB(settings) {
            $el.text('Load...');
            $('#wp-content-editor-container').append(spinner);
            videoRequest(id, function (response) {
                if (response.success) {
                    insertContentToEditor(getTag(id, response.items.id, settings));
                    $el.text('Insert');
                    spinner.remove();
                }
            });
        }
    });

    $playlistsList.on('click', '.videe-item-copy-code', function () {
        var _el = $(this),
            _id = _el.attr('data-videe-playlist-id'),
            title = _el.closest(".videe-item").find(".videe-item-title").text();
        prompt('Shortcode for:\n' + title + '', getTag(_id))
    });

    $playlistsList.on('click', '.videe-item-insert-code', function () {
        var _id = $(this).attr('data-videe-playlist-id');
        var srcs = [];
        var thumbs = [];
        $.each($(this).parent().parent().find('img'), function (index, img) {
            srcs.push($(img).attr('data-videe-video'));
            thumbs.push($(img).attr('src'));
        });
        var editor = tinyMCE.activeEditor;
        editor.fire('editVidee', {
            requestCallback: playListCB,
            videeSrc: srcs,
            thumbs: thumbs
        });

        function playListCB(settings) {
            insertContentToEditor(getTag(_id, null, settings));
        }

    });

    $widget.on('click', '.videe-upload-btn', function () {
        var uploadWindow = open(this.href);
        $(uploadWindow).on('load', function () {

        });
        $(window).on('message', function (e) {
            switch (e.originalEvent.data) {
                case 'closeMe':
                    uploadWindow.close();
                    break;
                case 'uploaded':
                    $('[data-domestic=0]', $filter).click();
                    break;
            }
        });
        $(window).on('beforeunload', function () {
            uploadWindow.close();
        });
        return false;
    });
    
    getData(tab);

    /*== Drug'n'drop ==*/

    var dragManager = new function () {
        var dragObject = {};
        var self = this;
        var isPlaylist;

        function onMouseDown(e) {
            if (e.which != 1) return;
            var classList = e.target.classList;
            if (classList.contains("videe-item-image") || classList.contains("videe-item-thumbnail")) {
                isPlaylist = classList.contains("playlist");

                var elem = $(e.target).closest('.videe-item');
            }
            if (!elem) return;

            dragObject.elem = elem[0];

            dragObject.downX = e.pageX;
            dragObject.downY = e.pageY;

            return false;
        }

        function onMouseMove(e) {
            if (!dragObject.elem) return;

            if (!dragObject.video) {
                var moveX = e.pageX - dragObject.downX;
                var moveY = e.pageY - dragObject.downY;

                if (Math.abs(moveX) < 3 && Math.abs(moveY) < 3) {
                    return;
                }

                dragObject.video = createVidee(e);
                if (!dragObject.video) {
                    dragObject = {};
                    return;
                }

                var coords = getCoords(dragObject.video);
                dragObject.shiftX = dragObject.downX - coords.left;
                dragObject.shiftY = dragObject.downY - coords.top;

                startDrag(e);
            }

            dragObject.video.style.left = e.pageX - dragObject.shiftX + 'px';
            dragObject.video.style.top = e.pageY - dragObject.shiftY + 'px';

            return false;
        }

        function onMouseUp(e) {
            $('body').removeClass('grabbing');
            if (dragObject.video) {
                finishDrag(e);
            }

            dragObject = {};
        }

        function finishDrag(e) {
            var dropElem = findDroppable(e);

            if (!dropElem) {
                self.onDragCancel(dragObject);
            } else {
                self.onDragEnd(dragObject, dropElem);
            }
        }

        function createVidee(e) {

            var video = dragObject.elem;
            var old = {
                parent: video.parentNode,
                nextSibling: video.nextSibling,
                position: video.position || '',
                left: video.left || '',
                top: video.top || '',
                zIndex: video.zIndex || ''
            };

            video.rollback = function () {
                old.parent.insertBefore(video, old.nextSibling);
                video.style.position = old.position;
                video.style.left = old.left;
                video.style.top = old.top;
                video.style.zIndex = old.zIndex
            };

            return video;
        }

        function startDrag(e) {
            var video = dragObject.video;

            document.body.appendChild(video);
            $('body').addClass('grabbing');
            video.style.zIndex = 9999;
            video.style.position = 'absolute';
        }

        function findDroppable(event) {
            var $video = $(dragObject.video);
            $video.hide();
            var elem = document.elementFromPoint(event.clientX, event.clientY);
            $video.css('display', 'inline-block');

            if (elem == null) {
                return null;
            }

            return $(elem).closest('#wp-content-editor-container')[0];
        }

        document.onmouseup = onMouseUp;
        document.onmousemove = onMouseMove;
        document.onmousedown = onMouseDown;


        this.onDragEnd = function (dragObject, dropElem) {

            var container = $(dropElem);
            var id;
            var spinner =
                $('<div class="videe-spinner videe-spinner-tiny" style="top: 140px;\
                    position: absolute; left: 0; right: 0;\
                    z-index: 100;"><div class="videe-spinner-rotator"></div></div>');

            var editor = tinyMCE.activeEditor;
            editor.render();

            function dropCB(settings) {
                container.append(spinner);
                videoRequest(id, function (response) {
                    if (response.success) {
                        spinner.remove();
                        insertContentToEditor(getTag(id, response.items.id, settings));
                    }
                });
            }

            if (!isPlaylist) {
                id = $(dragObject.video).attr('data-videe-video-id');
                editor.fire('editVidee', {
                    requestCallback: dropCB,
                    videeSrc: $(dragObject.video).attr('data-videe-src')
                });
            } else {
                var _id = $(dragObject.video).attr('data-videe-playlist-id');
                spinner.remove();
                insertContentToEditor(getTag(_id));
            }
            self.onDragCancel(dragObject);
        };
        this.onDragCancel = function (dragObject) {
            dragObject.video.rollback();
        };

    };

    function getCoords(elem) {
        var box = elem.getBoundingClientRect();
        return {
            top: box.top + pageYOffset,
            left: box.left + pageXOffset
        };
    }

})(jQuery);
