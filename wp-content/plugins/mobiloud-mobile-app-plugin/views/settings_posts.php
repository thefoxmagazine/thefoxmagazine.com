<div id="ml_settings_posts" class="tabs-panel ml-compact">
	<form method="post" action="<?php echo admin_url( 'admin.php?page=mobiloud_settings&tab=posts' ); ?>">
		<?php wp_nonce_field( 'form-settings_posts' ); ?>

		<p>This page lets you configure a number of options affecting how content is displayed in your app, including
			whether featured images, post meta information like author and date are shown on screen.</p>

		<p>For guidance on these settings, check out our <a
				href="http://www.mobiloud.com/help/knowledge-base/how-can-i-customize-the-article-screen/<?php echo get_option( 'affiliate_link', null ); ?>?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=settings_content"
				target="_blank">Knowledge Base</a>.</p>


		<p>Any questions or need some help? Contact us at <a class="contact" href="mailto:support@mobiloud.com">support@mobiloud.com</a></p>

		<h3>Post and Page screen settings</h3>


		<h4>Featured image in the article screen</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>You can switch display or hide the featured image in the article screen. You can also add featured
					images manually using the Editor functionality, <a target="_blank"
																	   href="http://www.mobiloud.com/help/knowledge-base/featured-images/?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=content_page">read
						our guide</a>.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_show_article_featuredimage" name="ml_show_article_featuredimage"
						   value="true" <?php echo Mobiloud::get_option( 'ml_show_article_featuredimage' ) ? 'checked' : ''; ?>/>
					<label for="ml_show_article_featuredimage">Show featured image</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_original_size_featured_image" name="ml_original_size_featured_image"
						   value="true" <?php echo Mobiloud::get_option( 'ml_original_size_featured_image' ) ? 'checked' : ''; ?>/>
					<label for="ml_original_size_featured_image">Show featured images respecting the original image
						proportions</label>
				</div>

			</div>
		</div>


		<h4>Image galleries</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Your app will ignore links attached to images to ensure that these open in the built-in image
					gallery. If instead you'd prefer image links to work inside the app, you can change this setting
					accordingly.</p>
				<p>As an exception, say to allow an image banner within the content to load an external link while
					ensuring other images are always opened in the gallery, you can assign the class
					<i>ml_followlinks</i> to the image banner.</p>

			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_followimagelinks" name="ml_followimagelinks"
						   value="1" <?php echo Mobiloud::get_option( 'ml_followimagelinks' ) ? 'checked' : ''; ?>/>
					<label for="ml_followimagelinks">Load links instead of image gallery for images with links</label>
				</div>
			</div>
		</div>


		<h4>Post and page meta information</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Change which meta elements of your posts and pages should be displayed in the post and page
					screens.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_post_author_enabled" name="ml_post_author_enabled"
						   value="true" <?php echo Mobiloud::get_option( 'ml_post_author_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_post_author_enabled">Show author in posts</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_page_author_enabled" name="ml_page_author_enabled"
						   value="true" <?php echo Mobiloud::get_option( 'ml_page_author_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_page_author_enabled">Show author in pages</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_post_date_enabled" name="ml_post_date_enabled"
						   value="true" <?php echo Mobiloud::get_option( 'ml_post_date_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_post_date_enabled">Show date in posts</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_page_date_enabled" name="ml_page_date_enabled"
						   value="true" <?php echo Mobiloud::get_option( 'ml_page_date_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_page_date_enabled">Show date in pages</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_post_title_enabled" name="ml_post_title_enabled"
						   value="true" <?php echo Mobiloud::get_option( 'ml_post_title_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_post_title_enabled">Show title in posts</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_page_title_enabled" name="ml_page_title_enabled"
						   value="true" <?php echo Mobiloud::get_option( 'ml_page_title_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_page_title_enabled">Show title in pages</label>
				</div>
			</div>
		</div>

		<h4>Right To Left Support</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>If your content is in Arabic and Hebrew, enable support for RTL.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_rtl_text_enable" name="ml_rtl_text_enable"
						   value="true" <?php echo Mobiloud::get_option( 'ml_rtl_text_enable' ) ? 'checked' : ''; ?>/>
					<label for="ml_rtl_text_enable">Enable Right-To-Left text</label>
				</div>
			</div>
		</div>

		<h4>Internal links</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Your app can open internal links (e.g. to posts, pages or categories) and open them in the native article or category views. You can disable this and links will open in the internal browser normally used for external links.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_internal_links" name="ml_internal_links"
						   value="true" <?php echo Mobiloud::get_option( 'ml_internal_links' ) ? 'checked' : ''; ?>/>
					<label for="ml_internal_links">Open internal links in native views</label>
				</div>
			</div>
		</div>


		<h3>Commenting settings</h3>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Select the commenting system you'd like to use in your app.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<div class="ml-radio-wrap">
						<input type="radio" id="ml_comments_system_wordpress" name="ml_comments_system"
							   value="wordpress" <?php echo get_option( 'ml_comments_system', 'wordpress' ) == 'wordpress' ? 'checked' : ''; ?>/>
						<label for="ml_comments_system_wordpress">Wordpress</label>
					</div>
					<div class="ml-radio-wrap">
						<input type="radio" id="ml_comments_system_disqus" name="ml_comments_system"
							   value="disqus" <?php echo get_option( 'ml_comments_system', 'wordpress' ) == 'disqus' ? 'checked' : ''; ?>/>
						<label for="ml_comments_system_disqus">Disqus</label>
					</div>
					<div class="ml-radio-wrap">
						<input type="radio" id="ml_comments_system_facebook" name="ml_comments_system"
							   value="facebook" <?php echo get_option( 'ml_comments_system', 'wordpress' ) == 'facebook' ? 'checked' : ''; ?>/>
						<label for="ml_comments_system_facebook">Facebook Comments</label>
					</div>
					<div class="ml-radio-wrap">
						<input type="radio" id="ml_comments_system_disabled" name="ml_comments_system"
							   value="disabled" <?php echo get_option( 'ml_comments_system', 'wordpress' ) == 'disabled' ? 'checked' : ''; ?>/>
						<label for="ml_comments_system_disabled">Comments should be disabled</label>
					</div>
				</div>
				<div
					class="ml-disqus-row ml-form-row" <?php echo Mobiloud::get_option( 'ml_comments_system', 'wordpress' ) == 'disqus' ? '' : 'style="display: none;"'; ?>>
					<label>Disqus shortname <span class="required">*</span></label>
					<input name="ml_disqus_shortname" id="ml_disqus_shortname" type="text"
						   value="<?php echo get_option( "ml_disqus_shortname", '' ); ?>"/>
					<p>A shortname is the unique identifier assigned to a Disqus site. All the comments posted to a site
						are referenced with the shortname.
						See <a href="#">how to find your shortname</a>.</p>
				</div>
			</div>
		</div>
		<?php if ( strlen( Mobiloud::get_option( 'ml_pb_app_id' ) ) > 0 && Mobiloud::get_option( 'ml_pb_app_id' ) < "543e7b3f1d0ab16d148b4599" ): ?>
			<div class='update-nag'>
				<p>The functionality above is new. Your app might require to be updated for these settings to take
					effect.</p>
				<p>Should you have any questions or to request an update, get in touch at <a
						href='mailto:support@mobiloud.com'>support@mobiloud.com</a>.</p>
			</div>
		<?php endif; ?>


		<h3>Advanced Settings</h3>

		<h4>Children Page Navigation</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Did you built a site with a complex page hierarchy and you'd like to have this available in the app?
					The page hierarchy navigation feature allows users to see a list of children pages at the bottom of
					every page within your app.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_hierarchical_pages_enabled" name="ml_hierarchical_pages_enabled"
						   value="true" <?php echo Mobiloud::get_option( 'ml_hierarchical_pages_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_hierarchical_pages_enabled">Enable page hierarchy navigation</label>
				</div>
			</div>
		</div>

		<h4>Enable caching</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Allow the plugin to cache the response, storing it in your WordPress database.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_cache_enabled" name="ml_cache_enabled"
						   value="true" <?php echo Mobiloud::get_option( 'ml_cache_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_cache_enabled">Enable caching engine</label>
				</div>
			</div>
		</div>

		<h4>Enable image preload at Mobile Application</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Only if this option turned on, then the preloading of images at startup will occur.</p>
				<p>Because this is both memory intensive and CPU intensive for 512 MB devices (iPhone 4/4S, iPad 2, iPad Mini 1 - non-retina), this will default turned off on these devices, no matter the current value.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_image_cache_preload" name="ml_image_cache_preload"
						   value="true" <?php echo Mobiloud::get_option( 'ml_image_cache_preload' ) ? 'checked' : ''; ?>/>
					<label for="ml_image_cache_preload">Enable preloading of images</label>
				</div>
			</div>
		</div>

		<h4>Remove unused shortcodes</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
			<p>To remove any shortcodes that remain visibile in the app, you can enable this feature.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_remove_unused_shortcodes" name="ml_remove_unused_shortcodes"
						   value="true" <?php echo Mobiloud::get_option( 'ml_remove_unused_shortcodes', true) ? 'checked' : ''; ?>/>
					<label for="ml_remove_unused_shortcodes">Remove unused shortcodes</label>
				</div>
			</div>
		</div>

		<h4>Support Really Simple SSL plugin</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
			<p>In fact this plugin break articles list. Please turn on this option if you are using it.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_fix_rsssl" name="ml_fix_rsssl"
						   value="true" <?php echo Mobiloud::get_option( 'ml_fix_rsssl') ? 'checked' : ''; ?>/>
					<label for="ml_fix_rsssl">Support Really Simple SSL plugin</label>
				</div>
			</div>
		</div>

		<h4>Alternative Featured Image</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>You can override the featured image used in article lists and at the top of every article with a
					secondary image you can define for every post.</p>
				<p>Install the <a href="https://wordpress.org/plugins/multiple-post-thumbnails/">Multiple Post
						Thumbnails</a> plugin and enter the ID of the secondary featured image field you've setup,
					normally "secondary-image".</p>
				<p>Alternatively enter the name of a custom field where you'll enter, for each post, the full URL of the
					alternative image.</p>

			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-left-align clearfix">
					<label class='ml-width-120' for="ml_custom_featured_image">Image ID</label>
					<input type="text" placeholder="Image ID" id="ml_custom_featured_image"
						   name="ml_custom_featured_image"
						   value="<?php echo esc_attr( Mobiloud::get_option( 'ml_custom_featured_image' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Override Article/Page URL with a custom field</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>When sharing your content, users will normally share the article's URL. For curation-based
					publications,
					though, you might want users to share the source for that story.</p>
				<p>Enter a custom field name to the right which you can fill for every post with the URL you want users
					to share.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-left-align clearfix">
					<label class='ml-width-120' for="ml_custom_field_url">URL Field Name</label>
					<input type="text" placeholder="Custom Field Name" id="ml_custom_field_url"
						   name="ml_custom_field_url"
						   value="<?php echo esc_attr( Mobiloud::get_option( 'ml_custom_field_url' ) ); ?>"/>
				</div>
			</div>
		</div>


		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
								 value="Save Changes"></p>
	</form>
</div>