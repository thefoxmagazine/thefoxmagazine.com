<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

class KCF_AdminPageController {

	public function __construct() {
		add_action('admin_menu', array($this, 'admin_menu'));
	}

	public function admin_menu() {
		add_menu_page(
			__('Settings', 'chiliforms'),
			__('ChiliForms', 'chiliforms'),
			'manage_options',
			'chiliforms-menu',
			array($this, 'admin_menu_html'),
			'dashicons-index-card' // TODO: replace with custom icon, 'none' + css styles or 'data:image/svg+xml;base64,'
		);
	}

	public function admin_menu_html() {
		// nothing here, content is printed in submenu callback
	}
}