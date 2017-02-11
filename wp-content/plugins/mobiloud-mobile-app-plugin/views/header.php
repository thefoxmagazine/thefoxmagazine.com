<div id="wrap" class="mobiloud">
	<div id="ml-header">
		<a href="<?php echo admin_url( 'admin.php?page=mobiloud' ); ?>" class="ml-logo">
			<img src="<?php echo MOBILOUD_PLUGIN_URL; ?>assets/img/mobiloud-logo-black.png"/>
		</a>

		<?php if ( strlen( Mobiloud::get_option( 'ml_pb_app_id' ) ) <= 0 && strlen( Mobiloud::get_option( 'ml_pb_secret_key' ) ) <= 0 ): ?>
			<a href="http://www.mobiloud.com/publish/?email=<?php echo Mobiloud::get_option( 'ml_user_email', $current_user->user_email ); ?>&utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=plugin-admin-header"
			   target="_blank" class="pricing-btn button-primary">
				Publish My App
			</a>
			<p class='ml-trial-msg'>When ready to go live, pick a plan &amp; publish your app.</p>
		<?php else: ?>
		<?php endif; ?>
	</div>