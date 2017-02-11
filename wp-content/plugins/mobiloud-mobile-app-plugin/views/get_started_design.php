<div id="get_started_design" class="tabs-panel">
	<div class="get_started_options">
		<form method="post" action="<?php echo admin_url( 'admin.php?page=mobiloud' ); ?>">
			<?php wp_nonce_field( 'form-get_started_design' ); ?>
			<div class="ml-form-row">
				<label>Upload Your Logo</label>
				<input id="ml_preview_upload_image" type="text" size="36" name="ml_preview_upload_image"
					   value="<?php echo get_option( "ml_preview_upload_image" ); ?>"/>
				<input id="ml_preview_upload_image_button" type="button" value="Upload Image" class="browser button"/>
			</div>
			<?php $logoPath = Mobiloud::get_option( "ml_preview_upload_image" ); ?>
			<div
				class="ml-form-row ml-preview-upload-image-row" <?php echo ( strlen( $logoPath ) === 0 ) ? 'style="display:none;"' : ''; ?>>
				<div class='ml-preview-image-holder'>
					<img src='<?php echo $logoPath; ?>'/>
				</div>
				<a href='#' class='ml-preview-image-remove-btn'>Remove logo</a>
			</div>
			<div class="ml-form-row">
				<label>Navigation Bar Color</label>
				<input name="ml_preview_theme_color" id="ml_preview_theme_color" type="text"
					   value="<?php echo get_option( "ml_preview_theme_color" ); ?>"/>
			</div>
			<div class='ml-form-row'>
				<label>Show categories tab</label>
				<div class="ml-checkbox-wrap">
					<input type="checkbox" id="ml_show_android_cat_tabs" name="ml_show_android_cat_tabs"
						   value="true" <?php echo Mobiloud::get_option( 'ml_show_android_cat_tabs' ) ? 'checked' : ''; ?>/>
					<label for="ml_show_android_cat_tabs">Show categories tab menu at the top of the screen</label>
				</div>
			</div>

			<div class="ml-form-row ml-home-screen-label">
				<label>Articles Menu Item</label>
				<p>Enter the label you'd like to use for the 'Articles' menu item, letting users list your articles.</p>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_show_article_list_menu_item" name="ml_show_article_list_menu_item"
						   value="true" <?php echo Mobiloud::get_option( 'ml_show_article_list_menu_item' ) ? 'checked' : ''; ?>/>
					<label for="ml_show_article_list_menu_item">Show 'Article' list menu item</label>
				</div>
				<input type='text' id='ml_article_list_menu_item_title' name='ml_article_list_menu_item_title'
					   value='<?php echo Mobiloud::get_option( 'ml_article_list_menu_item_title', 'Articles' ); ?>'/>
			</div>
			<div class="ml-form-row">
				<label>Article List Style</label>
				<div class="ml-radio-wrap">
					<input type="radio" id="ml_article_list_view_type_extended" name="ml_article_list_view_type"
						   value="extended" <?php echo get_option( 'ml_article_list_view_type', 'extended' ) == 'extended' ? 'checked' : ''; ?>/>
					<label for="ml_article_list_view_type_extended">Extended (large thumbnails)</label>
				</div>
				<div class="ml-radio-wrap">
					<input type="radio" id="ml_article_list_view_type_compact" name="ml_article_list_view_type"
						   value="compact" <?php echo get_option( 'ml_article_list_view_type' ) == 'compact' ? 'checked' : ''; ?>/>
					<label for="ml_article_list_view_type_compact">Compact (square thumbnails)</label>
				</div>
			</div>

			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
									 value="Save Changes"></p>
		</form>
	</div>
	<?php
	$user_email     = Mobiloud::get_option( 'ml_user_email' );
	$user_name      = Mobiloud::get_option( 'ml_user_name' );
	$user_site      = Mobiloud::get_option( 'ml_user_site' );
	$plugin_url     = plugins_url();
	$plugin_version = MOBILOUD_PLUGIN_VERSION;

	$http_prefix = 'http';
	if ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) {
		$http_prefix = 'https';
	}
	?>
	<div class="get_started_preview shrinked">
		<div class="updated" style="" id="ml_reminder">This is just a mockup. Use the <a class="sim-btn thickbox"
																						 target="_blank"
																						 href="<?php echo $http_prefix; ?>://www.mobiloud.com/simulator/?name=<?php echo urlencode( esc_attr( $user_name ) ); ?>&email=<?php echo urlencode( esc_attr( $user_email ) ); ?>&site=<?php echo urlencode( esc_url( $user_site ) ); ?>&p=<?php echo urlencode( esc_url( $plugin_url ) ); ?>&v=<?php echo urlencode( esc_attr( $plugin_version ) ); ?>">Live
				Preview</a> to test your app.
		</div>
		<div class="ml-preview-app" onlclick=""></div>
		<div id="ml_preview_loading"></div>
	</div>

	<div class="get_started_preview">

		<style type="text/css">

		</style>
		<div id="get_started_menu_config" style="text-align:center;">
			<p>
				Test your app in the Live Preview to see the result.
			</p>


			<?php add_thickbox(); ?>
			<a href="<?php echo $http_prefix; ?>://www.mobiloud.com/simulator/?name=<?php echo urlencode( esc_attr( $user_name ) ); ?>&email=<?php echo urlencode( esc_attr( $user_email ) ); ?>&site=<?php echo urlencode( esc_url( $user_site ) ); ?>&p=<?php echo urlencode( esc_url( $plugin_url ) ); ?>&v=<?php echo urlencode( esc_attr( $plugin_version ) ); ?>"
			   target="_blank" class="sim-btn thickbox button button-hero button-primary">
				See Live Preview
			</a>
			<div class="spacer"></div>
			<?php $app_id = get_option( 'ml_pb_app_id' );
			if ( empty( $app_id ) ): ?>
				<p>To get a preview of your app on your own iOS or Android<br>device, follow our <a
						href="http://www.mobiloud.com/help/knowledge-base/preview/<?php echo get_option( 'affiliate_link', null ); ?>?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=plugin-admin-get-started"
						target="_blank">step-by-step instructions</a>.</p>
				<div class="spacer"></div>
				<p>Ready to have your app published?</p>
			<a href="http://www.mobiloud.com/publish/?email=<?php echo Mobiloud::get_option( 'ml_user_email', $current_user->user_email ); ?>&utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=plugin-admin-get-started"
				   target="_blank" class="pricing-btn button button-hero button-primary">
					See Pricing &amp; Publish My App
				</a>

			<?php endif; ?>

			<p>Any questions? <a class="contact" href="mailto:support@mobiloud.com">Contact us now</a>.</p>

		</div>
		<div style='clear:both;'></div>

	</div>
