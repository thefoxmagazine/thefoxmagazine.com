<div id="ml_settings_license" class="tabs-panel ml-compact">
	<form method="post" action="<?php echo admin_url( 'admin.php?page=mobiloud_settings&tab=license' ); ?>">
		<?php wp_nonce_field( 'form-settings_license' ); ?>
		<h3>Push Keys</h3>
		<div class='ml-col-twothirds'>
			<p>Once your app has been published, enter here the Push keys we have sent you.</p>

			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row">Push App ID</th>
					<td>
						<input size="36" type="text" id="ml_pb_app_id" name="ml_pb_app_id"
						       placeholder="Enter Push ID"
						       value='<?php echo Mobiloud::get_option( 'ml_pb_app_id' ); ?>'>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Secret Key</th>
					<td>
						<input size="36" type="text" id="ml_pb_secret_key" name="ml_pb_secret_key"
						       placeholder="Enter Secret Key"
						       value='<?php echo Mobiloud::get_option( 'ml_pb_secret_key' ); ?>'>
					</td>
				</tr>
				</tbody>
			</table>

			<p>Can't find your keys? <a class="contact" href="mailto:support@mobiloud.com">Request your keys</a> from our support
				team.</p>

		</div>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
		                         value="Save Changes"></p>
	</form>
</div>