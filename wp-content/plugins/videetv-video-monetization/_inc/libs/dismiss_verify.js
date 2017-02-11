jQuery(document).ready(function() {
	
	jQuery('.dismiss-verify .notice-dismiss-button').on('click', function(event) {
		event.preventDefault();
		var parent = jQuery(this).parents('.dismiss-verify');
		
		var message = 'You can verify your account at any time on <a href="' + params.settingsUrl + '">Account Settings</a> page';
		
		jQuery('p:first', parent).html(message);
		jQuery.ajax({
			url: params.postUrl,
			type: 'POST',

			data: {
				action: 'dismissVerifyNotice'
			}
		}).done(function() {
			setTimeout(function() { parent.fadeOut();}, 2500);
		});
		
		return false;
	});
	
});


