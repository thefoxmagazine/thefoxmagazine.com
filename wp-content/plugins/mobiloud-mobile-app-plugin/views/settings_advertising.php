<?php
function echo_admob_ad_size($option_var_name, $default_value = 'medium') {
	$current_value = Mobiloud::get_option( $option_var_name, $default_value );
	?><tr valign="top" class="ml_admob_ads_wrap">
		<th scope="row">Ad size</th>
		<td class="ml_ad_interval">
			<select id="<?php echo esc_attr($option_var_name); ?>" name="<?php echo esc_attr($option_var_name); ?>">
				<?php foreach ( array( 'small' => 'Small', 'medium' => 'Medium', 'large' => 'Large' ) as $k => $value) {
					?><option
						value="<?php echo esc_attr( $k ); ?>" <?php selected($current_value, $k, true); ?>>
						<?php echo esc_html( $value ); ?>
					</option>
					<?php } ?>
			</select>
		</td>
	</tr><?php
}
?><div id="ml_settings_advertising" class="tabs-panel ml-compact">
	<form method="post" action="<?php echo admin_url( 'admin.php?page=mobiloud_settings&tab=advertising' ); ?>">
		<?php wp_nonce_field( 'form-settings_advertising' ); ?>
		<p>With MobiLoud's support for a number of networks and ad servers and the possibility of adding any image,
			javascript or HTML based ads within the contents of your app, the possibilities to monetize your content are
			endless.</p>
		<p>Before setting up advertising in your app <a
			href="http://www.mobiloud.com/help/knowledge-base/ads-banners/<?php echo get_option( 'affiliate_link', null ); ?>?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=ads_page"
			target="_blank">read the guide in our Knowledge Base</a>.</p>
		<p>Need any help? <a class="contact" href="mailto:support@mobiloud.com">Contact our support team</a>.</p>

		<h3>Banner, Interstitial and Native ads</h3>
		<p>The following ad platforms are supported:</p>
		<ul class="ml-info-list">
			<li><strong>AdMob</strong>: AdMob is a leading global mobile advertising network that helps you monetize
				your mobile apps. Banner ads and interstitials are supported.
			</li>
			<li><strong>MoPub</strong>: MoPub is a hosted ad serving solution built specifically for mobile publishers.
				Banner ads, interstitials and native ads are supported.
			</li>
			<li><strong>Google DFP</strong>: DoubleClick for Publishers (DFP) is a flexible ad server solution offered
				by Google. Banner ads and interstitials are supported.
			</li>
		</ul>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Select Advertising Platform</th>
					<td>
						<select id="ml_advertising_platform" name="ml_advertising_platform">
							<option
								value="admob" <?php echo Mobiloud::get_option( 'ml_advertising_platform' ) === 'admob' ? 'selected="selected"' : ''; ?>>
								AdMob
							</option>
							<option
								value="mopub" <?php echo Mobiloud::get_option( 'ml_advertising_platform' ) === 'mopub' ? 'selected="selected"' : ''; ?>>
								MoPub
							</option>
							<option
								value="gdfp" <?php echo Mobiloud::get_option( 'ml_advertising_platform' ) === 'gdfp' ? 'selected="selected"' : ''; ?>>
								Google DoubleClick (DFP)
							</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<h3>iOS Ad Units</h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">Phone Banner Unit ID</th>
							<td>
								<input type="text" id="ml_ios_phone_banner_unit_id" name="ml_ios_phone_banner_unit_id"
									value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_phone_banner_unit_id' ) ); ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Tablet Banner Unit ID</th>
							<td>
								<input type="text" id="ml_ios_tablet_banner_unit_id" name="ml_ios_tablet_banner_unit_id"
									value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_tablet_banner_unit_id' ) ); ?>"/>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="ml-radio-wrap">
									<input type="radio" id="ml_ios_banner_position_top" name="ml_ios_banner_position"
										value="top" <?php echo Mobiloud::get_option( 'ml_ios_banner_position', 'bottom' ) === 'top' ? 'checked' : ''; ?>>
									<label for="ml_ios_banner_position_top">Show banners at the top of the screen</label>
								</div>
								<div class="ml-radio-wrap">
									<input type="radio" id="ml_ios_banner_position_bottom" name="ml_ios_banner_position"
										value="bottom" <?php echo Mobiloud::get_option( 'ml_ios_banner_position', 'bottom' ) === 'bottom' ? 'checked' : ''; ?>>
									<label for="ml_ios_banner_position_bottom">Show banners at the bottom of the screen
										(recommended)</label>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<hr/>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">Interstitial Ad Unit ID</th>
							<td>
								<input type="text" id="ml_ios_interstitial_unit_id" name="ml_ios_interstitial_unit_id"
									value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_interstitial_unit_id' ) ); ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Interval</th>
							<td class="ml_ad_interval">
								<p>Show interstitial ads every<br/>
									article or page screens.</p>
								<select id="ml_ios_interstitial_interval" name="ml_ios_interstitial_interval">
									<?php for ( $a = 1; $a <= 10; $a ++ ): ?>
										<option
											value="<?php echo esc_attr( $a ); ?>" <?php echo Mobiloud::get_option( 'ml_ios_interstitial_interval', 5 ) == $a ? 'selected="selected"' : ''; ?>>
											<?php echo esc_html( $a ); ?>
										</option>
										<?php endfor; ?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="ml_native_ads_wrap ml_admob_ads_wrap">
					<hr/>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">Article List Native Ad Unit ID</th>
								<td>
									<input type="text" id="ml_ios_native_ad_unit_id" name="ml_ios_native_ad_unit_id"
										value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_native_ad_unit_id' ) ); ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">Interval</th>
								<td class="ml_ad_interval">
									<p>Show native ads every<br/>
										articles in the article list.</p>
									<select id="ml_ios_native_ad_interval" name="ml_ios_native_ad_interval">
										<?php for ( $a = 1; $a <= 10; $a ++ ): ?>
											<option
												value="<?php echo esc_attr( $a ); ?>" <?php echo Mobiloud::get_option( 'ml_ios_native_ad_interval', 5 ) == $a ? 'selected="selected"' : ''; ?>>
												<?php echo esc_html( $a ); ?>
											</option>
											<?php endfor; ?>
									</select>
								</td>
							</tr>
							<?php echo_admob_ad_size( 'ml_ios_native_ad_type' ); ?>
						</tbody>
					</table>
				</div>
				<div class="ml_admob_ads_wrap">
					<hr/>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">Article Screen Native Ad Unit ID</th>
								<td>
									<input type="text" id="ml_ios_native_ad_article_unit_id" name="ml_ios_native_ad_article_unit_id"
										value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_native_ad_article_unit_id' ) ); ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">At position</th>
								<td class="ml_ad_interval">
									<select id="ml_ios_native_ad_article_position" name="ml_ios_native_ad_article_position">
										<?php foreach ( array( 'top' => 'Above article', 'bottom' => 'Below article', 'both' => 'Both above and below' ) as $k => $value) {
											?><option
												value="<?php echo esc_attr( $k ); ?>" <?php echo Mobiloud::get_option( 'ml_ios_native_ad_article_position', 'both' ) == $k ? 'selected="selected"' : ''; ?>>
												<?php echo esc_html( $value ); ?>
											</option>
											<?php } ?>
									</select>
								</td>
							</tr>
							<?php echo_admob_ad_size( 'ml_ios_native_ad_article_type' ); ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class='ml-col-half'>
				<h3>Android Ad Units</h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">Phone Banner Unit ID</th>
							<td>
								<input type="text" id="ml_android_phone_banner_unit_id"
									name="ml_android_phone_banner_unit_id"
									value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_phone_banner_unit_id' ) ); ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Tablet Banner Unit ID</th>
							<td>
								<input type="text" id="ml_android_tablet_banner_unit_id"
									name="ml_android_tablet_banner_unit_id"
									value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_tablet_banner_unit_id' ) ); ?>"/>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="ml-radio-wrap">
									<input type="radio" id="ml_android_banner_position_top"
										name="ml_android_banner_position"
										value="top" <?php echo Mobiloud::get_option( 'ml_android_banner_position', 'bottom' ) === 'top' ? 'checked' : ''; ?>>
									<label for="ml_android_banner_position_top">Show banners at the top of the
										screen</label>
								</div>
								<div class="ml-radio-wrap">
									<input type="radio" id="ml_android_banner_position_bottom"
										name="ml_android_banner_position"
										value="bottom" <?php echo Mobiloud::get_option( 'ml_android_banner_position', 'bottom' ) === 'bottom' ? 'checked' : ''; ?>>
									<label for="ml_android_banner_position_bottom">Show banners at the bottom of the screen
										(recommended)</label>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<hr/>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">Interstitial Ad Unit ID</th>
							<td>
								<input type="text" id="ml_android_interstitial_unit_id"
									name="ml_android_interstitial_unit_id"
									value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_interstitial_unit_id' ) ); ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Interval</th>
							<td class="ml_ad_interval">
								<p>Show interstitial ads every<br/>
									article or page screens.</p>
								<select id="ml_android_interstitial_interval" name="ml_android_interstitial_interval">
									<?php for ( $a = 1; $a <= 10; $a ++ ): ?>
										<option
											value="<?php echo esc_attr( $a ); ?>" <?php echo Mobiloud::get_option( 'ml_android_interstitial_interval', 5 ) == $a ? 'selected="selected"' : ''; ?>>
											<?php echo esc_html( $a ); ?>
										</option>
										<?php endfor; ?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="ml_native_ads_wrap ml_admob_ads_wrap">
					<hr/>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">Article List Native Ad Unit ID</th>
								<td>
									<input type="text" id="ml_android_native_ad_unit_id" name="ml_android_native_ad_unit_id"
										value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_native_ad_unit_id' ) ); ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">Interval</th>
								<td class="ml_ad_interval">
									<p>Show native ads every<br/>
										articles in the article list.</p>
									<select id="ml_android_native_ad_interval" name="ml_android_native_ad_interval">
										<?php for ( $a = 1; $a <= 10; $a ++ ): ?>
											<option
												value="<?php echo esc_attr( $a ); ?>" <?php echo Mobiloud::get_option( 'ml_android_native_ad_interval', 5 ) == $a ? 'selected="selected"' : ''; ?>>
												<?php echo esc_html( $a ); ?>
											</option>
											<?php endfor; ?>
									</select>
								</td>
							</tr>
							<?php echo_admob_ad_size( 'ml_android_native_ad_type' ); ?>
						</tbody>
					</table>
				</div>
				<div class="ml_admob_ads_wrap">
					<hr/>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">Article Screen Native Ad Unit ID</th>
								<td>
									<input type="text" id="ml_android_native_ad_article_unit_id" name="ml_android_native_ad_article_unit_id"
										value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_native_ad_article_unit_id' ) ); ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">At position</th>
								<td class="ml_ad_interval">
									<select id="ml_android_native_ad_article_position" name="ml_android_native_ad_article_position">
										<?php foreach ( array( 'top' => 'Above article', 'bottom' => 'Below article', 'both' => 'Both above and below' ) as $k => $value) {
											?><option
												value="<?php echo esc_attr( $k ); ?>" <?php echo Mobiloud::get_option( 'ml_android_native_ad_article_position', 'both' ) == $k ? 'selected="selected"' : ''; ?>>
												<?php echo esc_html( $value ); ?>
											</option>
											<?php } ?>
									</select>
								</td>
							</tr>
							<?php echo_admob_ad_size( 'ml_android_native_ad_article_type' ); ?>
						</tbody>
					</table>
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


		<h3>Embed HTML ads within the content</h3>
		<div class='ml-col-twothirds'>
			<p>You can use the editor to add HTML or Javascript code in a number of ad positions within the post and
				page screens.</p>

			<div class="ml-editor-controls">
				<select id="ml_ad_banner_position_select" name="ml_ad_banner_position_select">
					<option value="">
						Select a position...
					</option>
					<?php foreach ( Mobiloud_Admin::$banner_positions as $position_key => $position_name ): ?>
						<option value='<?php echo esc_attr( $position_key ); ?>' ?>
							<?php echo esc_html( $position_name ); ?>
						</option>
						<?php endforeach; ?>
				</select>
				<a href="#" class='button-primary ml-save-banner-btn'>Save</a>
			</div>
			<textarea class='ml-editor-area ml-show'></textarea>
			<?php foreach ( Mobiloud_Admin::$banner_positions as $position_key => $position_name ): ?>
				<textarea class='ml-editor-area'
					name='<?php echo esc_attr( $position_key ); ?>'><?php echo stripslashes( htmlspecialchars( Mobiloud::get_option( $position_key, '' ) ) ); ?></textarea>
				<?php endforeach; ?>

			<h4>Preview the results</h4>
			<p>Select a post or page to preview the results of your edits.</p>
			<select id="preview_popup_post_select">
				<?php
				$posts_query         = array(
					'posts_per_page' => 10,
					'orderby'        => 'post_date',
					'order'          => 'DESC',
					'post_type'
				);
				$included_post_types = explode( ",", Mobiloud::get_option( 'ml_article_list_include_post_types', array() ) );
				foreach ( $included_post_types as $post_type ) {
					$posts_query['post_type'] = $post_type;
					$posts                    = get_posts( $posts_query );
					if ( count( $posts ) > 0 ) {
						?>
						<optgroup label="<?php echo ucfirst( $post_type ); ?>">
							<?php foreach ( $posts as $post ) { ?>

								<option
									value="<?php echo MOBILOUD_PLUGIN_URL; ?>post/post.php?post_id=<?php echo $post->ID; ?>">
									<?php if ( strlen( $post->post_title ) > 40 ) { ?>

										<?php echo substr( $post->post_title, 0, 40 ); ?>

										..
										<?php } else { ?>

										<?php echo $post->post_title; ?>

										<?php } ?>
								</option><?php } ?>
						</optgroup>
						<?php
					}
				}


				?>
				<?php $pages = get_pages( array( 'sort_order'  => 'ASC',
					'sort_column' => 'post_title',
					'post_type'   => 'page',
					'post_status' => 'publish'
				) ); ?>
				<optgroup label="Pages">
					<?php foreach ( $pages as $page ) { ?>

						<option value="<?php echo MOBILOUD_PLUGIN_URL; ?>post/post.php?post_id=<?php echo $page->ID; ?>">
							<?php if ( strlen( $page->post_title ) > 40 ) { ?>

								<?php echo substr( $page->post_title, 0, 40 ); ?>

								..
								<?php } else { ?>

								<?php echo $page->post_title; ?>

								<?php } ?>
						</option><?php } ?>
				</optgroup>
			</select>
			<a href='#' class='ml_open_preview_btn button-secondary ml-preview-phone-btn'>Preview on phone</a>
			<a href='#' class='ml_open_preview_btn button-secondary ml-preview-tablet-btn'>Preview on tablet</a>
		</div>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
			value="Save Changes"></p>
	</form>
</div>
<div id="preview_popup_content">
	<div class="iphone5s_device">
		<iframe id="preview_popup_iframe">
		</iframe>
	</div>
	<div class="ipadmini_device">
		<iframe id="preview_popup_iframe">
		</iframe>
	</div>
</div>