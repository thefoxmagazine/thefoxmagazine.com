<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

class KCF_AppController {

	private $admin_page_controller;
	private $forms_page_controller;
	private $entries_page_controller;
	private $options_page_controller;
	private $ajax_actions_controller;

	public function __construct() {

		KCF_Options::register();

		$this->admin_page_controller = new KCF_AdminPageController();
		$this->forms_page_controller = new KCF_FormsPageController();
		$this->entries_page_controller = new KCF_EntriesPageController();
		$this->options_page_controller = new KCF_OptionsPageController();

		global $kcf_fieldFactory;
		$kcf_fieldFactory = new KCF_FieldFactory();

		// dynamic preview page
		add_filter('the_posts', array($this,'dynamic_preview_page'));

		// AJAX actions
		$this->ajax_actions_controller = new KCF_AjaxActions();
		$this->ajax_actions_controller->register();

		add_action('wp_enqueue_scripts', array($this, 'client_enqueue'));
	}

	public function client_enqueue() {
		wp_enqueue_script( 'chiliforms_client', KCF_PLUGIN_URL . 'js/build/clientside/chiliforms.js', array(), KCF_VERSION, true );

		wp_localize_script( 'chiliforms_client', 'KCFData', array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce' => array(
					'nonce' => wp_create_nonce( KCF_AjaxActions::get_nonce() ),
					'nonceKey' => KCF_AjaxActions::get_nonce_key(),
				)
			)
		);

        /**
         * Styles
         */
        wp_register_style( 'chiliforms_css', KCF_PLUGIN_URL . 'assets/css/bundle/chiliforms.css', array(), KCF_VERSION );
        wp_enqueue_style( 'chiliforms_css' );
	}
	
	public function dynamic_preview_page($posts) {
		global $wp, $wp_query;

		$page_slug = 'chiliforms_preview';

		// no query params, return
		if (!$wp->query_vars) {
			return $posts;
		}

		// has real posts, return
		if (count( $posts ) !== 0) {
			return $posts;
		}

		// no privileges for preview page
		if (!current_user_can('administrator')) {
			return $posts;
		}

		// no form id parameter
		if (! isset($_GET['form_id'])) {
			return $posts;
		}

		// wrong preview page slug
		if (strtolower( $wp->request ) !== $page_slug &&
		        (!array_key_exists('page_id', $wp->query_vars) || $wp->query_vars['page_id'] !== $page_slug )) {

			return $posts;
		}

		$form_data = KCF_DbModel::get_form_by_id($_GET['form_id']);

		if (!$form_data) {
			return $posts;
		}

		ob_start();

		$form_renderer = new KCF_FormModel($form_data);

		$form_renderer->render();

		$form_content = ob_get_clean();

		$post                 = new stdClass;
		$post->post_author    = 1;
		$post->post_name      = $page_slug;
		$post->guid           = get_bloginfo( 'wpurl' . '/' . $page_slug );
		$post->post_title     = __( 'Chiliforms preview', 'chiliforms' );
		$post->post_content   = $form_content;
		$post->ID             = -42;
		$post->post_status    = 'static';
		$post->comment_status = 'closed';
		$post->ping_status    = 'closed';
		$post->comment_count  = 0;

		$post->post_date     = current_time( 'mysql' );
		$post->post_date_gmt = current_time( 'mysql', 1 );

		$post    = (object) array_merge( (array) $post, array(
			'slug'         => $page_slug,
			'post_title'   => __( 'Chiliforms preview', 'chiliforms' ),
			'post_content' => $form_content,
			'post_type' => 'page'
		) );
		$posts   = null;
		$posts[] = $post;

		$wp_query->is_page     = true;
		$wp_query->is_singular = true;
		$wp_query->is_home     = false;
		$wp_query->is_archive  = false;
		$wp_query->is_category = false;
		unset( $wp_query->query['error'] );
		$wp_query->query_vars['error'] = '';
		$wp_query->is_404              = false;

		remove_filter( 'the_content', 'wpautop' );

		return $posts;
	}
}
