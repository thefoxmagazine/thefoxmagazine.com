/*
 * Always get element with method this.getEl() instead this.$el
 * old WP version doesn't have this.$el
 * */

(function ($) {
    tinyMCE.PluginManager.add('editvidee', function (editor) {
        var toolbar;
        var videeToolbar;
        var iOS = tinyMCE.Env.iOS;
        var serializer;
        var DOM = tinyMCE.DOM;
        var settings = editor.settings;
        var Factory = tinyMCE.ui.Factory;
        var each = tinyMCE.each;
        var toolbarIsHidden;
        var currentSelection;
        var mceIframe;
        var wpAdminbar;

        var mceToolbar;
        var mceStatusbar;
        var wpStatusbar;

        var container;

        editor.on('preinit', function () {
            mceIframe = document.getElementById(editor.id + '_ifr');
            wpAdminbar = document.getElementById('wpadminbar');
            container = editor.getContainer();
            if (container) {
                mceToolbar = tinymce.$('.mce-toolbar-grp', container)[0];
                mceStatusbar = tinymce.$('.mce-statusbar', container)[0];
            }
            if (editor.id === 'content') {
                wpStatusbar = document.getElementById('post-status-info');
            }
        });

        if (!tinyMCE.$) {
            tinyMCE.$ = jQuery
        }

        function isPlaceholder(node) {
            return !!( editor.dom.getAttrib(node, 'data-mce-placeholder') || editor.dom.getAttrib(node, 'data-mce-object') );
        }

        editor.addButton('wp_videe_edit', {
            tooltip: 'Edit ', // trailing space is needed, used for context
            icon: 'dashicon dashicons-edit',
            onclick: function () {
                editVidee(editor.selection.getNode());
            }
        });

        editor.addButton('wp_videe_remove', {
            tooltip: 'Remove ', // trailing space is needed, used for context
            icon: 'dashicon dashicons-no',
            onclick: function () {
                removeVidee(editor.selection.getNode());
            }
        });

        function toolbarConfig() {
            var toolbarItems = [],
                buttonGroup;

            each(['wp_videe_edit', 'wp_videe_remove'], function (item) {
                var itemName;

                function bindSelectorChanged() {
                    var selection = editor.selection;

                    if (item.settings.stateSelector) {
                        selection.selectorChanged(item.settings.stateSelector, function (state) {
                            item.active(state);
                        }, true);
                    }

                    if (item.settings.disabledStateSelector) {
                        selection.selectorChanged(item.settings.disabledStateSelector, function (state) {
                            item.disabled(state);
                        });
                    }
                }

                if (item === '|') {
                    buttonGroup = null;
                } else {
                    if (Factory.has(item)) {
                        item = {
                            type: item
                        };

                        if (settings.toolbar_items_size) {
                            item.size = settings.toolbar_items_size;
                        }

                        toolbarItems.push(item);

                        buttonGroup = null;
                    } else {
                        if (!buttonGroup) {
                            buttonGroup = {
                                type: 'buttongroup',
                                items: []
                            };

                            toolbarItems.push(buttonGroup);
                        }

                        if (editor.buttons[item]) {
                            itemName = item;
                            item = editor.buttons[itemName];

                            if (typeof item === 'function') {
                                item = item();
                            }

                            item.type = item.type || 'button';

                            if (settings.toolbar_items_size) {
                                item.size = settings.toolbar_items_size;
                            }

                            item = Factory.create(item);
                            buttonGroup.items.push(item);

                            if (editor.initialized) {
                                bindSelectorChanged();
                            } else {
                                editor.on('init', bindSelectorChanged);
                            }
                        }
                    }
                }
            });

            return {
                type: 'panel',
                layout: 'stack',
                classes: 'toolbar-grp inline-toolbar-grp wp-image-toolbar videe-toolbar',
                ariaRoot: true,
                ariaRemember: true,
                items: [
                    {
                        type: 'toolbar',
                        layout: 'flow',
                        items: toolbarItems
                    }
                ]
            };
        }

        videeToolbar = Factory.create(toolbarConfig()).renderTo(document.body);
        videeToolbar.show = function () {
            var _this = this;
            var visibility = _this.getEl().style.visibility;
            var hidden = visibility == "hidden" || visibility == "";
            if (hidden) {
                _this.getEl().style.visibility = "visible"
            }
            return videeToolbar.visible(!0)
        };
        videeToolbar.hide = function () {
            var _this = this;
            var visibility = _this.getEl().style.visibility;
            var hidden = visibility == "hidden" || visibility == "";
            if (!hidden) {
                _this.getEl().style.visibility = "hidden"
            }
            return videeToolbar.visible(!1)
        };
        videeToolbar.reposition = function () {
            if (!currentSelection) {
                return this;
            }

            var scrollX = window.pageXOffset || document.documentElement.scrollLeft,
                scrollY = window.pageYOffset || document.documentElement.scrollTop,
                windowWidth = window.innerWidth,
                windowHeight = window.innerHeight,
                iframeRect = mceIframe ? mceIframe.getBoundingClientRect() : {
                    top: 0,
                    right: windowWidth,
                    bottom: windowHeight,
                    left: 0,
                    width: windowWidth,
                    height: windowHeight
                },
                toolbar = this.getEl(),
                toolbarWidth = toolbar.offsetWidth,
                toolbarHeight = toolbar.offsetHeight,
                selection = currentSelection.getBoundingClientRect(),
                selectionMiddle = ( selection.left + selection.right ) / 2,
                buffer = 5,
                margin = 8,
                spaceNeeded = toolbarHeight + margin + buffer,
                wpAdminbarBottom = wpAdminbar ? wpAdminbar.getBoundingClientRect().bottom : 0,
                mceToolbarBottom = mceToolbar ? mceToolbar.getBoundingClientRect().bottom : 0,
                mceStatusbarTop = mceStatusbar ? windowHeight - mceStatusbar.getBoundingClientRect().top : 0,
                wpStatusbarTop = wpStatusbar ? windowHeight - wpStatusbar.getBoundingClientRect().top : 0,
                blockedTop = Math.max(0, wpAdminbarBottom, mceToolbarBottom, iframeRect.top),
                blockedBottom = Math.max(0, mceStatusbarTop, wpStatusbarTop, windowHeight - iframeRect.bottom),
                spaceTop = selection.top + iframeRect.top - blockedTop,
                spaceBottom = windowHeight - iframeRect.top - selection.bottom - blockedBottom,
                editorHeight = windowHeight - blockedTop - blockedBottom,
                className = '',
                iosOffsetTop = 0,
                iosOffsetBottom = 0,
                top, left;

            if (spaceTop >= editorHeight || spaceBottom >= editorHeight) {
                return this.hide();
            }

            // Add offset in iOS to move the menu over the image, out of the way of the default iOS menu.
            if (tinymce.Env.iOS && currentSelection.nodeName === 'IMG') {
                iosOffsetTop = 54;
                iosOffsetBottom = 46;
            }

            if (this.bottom) {
                if (spaceBottom >= spaceNeeded) {
                    className = ' mce-arrow-up';
                    top = selection.bottom + iframeRect.top + scrollY - iosOffsetBottom;
                } else if (spaceTop >= spaceNeeded) {
                    className = ' mce-arrow-down';
                    top = selection.top + iframeRect.top + scrollY - toolbarHeight - margin + iosOffsetTop;
                }
            } else {
                if (spaceTop >= spaceNeeded) {
                    className = ' mce-arrow-down';
                    top = selection.top + iframeRect.top + scrollY - toolbarHeight - margin + iosOffsetTop;
                } else if (spaceBottom >= spaceNeeded && editorHeight / 2 > selection.bottom + iframeRect.top - blockedTop) {
                    className = ' mce-arrow-up';
                    top = selection.bottom + iframeRect.top + scrollY - iosOffsetBottom;
                }
            }

            if (typeof top === 'undefined') {
                top = scrollY + blockedTop + buffer + iosOffsetBottom;
            }

            left = selectionMiddle - toolbarWidth / 2 + iframeRect.left + scrollX;

            if (selection.left < 0 || selection.right > iframeRect.width) {
                left = iframeRect.left + scrollX + ( iframeRect.width - toolbarWidth ) / 2;
            } else if (toolbarWidth >= windowWidth) {
                className += ' mce-arrow-full';
                left = 0;
            } else if (( left < 0 && selection.left + toolbarWidth > windowWidth ) || ( left + toolbarWidth > windowWidth && selection.right - toolbarWidth < 0 )) {
                left = ( windowWidth - toolbarWidth ) / 2;
            } else if (left < iframeRect.left + scrollX) {
                className += ' mce-arrow-left';
                left = selection.left + iframeRect.left + scrollX;
            } else if (left + toolbarWidth > iframeRect.width + iframeRect.left + scrollX) {
                className += ' mce-arrow-right';
                left = selection.right - toolbarWidth + iframeRect.left + scrollX;
            }

            // No up/down arrows on the menu over images in iOS.
            if (tinymce.Env.iOS && currentSelection.nodeName === 'IMG') {
                className = className.replace(/ ?mce-arrow-(up|down)/g, '');
            }

            toolbar.className = toolbar.className.replace(/ ?mce-arrow-[\w]+/g, '') + className;

            DOM.setStyles(toolbar, {
                'left': left,
                'top': top
            });

            return this;
        };

        editor.on('nodechange', function (event) {
            var collapsed = editor.selection.isCollapsed();

            var args = {
                element: event.element,
                parents: event.parents,
                collapsed: collapsed
            };

            //editor.fire( 'wptoolbar', args );

            currentSelection = args.selection || args.element;
            var delay = iOS ? 350 : 10;
            if (event.element.nodeName == 'IMG' || isPlaceholder(event.element)) {
                var element = editor.selection.getNode();
                if (element.parentNode.className != "videe-videowrap") {
                    videeToolbar.hide();
                    return;
                }
            }

            setTimeout(function () {
                var element = editor.selection.getNode();
                if (element.nodeName === 'IMG' && isPlaceholder(element)) {
                    if (element.parentNode.className == "videe-videowrap") {
                        if (videeToolbar._visible) {
                            videeToolbar.reposition();
                        } else {
                            videeToolbar.reposition();
                            videeToolbar.show();
                        }
                    }
                } else {
                    videeToolbar.hide();
                }
            }, delay);
        });
        function hide() {
            if (!toolbarIsHidden) {
                videeToolbar.hide();
            }
        }

        videeToolbar.on('show', function () {
            this.reposition();
            toolbarIsHidden = false;
            if (this._visible) {
                DOM.addClass(this.getEl(), 'mce-inline-toolbar-grp-active');
            }
        });
        videeToolbar.on('hide', function () {
            toolbarIsHidden = true;
            DOM.removeClass(this.getEl(), 'mce-inline-toolbar-grp-active');
        });
        videeToolbar.on('keydown', function (event) {
            if (event.keyCode === 27) {
                hide();
                editor.focus();
            }
        });
        DOM.bind(window, 'resize scroll', function () {
            if (!toolbarIsHidden) {
                hide();
            }
        });

        editor.on('init', function () {
            editor.dom.bind(editor.getWin(), 'scroll', hide);
        });

        editor.on('blur hide', hide);

        editor.on('ObjectResizeStart', function (event) {});

        // 119 = F8
        editor.shortcuts.add('Alt+119', '', function () {
            var node = videeToolbar.find('toolbar')[0];

            if (node) {
                node.focus(true);
            }
        });
		


        function editVidee(img) {

            // var sm = img.getAttribute("data-videe-sm").split("x");
            // var md = img.getAttribute("data-videe-md").split("x");
            // var lg = img.getAttribute("data-videe-lg").split("x");
            var mute = img.getAttribute("data-videe-mute") == "true";
            var isPlaylist = !!img.getAttribute("data-videe-playlistid");
			var width = isPositiveNumber(img.getAttribute("width")) ? parseInt(img.getAttribute("width")) : "";
			var height = isPositiveNumber(img.getAttribute("height")) ? parseInt(img.getAttribute("height")) : "";
            var settings = {
				id: isPlaylist? img.getAttribute("data-videe-playlistid"): img.getAttribute("data-videe-videoid"),
				isPlaylist: isPlaylist,
                width: ko.observable(width),
                // smWidth: ko.observable(sm[0]),
                // mdWidth: ko.observable(md[0]),
                // lgWidth: ko.observable(lg[0]),
                height: ko.observable(height),
                // smHeight: ko.observable(sm[1]),
                // mdHeight: ko.observable(md[1]),
                // lgHeight: ko.observable(lg[1]),
                autoplay: ko.observable(img.getAttribute("data-videe-autoplay") == "true"),
				autosize: ko.observable(img.getAttribute("data-videe-autosize") == "true"),
                loop: ko.observable(img.getAttribute("data-videe-loop") == "true"),
                // playlistMode: ko.observable(img.getAttribute("data-videe-playlist-mode")),
                volume: ko.observable(mute ? 0 : img.getAttribute("data-videe-volume")),
                oldVolum: ko.observable(img.getAttribute("data-videe-volume")),
                mute: ko.observable(mute),
                async: ko.observable(img.getAttribute("data-videe-async") == "true"),
                background: ko.observable(img.getAttribute("data-videe-background") == "true"),
                loadSettings: true,
                loadPlaylist: true,
                edit: true,
                img: img
            };
			
			
            if(!isPlaylist){
                settings.thumb = ko.observable(img.getAttribute("data-videe-thumbnail"));
            } else {
                settings.thumbs = ko.observableArray(img.getAttribute("data-videe-thumbnail").split(','));
            }

            widgetViewModel.openModal(false, settings);
        }
		


        function removeVidee(node) {
            var wrap = editor.dom.getParent(node, '.videe-videowrap');

            if (wrap.nextSibling) {
                editor.selection.select(wrap.nextSibling);
            } else if (wrap.previousSibling) {
                editor.selection.select(wrap.previousSibling);
            } else {
                editor.selection.select(wrap.parentNode);
            }

            editor.selection.collapse(true);
            editor.dom.remove(wrap);

            editor.nodeChanged();
            editor.undoManager.add();
        }
    })
})(jQuery);
