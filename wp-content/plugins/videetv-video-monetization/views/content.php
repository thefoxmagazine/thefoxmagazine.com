<?php global $locator; if (!$locator) exit('Forbidden. hacking attempt'); ?>

<style>
	#wpcontent {
		padding-left: 0 !important;
	}

	#wpwrap, #wpbody {
		height: 100%;
		overflow: hidden;
	}

	#wpfooter {
		display: none;
	}

	#wpbody, #wpbody-content, #videe-constructor, #videe-constructor-frame {
		width: 100%;
		height: 100%;
	}

</style>

<div id="videe-constructor"></div>

<script type="text/javascript">
	var root = document.getElementById('videe-constructor');
	setTimeout(function () {
		root.style.height = "calc(100% - " + root.offsetTop + "px)";
	}, 0);
	window.onresize = function () {
		setTimeout(function () {
			root.style.height = "calc(100% - " + root.offsetTop + "px)";
		}, 0)
	}
</script>

<script type="text/javascript">
	var url = location.protocol
		+ "<?php echo $locator->config['wpConstructorUrl']; ?>" // Original aka prod
		//+ "//localhost:7005/" // For local dev
		+ "?wp_token=<?php echo $locator->getOption('token')?>"
		+ "&wp_url=http://<?php echo $_SERVER['HTTP_HOST']?><?php echo $_SERVER['REQUEST_URI']?>"
		+ location.hash;

	jQuery('#videe-constructor').html('<iframe name="target" id="videe-constructor-frame" src="' + url + '"></iframe>');

	window.addEventListener('message', function (event) {
		if (typeof event.data == 'string') {

			var data = JSON.parse(event.data);
			var postUrl = location.pathname + '?page=videe-key-config';
			
			if (data.type === 'credentials') {
				jQuery.ajax({
					url: postUrl,
					type: 'POST',
					data: {
						token: data.token,
						userId: data.user,
						action: 'enter-key'
					}
				}).done(function() {
					if (data.redirectUrl) {
						setTimeout(function() {
							window.location = data.redirectUrl;
						}, 0);	
					} else {
						setTimeout(function() {
							window.location.reload();
						}, 0);						
					}
				});
			}

			if (data.type === 'signout') {
				jQuery.ajax({
					url: postUrl,
					type: 'POST',

					data: {
						key: null,
						user: null,
						action: 'enter-key'
					}
				});
			}
			
			
			if (data.type === 'waitVerify') {
				
				jQuery.ajax({
					url: postUrl,
					type: 'POST',

					data: {
						action: 'waitVerify',
						email: data.email
					}
				});
			}

			if (data.type === 'change-tab') {
				location.hash = "#_"
				highlightСurrentSubmenu(data.tab)
			}

			if (data.type === 'redirect-parent') {
				setTimeout(function() {
					window.location = data.url; 
				}, 100);
			}

			if (data.type === 'page-reload') {
				setTimeout(function() {
					location.reload();
				}, 100);
			}
		}
	}, false);
	
	jQuery('#videe-constructor-frame').load(function () {
		var iframe = document.getElementById('videe-constructor-frame').contentWindow;
		// get Wordpress install subfolder
		var subfolder = location.pathname.substr(0, location.pathname.indexOf('/wp-admin'));
		
		iframe.postMessage(JSON.stringify({
			type: 'auth',
			token: '<?php echo $locator->getOption('token')?>',
			user: '<?php echo $locator->getOption('userId')?>',
			domain: location.protocol + '//' + location.host,
			email: '<?php echo $locator->getOption('email')?>',
			wordpressSubfolder: subfolder,
			verified: '<?php echo $locator->getOption('verified')? "true": "false" ?>',
			waitVerify: '<?php echo $locator->getOption('waitVerify')? "true": "false" ?>',
			version: '<?php echo $locator->config['videeVersion']?>'
		}), '*');
		
		jQuery("#verify").on("click", function(e) {
			e.preventDefault();
			iframe.postMessage(JSON.stringify({
				type: 'verify'
			}), '*');			
		});
		
	});

	jQuery(window).on("hashchange", function () {
		if (!location.ignoreThisHashchange) {
			var iframe = document.getElementById('videe-constructor-frame').contentWindow;
			iframe.postMessage(JSON.stringify({
				type: 'navigate',
				hash: location.hash
			}), '*');
			highlightСurrentSubmenu();
		}
		location.ignoreThisHashchange = false;
	})

	highlightСurrentSubmenu()

	function highlightСurrentSubmenu(tab) {
		var hash;
		if (tab) {
			hash = "#tab=" + tab
			location.ignoreThisHashchange = true
			location.hash = hash
		} else {
			var hash = location.hash;
			if (!hash || hash == "#" || hash == "#_") hash = "#tab=library";
		}
		var $menu = jQuery("#toplevel_page_videe-key-config");
		$menu.find(".wp-submenu .current").removeClass("current");
		$menu.find(".wp-submenu a[href*='" + hash + "']").closest("li").addClass("current");
	}
	// ToDO Refactor this shit
	var iframe = jQuery('#videe-constructor-frame');
	iframe.hide();
	var div = jQuery('<div class="videe-spinner videe-spinner-tiny"><div class="videe-spinner-rotator"></div></div>');
	div.css({
		margin: "auto",
		position: "absolute",
		top: 0,
		left: 0,
		right: 0,
		bottom: 0,
		fontSize: '80px',
		background: '#F1F1F1'
	});
	jQuery('#videe-constructor').append(div);

	iframe[0].onload = function () {
		div.remove();
		iframe.show();
	};

</script>
