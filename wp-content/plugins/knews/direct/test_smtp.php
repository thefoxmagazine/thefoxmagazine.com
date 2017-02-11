<?php
global $Knews_plugin;

if ($Knews_plugin) {


	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	$theHtml = '<p><strong>' . __('SMTP Submit e-mail test done','knews') . '</strong></p>';
	$theHtml .= '<p>' . __('If you can read this e-mail, SMTP is correctly configured and you can activate it in Knews preferences','knews').'</p>';
	
	$test_array = array(
					'SMTPDebug' => 1,
					'from_mail_knews' => trim($Knews_plugin->post_safe('from_mail_knews')),
					'from_name_knews' => trim($Knews_plugin->post_safe('from_name_knews')),
					'smtp_host_knews' => trim($Knews_plugin->post_safe('smtp_host_knews')),
					'smtp_port_knews' => trim($Knews_plugin->post_safe('smtp_port_knews')),
					'smtp_user_knews' => trim($Knews_plugin->post_safe('smtp_user_knews')),
					'smtp_pass_knews' => trim($Knews_plugin->post_safe('smtp_pass_knews')),
					'smtp_secure_knews' => $Knews_plugin->post_safe('smtp_secure_knews'),
					'is_sendmail' => $Knews_plugin->post_safe('is_sendmail_knews')
				);

	$bounce = $Knews_plugin->post_safe('bounce_header_knews');
	if ($bounce != '') $test_array['bounce_header_knews'] = $bounce;

	$enviament = $Knews_plugin->sendMail($Knews_plugin->post_safe('email_test'), 'Test Knews', $theHtml, '', $test_array);
	
	if ($enviament['ok'] == 1) {
		echo '<p>' . __('Shipping done, check that the mail has arrived.','knews') . '</p>';
	} else {
		echo '<p>' . __('Submit error.','knews') . '</p>';
	}
}
die();
?>