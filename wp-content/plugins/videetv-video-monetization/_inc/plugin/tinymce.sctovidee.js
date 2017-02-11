(function() {
	var registerCss = function(editor){
		var img = Config().getApiUrl() + "_inc/images/video_icon_big.png";
		var style = [
			".videe-videowrap {",
			"	position: relative;",
			"	background: #000;",
			"}",
			".videe-videowrap:after {",
			"    content: '';",
			"    position: absolute;",
			"    top: 0;",
			"    bottom: 0;",
			"    left: 0;",
			"    right: 0;",
			"    background: url(" + img + ") no-repeat center center / contain;",
			"    pointer-events: none;",
			"}"
		].join("\n");
		var css = document.createElement("style");
		css.type = "text/css";
		css.innerHTML = style;

		setTimeout(function(){
			editor.getDoc().head.appendChild(css)
		}, 1000)
	};

	var scToVid = function(co) {
        var exp = /\[videe_widget([^\]]*)\]/g;
		return co.replace(exp, function(shortTag, b, position, baseStr){
			var params = b.split(" ").reduce(function(params, cur){
				var param = cur.split("=");
				if (param.length==1) params[param[0]] = true;
				else params[param[0]] = param[1];
				return params
			},{});
			var subStr = baseStr.substring(shortTag.length+position);
			
			var width, height, autosize;
			
			if(isPositiveNumber(params.width) && isPositiveNumber(params.height)) {
				width = params.width;
				height = params.height;
				autosize = params.autosize;
			} else {
				width = 640;
				height = 480;
				autosize = true;
			}
			
			var html = '<p class="videe-videowrap" style="width:' + width + 'px; height:' + height + 'px;">';
			
			
			
	        html += '<img src=' + params.thumbnail.split(",")[0] +
				' width="' + width + '" height="' + height;
	        html += '"   style="width:' + params.width + 'px; height:' + params.height + 'px; max-width: none";';
	        // html += '    data-videe-sm="' + params.sm + '"';
	        // html += '    data-videe-md="' + params.md + '"';
	        // html += '    data-videe-lg="' + params.lg + '"';
	        html += '    data-videe-autoplay="' + params.autoplay + '"';
			html += '    data-videe-autosize="' + autosize + '"';
	        html += '    data-videe-loop="' + params.loop + '"';
	        // html += '    data-videe-playlist-mode="' + params['playlist-mode'] + '"';
	        html += '    data-videe-volume="' + params.volume + '"';
	        html += '    data-videe-mute="' + params.mute + '"';
	        html += '    data-videe-async="' + params.async + '"';
	        html += '    data-videe-background="' + params.background + '"';
	        html += '    data-videe-thumbnail="' + params.thumbnail + '" data-mce-placeholder="videe"'
	        html += '    data-videe-' + (params.videoId ? 'videoid="' + params.videoId : 'playlistid="' + params.playlistId) + '">';
	        html += '</p>';
	        html += (subStr.length > 4 && subStr.indexOf("</p>") != 0) ? "<p>" : "";
			return html
		});
	};

	var vidToSc = function(co) {
		function getAttr(s, n) {
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ? tinyMCE.DOM.decode(n[1]) : '';
		}
		return co.replace(/(?:<p\sclass="videe-videowrap"[^>]*>)(<img[^>]+>)(?:<\/p>)*/g, function(a,im) {
			var width = getAttr(im, 'width');
			var height = getAttr(im, 'height');
			var autosize = getAttr(im, 'autosize');
			// var sm = getAttr(im, 'data-videe-sm');
			// var md = getAttr(im, 'data-videe-md');
			// var lg = getAttr(im, 'data-videe-lg');
			var autoPlay = getAttr(im, 'data-videe-autoplay');
			var loop = getAttr(im, 'data-videe-loop');
			// var playlistMode = getAttr(im, 'data-videe-playlist-mode');
			var volume = getAttr(im, 'data-videe-volume');
			var mute = getAttr(im, 'data-videe-mute');
			var async = getAttr(im, 'data-videe-async');
			var background = getAttr(im, 'data-videe-background');
			var thumbnail = getAttr(im, 'data-videe-thumbnail');
			var videoId = getAttr(im, 'data-videe-videoid');
			var playlistId = getAttr(im, 'data-videe-playlistid');
			var id = (videoId) ? "videoId=" + videoId : "playlistId=" + playlistId;
			return "<p>"+
				"[videe_widget" +
				" width="+ width +
				" height="+ height +
				" autosize=" + autosize +
				// " sm="+ sm +
				// " md="+ md +
				// " lg="+ lg +
				" autoplay="+ autoPlay +
				" loop="+ loop +
				" volume="+ volume +
				" thumbnail="+ thumbnail +
				// " playlist-mode=" + playlistMode +
				" mute=" + mute +
				" async=" + async +
				" background=" + background +
				" " + id +
				"]</p>"
		});
	};

    function wrapVideeVideo (editor, node) {
        var newNode = document.createElement("p");
        node.parentNode.insertBefore(newNode, node.nextSibling);
        var rng = editor.selection.getRng();
        rng.setStart(newNode, 0);
        rng.setEnd(newNode, 0);
        editor.selection.setRng(rng);
    }

	tinyMCE.PluginManager.add('sctovidee', function(editor, url) {
		registerCss(editor);

		//replace shortcode before editor content set
		editor.onBeforeExecCommand.add(function (editor, command, ui, val) {
            var node = editor.selection.getNode();
            if (node) {
                if ((command == 'mceInsertContent' || command == "WP_Link") &&
                    ((node.className == "videe-videowrap") ||
                    (node.parentNode && node.parentNode.className == "videe-videowrap"))) {
                    wrapVideeVideo(editor, node)
                }
                if(node.id == "tinymce" && command == "WP_Link"){
                    for(var i = 0; i < node.childNodes.length; i++){
                        var child = node.childNodes[i];
                        if(child.className == "videe-videowrap"){
                            node = child;
                            wrapVideeVideo(editor, node);
                            break;
                        }
                    }
                }
                if((node.nodeName == "P" && node.children.lendth > 0) || node.parentNode.nodeName == "P"){
                    wrapVideeVideo(editor, node)
                }
            }
		});

		editor.onBeforeSetContent.add(function(editor, o) {
			o.content = scToVid(o.content);
		});

		editor.onKeyDown.addToTop(function(editor, e){
			var node = editor.selection.getNode();

			if (node.className == "videe-videowrap")
				node = node;
			else if (node.parentNode && node.parentNode.className == "videe-videowrap") {
				node = node.parentNode
			} else if(node.id == "tinymce"){
				for(var i = 0; i < node.childNodes.length; i++){
					var child = node.childNodes[i];
					if(child.className == "videe-videowrap"){
						node = child;
						break;
					}
				}
			} else {
				node = null
			}

			if (node && node.id != "tinymce") {
				if (e.keyCode == 46 || e.keyCode == 8) { //delete
					if(node.remove){
						node.remove()
					} else {
						jQuery(node).remove(); //FIX IE-10
					}
				}
				if (e.keyCode == 13) { //line break
					wrapVideeVideo(editor, node);
					return
				}
				e.preventDefault()
			}
		});

		//replace shortcode as its inserted into editor (which uses the exec command)
		editor.onExecCommand.add(function(editor, cmd) {
		    if (cmd ==='mceInsertContent'){
				tinyMCE.activeEditor.setContent( scToVid(tinyMCE.activeEditor.getContent()) );
			}
		});

		editor.onInit.add(function(editor) {
			//remove the residue of preview
			editor.$ = jQuery;
			editor.$(editor.contentDocument).on('DOMNodeRemoved', function(node) {
				if (
					node.relatedNode.className === 'videe-videowrap' &&
					node.target.tagName === 'IMG'
				) {
					node.relatedNode.parentNode.removeChild(node.relatedNode);
				}
			});
		});

		editor.on('ObjectResized', function(e) {
			if (e.target.parentNode.className == "videe-videowrap") {
				e.target.parentNode.style.width = e.width + "px";
				e.target.parentNode.style.height = e.height + "px";
				e.target.parentNode.dataset.mceStyle = "width: "+e.width + "px; "+ "height: "+e.height + "px;";
				e.target.width = e.width;
				e.target.height = e.height
			}
		});
		// //replace the image back to shortcode on save
		editor.onPostProcess.add(function(editor, o) {
			if (o.get)
				o.content = vidToSc(o.content);
		});
	});


})();
