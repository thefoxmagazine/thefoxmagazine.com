<?php
/*
  Plugin Name: Videe.tv
  Plugin URI: https://videe.tv/
  Description: This plugin provides you an access to the free video library, adjustable player and high income from every video viewer!
  Version: 1.8
  Author: Videe.tv
  Author URI: http://videe.tv
 */

if (!function_exists('add_action')) {
	die('Unauthorized access.');
}


spl_autoload_register( 'videeAutoload' );

function videeAutoload($className) {
	
	$fileName = str_replace('_', '-', strtolower($className));
	$filePath = sprintf("%sclass.%s.php", plugin_dir_path(__FILE__),  $fileName);
	
	if ( is_file($filePath) ) {
		require_once $filePath;
	}
}

$config = array(
	'videeApiUrl' => 'https://api.videe.tv/',
	'wpConstructorUrl' => '//wp-constructor.videe.tv/index.html',
	'playerScriptSrc' => '//player.videe.tv/v2.1/player.js',
	'piwikApiUrl' => 'https://piwik.videe.tv',
	'piwikSiteId' => 1,
	'videeVersion' => '1.8',
	'pluginSlugName' => 'videetv-video-monetization',
	'videeMinimumWpVersion' => '3.9.6',
	'videeMaximumWpVersion' => '4.8',
	'minimumPhpVersion' => '5.2',
	'videePluginUrl' => plugin_dir_url(__FILE__),
	'videePluginDir' => plugin_dir_path(__FILE__),
	'videePluginBasename' => plugin_basename(__FILE__),
	'preAttribute' => 'data-videe-'
);

function initLocator($config) {
	
	$locator = new Service_Locator();
	$locator->setConfig($config);
	
	$locator->add('config', $config);
	$locator->add('errors', 'Videe_Errors', true);
	$locator->add('piwik', 'Videe_Piwik', true);
	$locator->add('user', 'Videe_User', true);
	$locator->add('admin', 'Videe_Admin', true);
	$locator->add('settings', 'Videe_Settings', true);
	$locator->add('content', 'Videe_Content', true);

	return $locator;
}

global $locator;

$locator = initLocator($config);

register_activation_hook(__FILE__, array($locator->admin, 'pluginActivation'));
register_deactivation_hook(__FILE__, array($locator->admin, 'pluginDeactivation'), 10);

function pluginUninstall() {
	global $locator;
	$locator->piwik->trackUninstall();
}

register_uninstall_hook(__FILE__, 'pluginUninstall');

add_filter('the_content', array($locator->content, 'contentMonitor'));
add_filter('widget_text', array($locator->content, 'contentMonitor'));

function registerWidget() {
	global $locator;

	Videe_Widget::setServiceLocator($locator);
	Videe_Widget::setConfig($locator->getConfig());
	register_widget('Videe_Widget');
}

add_action('widgets_init', 'registerWidget');

if (is_admin()) {
	add_action('admin_init', array($locator->settings, 'init'));
	add_action('admin_init', array($locator->user, 'init'));
	add_action('admin_init', array($locator->admin, 'activationRedirect'));
	add_action('init', array($locator->admin, 'init'));	
}
