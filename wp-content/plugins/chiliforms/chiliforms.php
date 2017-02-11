<?php
/*
	Plugin Name: ChiliForms
	Description: A better WordPress forms solution.
	Author: KonstruktStudio
	Text Domain: chiliforms
	Version: 0.5.1
*/

if ( version_compare(PHP_VERSION, '5.3', '<') ) {
	add_action( 'admin_notices', create_function( '',
		"echo '<div class=\"error\"><p>" .
		__('Installed plugin <strong>Chiliforms</strong> requires at least PHP 5.3, your system uses version: ', 'chiliforms') .
		PHP_VERSION . ". " . __('Please, upgrade PHP or deactivate plugin.', 'chiliforms') .
		"</p></div>';" ) );
	return;
}

define('KCF_VERSION', '0.5.1');
define('KCF_PROJECT_URL', 'https://www.chiliforms.com');
define('KCF_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('KCF_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once('autoloader.php');

function kcf_activate() {
	KCF_DbModel::create_schema();
}
register_activation_hook(__FILE__, 'kcf_activate');

new KCF_AppController();

require_once('modules/api/api.php');