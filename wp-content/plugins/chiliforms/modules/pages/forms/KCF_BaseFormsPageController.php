<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

abstract class KCF_BaseFormsPageController {

	const FORMS_SCREEN_BASE = 'toplevel_page_chiliforms-menu';

	public function __construct() {
		add_action('admin_menu', array($this, 'submenu_options'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));
	}

	public function submenu_options() {
		add_submenu_page(
			'chiliforms-menu',
			__('Forms', 'chiliforms'),
			__('Forms', 'chiliforms'),
			'manage_options',
			'chiliforms-menu', // this is to replace first menu item
			array($this, 'submenu_html')
		);
	}

	public function submenu_html() {
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'chiliforms'));
		}

		$this->page_html();
	}

	abstract public function page_html();

	/**
	 * Should be overridden
	 * @return array
	 */
	abstract protected function _get_bootstrap_data();

	public function admin_enqueue( $hook ) {
		$screen = get_current_screen();

		if ( $screen->base !== self::FORMS_SCREEN_BASE ) {
			return;
		}

		wp_enqueue_script( 'admin_forms_app_react', KCF_PLUGIN_URL . 'js/build/admin/react.bundle.js', array(), KCF_VERSION, true );
		wp_enqueue_script( 'admin_forms_app', KCF_PLUGIN_URL . 'js/build/admin/chiliforms.bundle.js', array(), KCF_VERSION, true );

		wp_localize_script( 'admin_forms_app', 'KCFData', array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'version' => KCF_VERSION,
				'siteUrl' => site_url(),
				'formData' => $this->_get_bootstrap_data(),
				'formTemplates' => KCF_TemplateModel::get_all_templates(),
				'translations' => KCF_i18n::get_translations(),
				'nonce' => array(
					'nonce' => wp_create_nonce( KCF_AjaxActions::get_nonce() ),
					'nonceKey' => KCF_AjaxActions::get_nonce_key(),
				)
			)
		);

		/**
		 * Styles
		 */
		wp_register_style( 'kcf_admin_css', KCF_PLUGIN_URL . 'assets/css/bundle/admin.css', array(), KCF_VERSION );
		wp_enqueue_style( 'kcf_admin_css' );

        wp_register_style( 'kcf_fa_css', KCF_PLUGIN_URL . 'assets/css/vendor/font-awesome.min.css' );
        wp_enqueue_style( 'kcf_fa_css' );

		wp_register_style( 'kcf_fe_styles_css', KCF_PLUGIN_URL . 'assets/css/bundle/chiliforms.css', array(), KCF_VERSION );
		wp_enqueue_style( 'kcf_fe_styles_css' );
	}
}