<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Forms
 * @subpackage Yikes_Inc_Easy_Mailchimp_Forms/admin
 * @author     YIKES, Inc. <plugins@yikesinc.com>
 */
class Yikes_Inc_Easy_Mailchimp_Forms_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $yikes_inc_easy_mailchimp_extender    The ID of this plugin.
	 */
	private $yikes_inc_easy_mailchimp_extender;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Our form interface instance.
	 *
	 * @var Yikes_Inc_Easy_MailChimp_Extender_Form_Interface
	 */
	private $form_interface;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param string                                           $yikes_inc_easy_mailchimp_extender The name of this plugin.
	 * @param string                                           $version                           The version of this plugin.
	 * @param Yikes_Inc_Easy_MailChimp_Extender_Form_Interface $form_interface
	 */
	public function __construct(
		$yikes_inc_easy_mailchimp_extender,
		$version,
		Yikes_Inc_Easy_MailChimp_Extender_Form_Interface $form_interface
	) {
		$this->yikes_inc_easy_mailchimp_extender = $yikes_inc_easy_mailchimp_extender;
		$this->version = $version;
		$this->form_interface = $form_interface;
	}

	/**
	 * Our admin hooks.
	 *
	 * @author Jeremy Pry
	 */
	public function hooks() {

		// check for old plugin options and migrate if exist
		add_action( 'admin_menu', array( $this, 'register_admin_pages' ), 11 );

		// check for old plugin options and migrate if exist
		add_action( 'admin_init', array( $this, 'check_for_old_yks_mc_options' ) );

		// Ajax function to update new options...
		add_action( 'wp_ajax_migrate_old_plugin_settings', array( $this, 'migrate_archived_options' ) );

		// Ajax function to migrate our forms
		add_action( 'wp_ajax_migrate_prevoious_forms', array( $this, 'migrate_previously_setup_forms' ) );

		// fix menu icon spacing
		add_action( 'admin_head', array( $this, 'fix_menu_icon_spacing' ) );

		// register our plugin settings
		add_action( 'admin_init', array( $this, 'yikes_easy_mc_settings_init' ) );

		// plugin redirect on activation
		add_action( 'admin_init', array( $this, 'yikes_easy_mc_activation_redirect' ) );

		// Include Third Party Extensions
		new YIKES_MailChimp_ThirdParty_Integrations();

		// Include our dashboard widget class
		new YIKES_Inc_Easy_MailChimp_Dashboard_Widgets();

		// Include our front end widget class
		add_action( 'widgets_init', array( $this, 'register_optin_widget' ) );

		// Include our ajax processing class
		new YIKES_Inc_Easy_MailChimp_Process_Ajax();

		// load up our helper class
		add_action( 'admin_init', array( $this, 'yikes_mailchimp_load_helper_class' ) );

		// process the subscriber count shortcode in form descriptions
		add_action( 'yikes-mailchimp-form-description', array( $this, 'process_subscriber_count_shortcode_in_form_descriptions' ), 10, 2 );

		/***********************/
		/** Create A Form **/
		/**********************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-create-form' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_create_form' ) );

		}

		/***********************/
		/** Delete A Form **/
		/**********************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-delete-form' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_delete_form' ) );

		}

		/**********************************/
		/** Duplicate/Clone A Form    **/
		/********************************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-duplicate-form' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_duplicate_form' ) );

		}

		/*************************************/
		/**  Reset Form Impression Stats **/
		/***********************************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-reset-stats' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_reset_impression_stats' ) );

		}

		/**********************************/
		/**         Update A Form        **/
		/********************************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-update-form' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_update_form' ) );

		}

		/**************************************************/
		/**     Clear Store MailChimp Transient Data   **/
		/*************************************************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-clear-transient-data' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_clear_transient_data' ) );

		}

		/*******************************************/
		/** Remove a user from a mailing list     **/
		/*****************************************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-unsubscribe-user' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_unsubscribe_user' ) );

		}

		/*******************************************/
		/**    Create misisng error log file  **/
		/*****************************************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-create-error-log' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_create_missing_error_log' ) );

		}

		/*******************************************/
		/**   TinyMCE Initialization Functions     **/
		/*****************************************/
		add_action( 'admin_head', array( $this, 'add_tinyMCE_buttons' ) );

		// pass our lists data to tinyMCE button for use
		foreach ( array( 'post.php', 'post-new.php' ) as $hook ) {

			add_action( 'admin_enqueue_scripts', array( $this, 'tinymce_yikes_easy_mc' ) );

		}

		// display an admin notice for users on PHP < 5.3
		if ( phpversion() < '5.3' ) {
			add_action( "admin_notices", array( $this, 'display_php_warning' ), 999 );
		}

		// two week , dismissable notification - check the users plugin installation date
		add_action( 'admin_init', array( $this, 'yikes_easy_mailchimp_check_installation_date' ) );

		// dismissable notice admin side
		add_action( 'admin_init', array( $this, 'yikes_easy_mailchimp_stop_bugging_me' ), 5 );

		/**************************************************/
		/**        Clear MailChimp Error Log Data        **/
		/*************************************************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-clear-error-log' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_clear_error_log' ) );

		}

		/*********************************************/
		/**        Export MailChimp Opt-in Forms   **/
		/*******************************************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-export-forms' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_export_forms' ) );

		}

		/*********************************************/
		/**                Export Plugin Settings           **/
		/*******************************************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-export-settings' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_export_plugin_settings' ) );

		}

		/*******************************************/
		/**        Import Class Inclusion       **/
		/*****************************************/
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yikes-easy-mc-import-forms' ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_import_forms' ) );

		}

		/*******************************************/
		/**    Premium Support Request     **/
		/*****************************************/
		if ( isset( $_POST['submit-premium-support-request'] ) ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_premium_support_request' ) );

		}

		/****************************************/
		/**    Dismiss Options Migrations        **/
		/****************************************/
		if ( isset( $_REQUEST['dismiss_migration_nonce'] ) ) {

			add_action( 'init', array( $this, 'yikes_easy_mailchimp_dismiss_option_migrate' ) );

		}

		/** Parse default value into usable dynamic data **/
		add_filter( 'yikes-mailchimp-process-default-tag', array( $this, 'parse_mailchimp_default_tag' ) );

		/** Add a disclaimer to ensure that we let people know we are not endorsed/backed by MailChimp at all **/
		add_filter( 'admin_footer_text', array( $this, 'yikes_easy_forms_admin_disclaimer' ) );

		/** Add custom plugin action links **/
		add_filter( 'plugin_action_links_yikes-inc-easy-mailchimp-extender/yikes-inc-easy-mailchimp-extender.php', array( $this, 'easy_forms_plugin_action_links' ) );

		/* Alter the color scheme based on the users selection */
		add_action( 'admin_print_scripts', array( $this, 'alter_yikes_easy_mc_color_scheme' ) );

		// hook in and display our knowledge base articles on the support page
		add_action( 'yikes-mailchimp-support-page', array( $this, 'hook_and_display_kb_article_RSS' ) );

		// ensure that the upgrade went smoothly, else we have to let the user know we need to upgrade the database
		// after upgrading f rom 6.0.3.7 users need to upgrade the database as well
		add_action( 'plugins_loaded', array( $this, 'check_yikes_mc_table_version' ) );

		// Run a check to see if we need to convert the custom DB table to options.
		add_action( 'plugins_loaded', array( $this, 'check_db_version' ) );

	}

		/*
		*	Add custom action links on plugins.php
		*	@ param 	array	$links 	Pre-existing plugin action links
		*	@ return	array	$links		New array of plugin actions
		*/
		public function easy_forms_plugin_action_links( $links ) {
			$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=yikes-inc-easy-mailchimp-settings') ) .'">' . __( 'Settings', 'yikes-inc-easy-mailchimp-extender' ) . '</a>';
			$links[] = '<a href="' . esc_url( 'http://www.yikesplugins.com?utm_source=plugins-page&utm_medium=plugin-row&utm_campaign=admin' ) . '" target="_blank">' . __( 'More plugins by YIKES, Inc.' , 'yikes-inc-easy-mailchimp-extender' ) . '</a>';
			return $links;
		}

		/**
		 *	Add a disclaimer to the admin footer for all YIKES pages to ensure that users understand there is no correlation between this plugin and MailChimp.
		 *	This plugin simply provides the service of linking MailChimp with your site.
		 *
		 * @since        6.0
		 *
		 * @param       string   $footer_text The existing footer text
		 *
		 * @return      string
		 */
		public function yikes_easy_forms_admin_disclaimer( $footer_text ) {
			$page = get_current_screen();
			$base = $page->base;
			if ( strpos( $base, 'yikes-' ) !== false ) {
				$disclaimer_text = sprintf( '<em>' . __( 'Disclaimer: <strong>Easy Forms for MailChimp</strong> is in no way endorsed, affiliated or backed by MailChimp, or its parent company Rocket Science Group.', 'yikes-inc-easy-mailchimp-extender' ), '<a href="https://wordpress.org/support/view/plugin-reviews/give?filter=5#postform" target="_blank" class="give-rating-link" data-rated="' . __( 'Thanks :)', 'yikes-inc-easy-mailchimp-extender' ) . '">', '</a></em>' );
				return $disclaimer_text;
			} else {
				return $footer_text;
			}
		}

		/*
		*	Parse our default tag into dynamic data
		*	to be passed to MailChimp
		*
		*	@since 6.0.0
		*	@return	parsed tag content
		*/
		public function parse_mailchimp_default_tag( $default_tag ) {
			if( ! $default_tag || $default_tag == '' ) {
				return $default_tag;
			}
			global $post;
			// page title
			if( $default_tag == '{page_title}' ) {
				$default_tag = get_the_title( $post->ID );
			}
			// page id
			if( $default_tag == '{page_id}' ) {
				$default_tag = $post->ID;
			}
			// page url
			if( $default_tag == '{page_url}' ) {
				$default_tag = get_permalink( $post->ID );
			}
			// blog name
			if( $default_tag == '{blog_name}' ) {
				$default_tag = get_bloginfo( 'name' );
			}
			// is user logged in
			if( $default_tag == '{user_logged_in}' ) {
				if( is_user_logged_in() ) {
					$default_tag = 'Registered User';
				} else {
					$default_tag = 'Guest User';
				}
			}
			/* Return our filtered tag */
			return apply_filters( 'yikes-mailchimp-parse-custom-default-value', $default_tag );
		}

		/*
		*	Delete the contents of our error log
		*
		*	When a user clicks 'Clear Log' on the debug settings page, this funciton
		*	is used to clear the data out of our php file.
		*/
		public function yikes_easy_mailchimp_clear_error_log() {
			// file put contents $returned error + other data
			if( file_exists( YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php' ) ) {
				$clear_log = file_put_contents(
					YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php',
					''
				);
				if( $clear_log === false ) {
					// redirect the user to the manage forms page, display error message
					wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings&error-log-cleared=false' ) ) );
				} else {
					// redirect the user to the manage forms page, display confirmation
					wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings&error-log-cleared=true' ) ) );
				}
			}
		}

		/*
		*	Custom export function to export all or specific forms
		*	to allow for easy transpot to other sites
		*	@since 		6.0.0
		*	@return 	CSV export file
		*/
		public function yikes_easy_mailchimp_export_forms() {
			// grab our nonce
			$nonce = $_REQUEST['nonce'];
			// grab the forms
			$forms = isset( $_REQUEST['export_forms'] ) ? $_REQUEST['export_forms'] : array();
			// validate nonce
			if( ! wp_verify_nonce( $nonce, 'export-forms' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}

			// run the export function
			// parameters: ( $table_name, $form_ids, $file_name )
			Yikes_Inc_Easy_MailChimp_Export_Class::yikes_mailchimp_form_export('Yikes-Inc-Easy-MailChimp-Forms-Export', $forms );
			// re-direct the user back to the page
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=import-export-forms' ) ) );
			die();
		}

		/*
		*	Custom export function to export YIKES Easy Forms for MailChimp Plugin Settings
		*	to allow for easy transpot to other sites
		*	@since 		6.0.0
		*	@return 	CSV export file
		*/
		public function yikes_easy_mailchimp_export_plugin_settings() {
			// grab our nonce
			$nonce = $_REQUEST['nonce'];
			// validate nonce
			if( ! wp_verify_nonce( $nonce, 'export-settings' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}

			// run the export function
			// parameters: ( $table_name, $form_ids, $file_name )
			Yikes_Inc_Easy_MailChimp_Export_Class::yikes_mailchimp_settings_export( 'Yikes-Inc-Easy-MailChimp-Settings-Export' );
			// re-direct the user back to the page
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=import-export-forms' ) ) );
			die();
		}

		/*
		*	Custom import function to import all or specific forms
		*	@since 6.0.0
		*/
		public function yikes_easy_mailchimp_import_forms() {
			// grab our nonce
			$nonce = $_REQUEST['nonce'];
			// validate nonce
			if( ! wp_verify_nonce( $nonce, 'import-forms' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			// include the export class
			if( ! class_exists( 'Yikes_Inc_Easy_MailChimp_Import_Class' ) ) {
				include_once( YIKES_MC_PATH . 'includes/import-export/yikes-easy-mailchimp-import.class.php' );
			}
			// run the import function
			// parameters: ( $_FILES )
			Yikes_Inc_Easy_MailChimp_Import_Class::yikes_mailchimp_import_forms( $_FILES );
			$import_query_arg = Yikes_Inc_Easy_MailChimp_Import_Class::yikes_mailchimp_import_type( $_FILES );
			// re-direct the user back to the page
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=import-export-forms&' . $import_query_arg . '=true' ) ) );
			die();
		}

		/*
		*	Premium Support Request
		*	@since 6.0.0
		*/
		public function yikes_easy_mailchimp_premium_support_request() {

			if( isset( $_POST['action'] ) && $_POST['action'] != 'yikes-support-request' ) {
				return __( 'We encountered an error. Please contact the YIKES Inc. support team.' , 'yikes-inc-easy-mailchimp-extender' );
			}

			$license = $_POST['license_key'];
			$user_email = $_POST['user-email'];
			$support_topic = $_POST['support-topic'];
			$support_priority = $_POST['support-priority'];
			$support_content = $_POST['support-content'];

			// wp_die( print_r( $support_content) );

			$ticket_array = array(
				'action' => 'yikes-support-request',
				'license_key' => urlencode( base64_encode( $license ) ),
				'user_email' => urlencode( $user_email ),
				'site_url' => urlencode( esc_url( home_url() ) ),
				'support_topic' => urlencode( $support_topic ),
				'support_priority' => $support_priority,
				'support_content' => $support_content,
			);

			$yikes_plugin_support_url = 'https://yikesplugins.com';

			// Call the custom API.
			$response = wp_remote_post( esc_url( $yikes_plugin_support_url ), array(
				'timeout'   => 30,
				'sslverify' => false,
				'body'      => $ticket_array
			) );

			// catch the error
			if( is_wp_error( $response ) ) {
				wp_die( $response->getMessage() );
				return;
			}

			// retrieve our body
			$create_ticket_response = wp_remote_retrieve_body( $response );

			// display it
			if( $create_ticket_response )
				echo $create_ticket_response;

		}

		/**
		*	Dismiss the migrate options notice (incase the user wants to do things manually)
		*
		*	@since 6.0.0
		**/
		public function yikes_easy_mailchimp_dismiss_option_migrate() {
			// delete the options and allow the user to manually updadte things

			// Verify the NONCE is valid
			check_admin_referer( 'yikes-mc-dismiss-migration' , 'dismiss_migration_nonce' );

			// re-direct the user back to the page
			wp_redirect( esc_url_raw( admin_url( 'index.php?yikes-mc-options-migration-dismissed="true"' ) ) );
			die();
		}

		/**
		 * Error logging class
		 *
		 * This is our main error logging class file, used to log errors to the error log.
		 *
		 * @since 6.0.0
		 */
		public function load_error_logging_class() {
			if( get_option( 'yikes-mailchimp-debug-status' , '' ) == '1' ) {
				// if error logging is enabled we should include our error logging class
				/* Generate oure error logging table */
				require_once YIKES_MC_PATH . '/includes/error_log/class-yikes-inc-easy-mailchimp-error-logging.php';
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			}
		}

		/*
			yikes_easy_mailchimp_check_installation_date()
			checks the user installation date, and adds our action
			- if it's past 2 weeks we ask the user for a review :)
			@since v6.0.0
		*/
		public function yikes_easy_mailchimp_check_installation_date() {

			// add a new option to store the plugin activation date/time
			// @since v6.0.0
			// this is used to notify the user that they should review after 2 weeks
			if ( !get_option( 'yikes_easy_mailchimp_activation_date' ) ) {
				add_option( 'yikes_easy_mailchimp_activation_date', strtotime( "now" ) );
			}

			$stop_bugging_me = get_option( 'yikes_easy_mailchimp_review_stop_bugging_me' );

			if( !$stop_bugging_me ) {
				$install_date = get_option( 'yikes_easy_mailchimp_activation_date' );
				$past_date = strtotime( '-14 days' );
				if ( $past_date >= $install_date && current_user_can( 'install_plugins' ) ) {
					add_action( 'admin_notices', array( $this , 'yikes_easy_mailchimp_display_review_us_notice' ) );
				}
			}

		}

		/*
			Display our admin notification
			asking for a review, and for user feedback
			@since v6.0.0
		*/
		public function yikes_easy_mailchimp_display_review_us_notice() {
			/* Lets only display our admin notice on YT4WP pages to not annoy the hell out of people :) */
			if ( in_array( get_current_screen()->base , array( 'dashboard' , 'post' , 'edit' ) ) || strpos( get_current_screen()->base ,'yikes-inc-easy-mailchimp') !== false ) {

				$plugin_name = '<strong>Easy Forms for MailChimp</strong>';
				// Review URL - Change to the URL of your plugin on WordPress.org
				$reviewurl = 'https://wordpress.org/support/view/plugin-reviews/yikes-inc-easy-mailchimp-extender';
				$addons_url = esc_url( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-addons' ) );
				$nobugurl = esc_url_raw( add_query_arg( 'yikes_easy_mc_icons_nobug', '1', admin_url() ) );

				// Make sure all of our variables have values
				$reviewurl  = ( ! empty( $reviewurl ) ) ? $reviewurl : '';
				$addons_url = ( ! empty( $addons_url ) ) ? $addons_url : '';
				$nobugurl	= ( ! empty( $nobugurl ) ) ? $nobugurl : '';

				$review_message = '<div id="yikes-mailchimp-logo"></div>';
				$review_message .= sprintf( 
					__( 'It looks like you\'ve been using %1$s for 2 weeks now. We hope you\'re enjoying the features included with the free version. If so, please consider leaving us a review. Reviews only help to catch other users attention as well as provide us with feedback to grow and improve upon. If you\'re really enjoying the plugin, consider buying an add-on or developer license for some really awesome features and premium support.' , 'yikes-inc-easy-mailchimp-extender' ) 
					. '<span class="button-container"> <a href="%2$s" target="_blank" class="button-secondary"><span class="dashicons dashicons-star-filled"></span>'
						. __( "Leave A Review" , 'yikes-inc-easy-mailchimp-extender' ) 
					. '</a> <a href="%3$s" class="button-secondary"><span class="dashicons dashicons-upload"></span>'
						. __( "View Addons" , 'yikes-inc-easy-mailchimp-extender' ) 
					. '</a> <a href="%4$s" class="button-secondary"><span class="dashicons dashicons-no-alt"></span>'
						. __( "Dismiss" , 'yikes-inc-easy-mailchimp-extender' ) 
					. "</a> </span>", 
					$plugin_name, $reviewurl, $addons_url, $nobugurl );
				?>
					<div id="review-yikes-easy-mailchimp-notice">
						<?php echo $review_message; ?>
					</div>
				<?php
			}
		}

		/*
			yikes_easy_mailchimp_stop_bugging_me()
			Remove the Review us notification when user clicks 'Dismiss'
			@since v3.1.1
		*/
		public function yikes_easy_mailchimp_stop_bugging_me() {
			$nobug = "";
			if ( isset( $_GET['yikes_easy_mc_icons_nobug'] ) ) {
				$nobug = (int) esc_attr( $_GET['yikes_easy_mc_icons_nobug'] );
			}
			if ( 1 == $nobug ) {
				add_option( 'yikes_easy_mailchimp_review_stop_bugging_me', TRUE );
			}
		}

	/* End Two Week Notificaition */

		/* Display a warning users who are using PHP < 5.3 */
		public function display_php_warning() {
			$message = __( 'Easy Forms for MailChimp requires a minimum of PHP 5.3. The plugin will not function properly until you update. Please reach out to your host provider for assistance.' , 'yikes-inc-easy-mailchimp-extender' );
			echo "<div class='error'> <p><span class='dashicons dashicons-no-alt' style='color:rgb(231, 98, 98)'></span> $message</p></div>";
		}



	/* TinyMCE Functions */
		// load our button and pass in the JS form data variable
		public function add_tinyMCE_buttons() {
			global $typenow;
			// only on Post Type: post and page
			if( ! in_array( $typenow, array( 'post', 'page' ) ) ) {
				return;
			}
			add_filter( 'mce_buttons', array( $this, 'yks_mc_add_tinymce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'yks_mc_add_tinymce_plugin' ) );
		}

		// Add the button key for address via JS
		public function yks_mc_add_tinymce_button( $buttons ) {
			array_push( $buttons, 'yks_mc_tinymce_button_key' );
			// Print all buttons
			return $buttons;
		}

		// inlcude the js for tinymce
		public function yks_mc_add_tinymce_plugin( $plugin_array ) {

			$plugin_array['yks_mc_tinymce_button'] = plugins_url( '/js/min/yikes-inc-easy-mailchimp-tinymce-button.min.js', __FILE__ );

			return $plugin_array;

		}

		/**
		* Localize Script
		* Pass our imported list data, to the JS file
		* to build the drop down list in the modal
		*/
		public function tinymce_yikes_easy_mc() {
			// check capabilities
			if( ! current_user_can( apply_filters( 'yikes-mailchimp-user-role-access' , 'manage_options' ) ) ) {
				return;
			}

			$list_data = $this->form_interface->get_all_forms();
			$lists = array();
			if( !empty( $list_data ) ) {
				// build an array to pass to our javascript
				foreach( $list_data as $id => $form ) {
					$lists[] = array(
						'text'  => urlencode( $form['form_name'] ),
						'value' => $id,
					);
				}
			} else {
				$lists[0] = array(
					'text' => __( 'Please Import Some MailChimp Lists' , 'yikes-inc-easy-mailchimp-extender' ),
					'value' => '-'
				);
			}

			/* Pass our form data to our JS file for use */
			wp_localize_script( 'editor', 'localized_data', array(
				'forms' => json_encode( $lists ),
				'button_title' => __( 'Easy Forms for MailChimp', 'yikes-inc-easy-mailchimp-extender' ),
				'popup_title' => __( 'Easy Forms for MailChimp', 'yikes-inc-easy-mailchimp-extender' ),
				'list_id_label' => __( 'MailChimp Opt-In Form' , 'yikes-inc-easy-mailchimp-extender' ),
				'show_title_label' => __( 'Display Form Title' , 'yikes-inc-easy-mailchimp-extender' ),
				'show_description_label' => __( 'Display Form Description' , 'yikes-inc-easy-mailchimp-extender' ),
				'submit_button_text_label' => __( 'Custom Submit Button Text' , 'yikes-inc-easy-mailchimp-extender' ),
				'submit_button_message' => '<em>' . __( 'If left empty, the button will use the default submit button text .', 'yikes-inc-easy-mailchimp-extender' ) . '</em>',
				'alert_translated' => sprintf( __( 'You need to <a href=%s title="%s">create a form</a> before you can add one to a page or post.', 'yikes-inc-easy-mailchimp-extender' ), esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ), __( 'Create a form', 'yikes-inc-easy-mailchimp-extender' ) ),
			) );

		}
	/* End TinyMCE Functions */

	/*
	*	Redirect the user to our Welcome page
	*	when they activate the plug in, if they haven't been redirected before
	*/
	public function yikes_easy_mc_activation_redirect() {
		if ( get_option( 'yikes_mailchimp_activation_redirect', 'true' ) == 'true' ) {
			update_option( 'yikes_mailchimp_activation_redirect', 'false' );
			/* If the user had this plugin activated prior to today, redirect to 'Whats New' */
			if( get_option( 'yikes_easy_mailchimp_activation_date', strtotime( 'now' ) ) == strtotime( 'now' ) ) {
				wp_redirect( esc_url( admin_url( 'admin.php?page=yikes-mailchimp-welcome' ) ) );
			} else {
				/* Else redirect the user over to the 'Getting Started' tab */
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-welcome&section=whats-new' ) ) );
			}
			exit();
		}
	}

	/*
	*  Fix the MailChimp icon spacing in the admin menu
	*/
	public function fix_menu_icon_spacing() {
		?>
			<style>
			a[href="admin.php?page=yikes-inc-easy-mailchimp"] .wp-menu-image img {
				padding-top: 5px !important;
			}
			</style>
		<?php
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    6.0.0
	 */
	public function enqueue_styles() {
		/**
		 *	Enqueue our global dashboard styles
		 */
		wp_enqueue_style( 'yikes-inc-easy-mailchimp-extender-admin', plugin_dir_url( __FILE__ ) . 'css/yikes-inc-easy-mailchimp-extender-admin.min.css', array(), $this->version, 'all' );
		/*
		*	Enqueue Add-ons styles
		*/
		if ( get_current_screen()->base == 'easy-forms_page_yikes-inc-easy-mailchimp-addons' ) {
			wp_enqueue_style( 'yikes-inc-easy-mailchimp-extender-addons-styles', plugin_dir_url( __FILE__ ) . 'css/yikes-inc-easy-mailchimp-extender-addons.min.css', array(), $this->version, 'all' );
		}
		/*
		*	Enqueue Subscriber Profile Flags
		*/
		if ( get_current_screen()->base == 'admin_page_yikes-mailchimp-view-user' ) {
			wp_enqueue_style( 'yikes-inc-easy-mailchimp-extender-subscriber-flags', plugin_dir_url( __FILE__ ) . 'css/flag-icon.min.css', array(), $this->version, 'all' );
		}
	}
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    6.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'yikes-inc-easy-mailchimp-extender-admin-js', plugin_dir_url( __FILE__ ) . 'js/min/yikes-inc-easy-mailchimp-extender-admin.min.js', array( 'jquery' , 'jquery-ui-sortable' ), $this->version, false );

		$localized_data = array(
			'admin_url'                => esc_url_raw( admin_url() ),
			'ajax_url'                 => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'locating_interest_groups' => __( 'Locating Interest Groups', 'yikes-inc-easy-mailchimp-extender' ),
			'search_preloader_url'     => YIKES_MC_URL . 'includes/images/search-interest-group-preloader.gif',
			'preloader_url'            => esc_url_raw( admin_url( '/images/wpspin_light.gif' ) ),
		);

		wp_localize_script( 'yikes-inc-easy-mailchimp-extender-admin-js' , 'object_data' , $localized_data );

		// Enqueue required scripts for the form editor
		$screen = get_current_screen();
		if ( ! isset( $screen->base ) || 'admin_page_yikes-mailchimp-edit-form' !== $screen->base ) {
			return;
		}

		/** @var WP_Locale */
		global $wp_locale;

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery.timepicker.js',YIKES_MC_URL . 'admin/js/jquery.timepicker.min.js' , array( 'jquery' ) , $this->version, false );
		wp_enqueue_script( 'edit-form-js' , YIKES_MC_URL . 'admin/js/min/yikes-inc-easy-mailchimp-extender-edit-form.min.js' , array( 'jquery.timepicker.js', 'jquery-ui-datepicker' ) , $this->version, false );

		$localized_data = array(
			'ajax_url'                          => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'no_fields_assigned'                => __( 'No fields assigned to this form. Select some fields to add to this form from the right hand column.', 'yikes-inc-easy-mailchimp-extender' ),
			'bulk_delete_alert'                 => __( 'Are you sure you want to delete all of the fields assigned to this form?', 'yikes-inc-easy-mailchimp-extender' ),
			'closeText'                         => __( 'Done', 'yikes-inc-easy-mailchimp-extender' ),
			'currentText'                       => __( 'Today', 'yikes-inc-easy-mailchimp-extender' ),
			'monthNames'                        => array_values( $wp_locale->month ),
			'monthNamesShort'                   => array_values( $wp_locale->month_abbrev ),
			'monthStatus'                       => __( 'Show a different month', 'yikes-inc-easy-mailchimp-extender' ),
			'dayNames'                          => array_values( $wp_locale->weekday ),
			'dayNamesShort'                     => array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin'                       => array_values( $wp_locale->weekday_initial ),

			// set the date format to match the WP general date settings
			'dateFormat'                        => $this->yikes_jQuery_datepicker_date_format_php_to_js( get_option( 'date_format' ), 'date' ),

			// get the start of week from WP general setting
			'firstDay'                          => get_option( 'start_of_week' ),

			// is Right to left language? default is false
			'isRTL'                             => $wp_locale->is_rtl(),
			'start_date_exceeds_end_date_error' => __( 'Error: The start date and time cannot occur after the end date and time. Chosen date reverted to previous selection.', 'yikes-inc-easy-mailchimp-extender' ),

			// Editing field label fields
			'edit_field_label_pencil_title' => __( 'Click to edit the label', 'yikes-inc-easy-mailchimp-extender' ),
			'edit_field_label_cancel_title' => __( 'Click to cancel editing. Your changes will not be saved.', 'yikes-inc-easy-mailchimp-extender' ),
			'save_field_label_nonce' => wp_create_nonce( 'save_field_label_nonce' ),
		);
		wp_localize_script( 'edit-form-js' , 'yikes_mailchimp_edit_form' , $localized_data );
	}

	/**
	 * Convert the php date format string to a js date format
	 */
	public function yikes_jQuery_datepicker_date_format_php_to_js( $sFormat, $type ) {
		switch ( $type ) {
			default:
			case 'date':
				// Standard Date Fields
				switch ( $sFormat ) {
					//Predefined WP date formats
					case 'F j, Y':
					case 'j F Y':
					case 'm/d/Y':
					case 'mm/dd/yyyy':
					case 'MM/DD/YYYY':
					default:
						return( 'mm/dd/yy' );
						break;
					case 'Y/m/d':
					case 'Y-m-d':
						return( 'yy/mm/dd' );
						break;
					case 'd/m/Y':
					case 'dd/mm/yyyy':
					case 'DD/MM/YYYY':
						return( 'dd/mm/yyyy' );
						break;
				 }
				break;
			// Birthday Fields
			case 'birthday':
				switch ( $sFormat ) {
					//Predefined WP date formats
					case 'F j, Y':
					case 'j F Y':
					case 'm/d/Y':
					case 'mm/dd/yyyy':
					case 'MM/DD/YYYY':
					default:
						return( 'mm/dd' );
						break;
					case 'Y/m/d':
					case 'Y-m-d':
						return( 'mm/dd' );
						break;
					case 'd/m/Y':
					case 'dd/mm/yyyy':
					case 'DD/MM/YYYY':
					case 'dd/mm':
					case 'DD/MM':
						return( 'dd/mm' );
						break;
				 }
				break;
		}
	}

	/**
	 * Convert the php date format string to a js date format
	 */
	public function yikes_jQuery_datepicker_date_format( $site_option ) {
		switch( $site_option ) {
			//Predefined WP date formats
			default:
			case 'F j, Y':
			case 'm/d/Y':
				return( 'm/d/Y' );
				break;
			case 'Y-m-d':
				return( 'Y/m/d' );
				break;
			case 'd/m/Y':
				return( 'd/m/Y' );
				break;
		 }
	}

	/**
	*	Register our admin pages
	*	used to display data back to the user
	**/
	public function register_admin_pages() {

		/* Top Level Menu 'Easy MailChimp' */
		add_menu_page(
			__( 'Easy Forms', 'yikes-inc-easy-mailchimp-extender' ),
			'Easy Forms',
			apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
			'yikes-inc-easy-mailchimp',
			'', // no callback,
			YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_wink_icon.png'
		);

		// Sub Pages
		/*************/

		/* Easy MailChimp Settings */

		/* Easy MailChimp Manage Forms */
		add_submenu_page(
			'yikes-inc-easy-mailchimp',
			__( 'Opt-in Forms', 'yikes-inc-easy-mailchimp-extender' ),
			__( 'Opt-in Forms', 'yikes-inc-easy-mailchimp-extender' ),
			apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
			'yikes-inc-easy-mailchimp',
			array( $this, 'generateManageFormsPage' )
		);

		/* Easy MailChimp Manage Lists */
		add_submenu_page(
			'yikes-inc-easy-mailchimp',
			__( 'Mailing Lists', 'yikes-inc-easy-mailchimp-extender' ),
			__( 'Mailing Lists', 'yikes-inc-easy-mailchimp-extender' ),
			apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
			'yikes-inc-easy-mailchimp-lists',
			array( $this, 'generateManageListsPage' )
		);


		/*
		*	Custom action hook to hook into to add additional
		*	menu items from extensions
		*/
		do_action( 'yikes-mailchimp-menu' );


		/* Easy MailChimp Account Overview */
		if ( get_option( 'yikes-mc-api-validation' ) == 'valid_api_key' ) {
			/* Easy MailChimp Settings */
			add_submenu_page(
				'yikes-inc-easy-mailchimp',
				__( 'Account', 'yikes-inc-easy-mailchimp-extender' ),
				__( 'Account', 'yikes-inc-easy-mailchimp-extender' ),
				apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
				'yikes-inc-easy-mailchimp-account-overview',
				array( $this, 'generateAccountDetailsPage' )
			);
		}


		/* Easy MailChimp Settings */
		add_submenu_page(
			'yikes-inc-easy-mailchimp',
			__( 'Settings.', 'yikes-inc-easy-mailchimp-extender' ),
			__( 'Settings', 'yikes-inc-easy-mailchimp-extender' ),
			apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
			'yikes-inc-easy-mailchimp-settings',
			array( $this, 'generatePageOptions' )
		);

		/* Support Page */
		add_submenu_page(
			'yikes-inc-easy-mailchimp',
			__( 'Support', 'yikes-inc-easy-mailchimp-extender' ),
			__( 'Support', 'yikes-inc-easy-mailchimp-extender' ),
			apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
			'yikes-inc-easy-mailchimp-support',
			array( $this, 'generateSupportPage' )
		);

		/* Add-Ons Page */
		add_submenu_page(
			'yikes-inc-easy-mailchimp',
			__( 'Add-Ons', 'yikes-inc-easy-mailchimp-extender' ),
			__( 'Add-Ons', 'yikes-inc-easy-mailchimp-extender' ),
			apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
			'yikes-inc-easy-mailchimp-addons',
			array( $this, 'generateAddOnsPage' )
		);

		/** Hidden Pages **/

		/* Add Hidden Edit Form Page */
		add_submenu_page(
			'options.php',
			__( 'Edit Form', 'yikes-inc-easy-mailchimp-extender' ),
			__( 'Edit Form', 'yikes-inc-easy-mailchimp-extender' ),
			apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
			'yikes-mailchimp-edit-form',
			array( $this, 'generateEditFormPage' )
		);

		/* Add Hidden Migrate Options Page */
		add_submenu_page(
			'options-writing.php',
			__( 'Us Easy Forms for MailChimp Upgrade Options Structure', 'yikes-inc-easy-mailchimp-extender' ),
			'Us Easy Forms for MailChimp Upgrade Options Structure',
			'manage_options',
			'yikes-inc-easy-mailchimp-update',
			array( $this, 'migrate_old_yks_mc_options' )
		);

		/* Add Hidden Welcome Page */
		add_submenu_page(
			'options.php',
			__( 'Welcome', 'yikes-inc-easy-mailchimp-extender' ),
			__( 'Welcome', 'yikes-inc-easy-mailchimp-extender' ),
			apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
			'yikes-mailchimp-welcome',
			array( $this, 'generateWelcomePage' )
		);

		/* Add Hidden 'View List' Page */
		add_submenu_page(
			'options.php',
			__( 'View List', 'yikes-inc-easy-mailchimp-extender' ),
			__( 'View List', 'yikes-inc-easy-mailchimp-extender' ),
			apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
			'yikes-mailchimp-view-list',
			array( $this, 'generateViewListPage' )
		);

		/* Add Hidden View User Page */
		add_submenu_page(
			'options.php',
			__( 'View User', 'yikes-inc-easy-mailchimp-extender' ),
			__( 'View User', 'yikes-inc-easy-mailchimp-extender' ),
			apply_filters( 'yikes-mailchimp-user-role-access', 'manage_options' ),
			'yikes-mailchimp-view-user',
			array( $this, 'generateViewUserPage' )
		);

	}

	/*
	*	Redirect a user to an external page
	*	when they click 'Go Pro' in the admin menu
	*	to do: populate with sales URL
	*/
	public function generateAddOnsPage() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/add-ons.php'; // include our add-ons page
	}

	/**
	* Generate Us Easy MailChimp Manage Forms Page
	*
	* @since    1.0.0
	*/
	function generateManageFormsPage() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/manage-forms.php'; // include our manage forms page
	}

	/**
	* Generate Us Easy MailChimp Manage Lists Page
	*
	* @since    1.0.0
	*/
	function generateManageListsPage() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/manage-lists.php'; // include our lists page
	}

	/**
	* Generate Us Easy MailChimp Account Details Page
	*
	* @since    1.0.0
	*/
	function generateAccountDetailsPage() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/account-details.php'; // include our account details page
	}

	/**
	* Generate Us Easy MailChimp Support Page
	*
	* @since    1.0.0
	*/
	function generateSupportPage() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/support.php'; // include our options page
	}

	/**
	* Generate Us Easy MailChimp Edit Form Page
	*
	* @since    1.0.0
	*/
	function generateEditFormPage() {
		require_once YIKES_MC_PATH . 'admin/partials/edit-form.php'; // include our options page
	}

	/**
	* Generate Us Easy MailChimp Welcome Page
	*
	* @since    1.0.0
	*/
	function generateWelcomePage() {
		require_once YIKES_MC_PATH . 'admin/partials/welcome-page/welcome.php'; // include our options page
	}

	/**
	* Generate Us Easy MailChimp View List Page
	*
	* @since    1.0.0
	*/
	function generateViewListPage() {
		require_once YIKES_MC_PATH . 'admin/partials/view-list.php'; // include our options page
	}

	/**
	* Generate Us Easy MailChimp View User Page
	*
	* @since    1.0.0
	*/
	function generateViewUserPage() {
		require_once YIKES_MC_PATH . 'admin/partials/view-user.php'; // include our options page
	}

	/**
	*	Register our plugin settings, and display them on our settings page
	*
	* @since v.5.4
	**/
	function yikes_easy_mc_settings_init() {

		/* Register General Settings Section */

		register_setting( 'yikes_inc_easy_mc_general_settings_page', 'yikes-mc-api-key', array( $this , 'yikes_mc_validate_api_key' ) );

		add_settings_section(
			'yikes_easy_mc_settings_general_section_callback',
			'',
			'',
			'yikes_inc_easy_mc_general_settings_page'
		);

		/* Register Visual Representation of Connection */
		add_settings_field(
			'connection',
			__( 'API Connection', 'yikes-inc-easy-mailchimp-extender' ),
			'yikes_inc_easy_mc_visual_representation_of_connection_callback', // callback + validation inside of admin/partials/menu/options.php
			'yikes_inc_easy_mc_general_settings_page',
			'yikes_easy_mc_settings_general_section_callback'
		);

		/* Register Check Box Setting */
		add_settings_field(
			'yikes-mc-api-key',
			__( 'MailChimp API Key', 'yikes-inc-easy-mailchimp-extender' ),
			'yikes_inc_easy_mc_api_key_field_callback', // callback + validation inside of admin/partials/menu/options.php
			'yikes_inc_easy_mc_general_settings_page',
			'yikes_easy_mc_settings_general_section_callback'
		);

		/* End General Settings */

		/* Checkbox Settings */
		register_setting( 'yikes_inc_easy_mc_checkbox_settings_page', 'optin-checkbox-init' );

		/* Register General Settings Section */
		add_settings_section(
			'yikes_inc_easy_mc_checkbox_settings',
			'',
			'',
			'yikes_inc_easy_mc_checkbox_settings_page'
		);

		add_settings_field(
			'optin-checkbox-init',
			__( 'Select Checkboxes to Generate', 'yikes-inc-easy-mailchimp-extender' ),
			'',  // callback + validation inside of admin/partials/menu/options.php
			'yikes_inc_easy_mc_checkbox_settings'
		);
		/* End Checkbox Settings */

		/* reCAPTCHA Settings */

			register_setting( 'yikes_inc_easy_mc_recaptcha_settings_page' , 'yikes-mc-recaptcha-status' );
			register_setting( 'yikes_inc_easy_mc_recaptcha_settings_page' , 'yikes-mc-recaptcha-site-key' );
			register_setting( 'yikes_inc_easy_mc_recaptcha_settings_page' , 'yikes-mc-recaptcha-secret-key' );

			/* Register reCAPTCHA Settings Section */
			add_settings_section(
				'yikes_easy_mc_settings_recpatcha_section',
				'',
				'',
				'yikes_inc_easy_mc_recaptcha_settings_page'
			);

			add_settings_field(
				'yikes-mc-recaptcha-site-key',
				__( 'Enter reCAPTCHA Site Key', 'yikes-inc-easy-mailchimp-extender' ),
				'',  // callback + validation inside of admin/partials/menu/options.php
				'yikes_easy_mc_settings_recpatcha_section'
			);

			add_settings_field(
				'yikes-mc-recaptcha-secret-key',
				__( 'Enter reCAPTCHA Secret Key', 'yikes-inc-easy-mailchimp-extender' ),
				'',  // callback + validation inside of admin/partials/menu/options.php
				'yikes_easy_mc_settings_recpatcha_section'
			);

			add_settings_field(
				'yikes-mc-recaptcha-status',
				__( 'Enable ReCaptcha', 'yikes-inc-easy-mailchimp-extender' ),
				'',  // callback + validation inside of admin/partials/menu/options.php
				'yikes_easy_mc_settings_recpatcha_section'
			);

		/* End reCAPTCHA Settings */

		/* Debug Settings */
			register_setting( 'yikes_inc_easy_mc_debug_settings_page' , 'yikes-mailchimp-debug-status' );

			/* Register Debug Settings Section */
			add_settings_section(
				'yikes_easy_mc_settings_debug_section',
				'',
				'',
				'yikes_inc_easy_mc_debug_settings_page'
			);

			add_settings_field(
				'yikes-mailchimp-debug-status',
				__( 'Enable Debugging', 'yikes-inc-easy-mailchimp-extender' ),
				'',  // callback + validation inside of admin/partials/menu/options.php
				'yikes_easy_mc_settings_debug_section'
			);

		/* Custom Action Hook For Addon Settings */
			// custom action hook to allow our add-ons to take
			// advantage of our base settings
			do_action( 'yikes-mailchimp-settings-field' );

	}

	/**
	*	Options Sanitization & Validation
	*	@since complete re-write
	**/
	function yikes_mc_validate_api_key( $input ) {
		if( $input === '' ) {
			update_option( 'yikes-mc-api-validation' , 'invalid_api_key' );
			return '';
		}
		$api_key = strip_tags ( trim( $input ) );
		$dash_position = strpos( trim( $input ), '-' );
		if( $dash_position !== false ) {
			$manager = new Yikes_Inc_Easy_MailChimp_API_Manager( $api_key );
		} else {
			update_option( 'yikes-mc-api-invalid-key-response', __( 'Your API key appears to be invalid.', 'yikes-inc-easy-mailchimp-extender' ) );
			update_option( 'yikes-mc-api-validation' , 'invalid_api_key' );
			return $api_key;
		}

		$response = $manager->get_account_handler()->get_account( false );
		if( ! is_wp_error( $response ) ) {
			update_option( 'yikes-mc-api-validation' , 'valid_api_key' );
				// Clear the API key transient data
			$this->delete_yikes_mailchimp_transients();
		}  else {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			$error_logging->yikes_easy_mailchimp_write_to_error_log( $response->get_error_message() , __( "Connecting to MailChimp" , 'yikes-inc-easy-mailchimp-extender' ) , __( "Settings Page/General Settings" , 'yikes-inc-easy-mailchimp-extender' ) );
			update_option( 'yikes-mc-api-invalid-key-response' , $response->get_error_message() );
			update_option( 'yikes-mc-api-validation' , 'invalid_api_key' );
		}
		// returned the api key
		return $api_key;
	}

	/**
	* Generate Us Easy Forms for MailChimp Options Page
	*
	* @since    1.0.0
	*/
	function generatePageOptions() {
		require_once YIKES_MC_PATH . 'admin/partials/menu/options.php'; // include our options page
	}

	/**
	*	Check if users API key is valid, if not
	*	this function will apply a disabled attribute
	*	to form fields. (input, dropdowns, buttons etc.)
	* 	@since v5.5 re-write
	**/
	public function is_user_mc_api_valid_form( $echo = true ) {
		if ( $echo == true ) {
			if ( get_option( 'yikes-mc-api-validation', 'invalid_api_key' ) == 'invalid_api_key' ) {
				echo 'disabled="disabled"';
			}
		} else {
			if ( get_option( 'yikes-mc-api-validation', 'invalid_api_key' ) == 'invalid_api_key' ) {
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * Check for existing plugin options
	 *	if they exist, we need to migrate our options to
	 * the correct WordPress options API (old plugin stored options wierdly)
	 *
	 * @since    1.0.0
	 * @param      string    $yikes_inc_easy_mailchimp_extender       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function check_for_old_yks_mc_options() {
		$old_plugin_options = get_option( 'ykseme_storage' );
		// only perform options migrations if the site is not a multi-site setup
		if ( !is_multisite() ) {
			if( apply_filters( 'yikes_mc_old_options_filter' , $old_plugin_options ) ) {
				// display a notice to the user that they should 'migrate'
				// from the old plugin settings to the new ones
				add_action( 'admin_notices', array( $this , 'display_options_migrate_notice' ) , 11 );
			}
		}
	}

	/**
	 * Migrate our old options , to the new options API
	 * moving from 5.5 and beyond..
	 * @since
	*/
	public function migrate_old_yks_mc_options() {
		// include our migrate options helper file
		include_once YIKES_MC_PATH . 'admin/partials/upgrade-helpers/upgrade-migrate-options.php';
	}

	/**
		Admin Notices
		- Notifications displayed at the top of admin pages, back to the user
	**/

		/**
		 * Check for existing plugin options
		 *	if they exist, we need to migrate our options to
		 * the correct WordPress options API (old plugin stored options wierdly)
		 *
		 * @since    1.0.0
		 * @param      string    $yikes_inc_easy_mailchimp_extender       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */
		public function display_options_migrate_notice() {

			// Confirm that the necessary forms table in the database exists, else bail
			global $wpdb;
			if( $wpdb->get_var("show tables like '" . $wpdb->prefix . "yikes_easy_mc_forms'") != $wpdb->prefix . "yikes_easy_mc_forms" ) {
				return;
			}

			if( isset( $_GET['yikes-mc-options-migration-dismissed'] ) && $_GET['yikes-mc-options-migration-dismissed'] == 'true' ) {
					// Delete the options, start a-new! (this will disable the migration notice altogether)
					delete_option( 'widget_yikes_mc_widget' );
					delete_option( 'api_validation' );
					delete_option( 'ykseme_storage' );
					delete_option( 'yikes-mc-lists' );
				?>
					<div class="yikes-easy-mc-updated migrate-options-notice">
						<p><?php printf( __( "The previously stored options for %s have been cleared from the database. You should update the plugin options on the <a href='%s' title='Settings Page'>settings page</a> before continuing. You should also update the shortcodes used to generate your forms, and any widgets you may have previously set-up.", 'yikes-inc-easy-mailchimp-extender' ), '<strong>Us Easy Forms for MailChimp</strong>', admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings' ) ); ?></p>
					</div>
				<?php
			} else {
			?>
				<div class="yikes-easy-mc-updated migrate-options-notice">
					<p><?php printf( __( "It looks like you're upgrading from a previous version of %s.", 'yikes-inc-easy-mailchimp-extender' ), '<strong>Us Easy Forms for MailChimp</strong>' ); ?> <?php printf( __( "In the newest version of %s, the options data structure has changed. We've also moved the mailing lists into its own database table to allow for some higher level customization. Now you can easily create multiple forms and assign them to the same mailing list." , 'yikes-inc-easy-mailchimp-extender' ), '<strong>Us Easy Forms for MailChimp</strong>' ); ?></p>
					<p><?php _e( "Before you continue, it's strongly recommended you the perform the migration to ensure the plugin continues to function properly.", 'yikes-inc-easy-mailchimp-extender' ); ?></p>
					<p><em><?php _e( "It's also strongly recommended that you take a backup of your database.", 'yikes-inc-easy-mailchimp-extender' ); ?></em></p>
					<section id="migration-buttons">
						<!-- migrate button -->
						<form>
							<input type="hidden" name="yikes-mc-update-option-structure" value="yikes-mc-update-option-structure" />
							<a href="<?php echo wp_nonce_url( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-update' ) ), 'yikes-mc-migrate-options' , 'migrate_options_nonce' ); ?>" class="button-secondary"><?php _e( 'Perform Migration' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
						</form>
						<!-- dismiss button -->
						<form>
							<a href="<?php echo wp_nonce_url( esc_url_raw( admin_url() ), 'yikes-mc-dismiss-migration' , 'dismiss_migration_nonce' ); ?>" class="button-secondary"><?php _e( 'Dismiss Notice' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
						</form>
					</section>

				</div>
			<?php
			}
		}

		/*
		*	Search through multi dimensional array
		*	and return the index ( used to find the list name assigned to a form )
		*	- http://stackoverflow.com/questions/6661530/php-multi-dimensional-array-search
		*/
		function findMCListID($id, $array) {
		   foreach ($array as $key => $val) {
			   if ($val['id'] === $id) {
				   return $key;
			   }
		   }
		   return null;
		} // end

		/* Ajax Migrate Options */
		function migrate_archived_options() {
			// all options prefixed with 'yikes-mc-'
			$option_name = 'yikes-mc-'.$_POST['option_name'];
			$option_value = $_POST['option_value'];
			if( json_decode( $option_value ) ) {
				// decode our lists() array, and store it
				$opt_value = json_decode( $option_value, true );
			} else {
				$opt_value = $option_value;
			}
			update_option( $option_name, $opt_value );
			wp_die(); // this is required to terminate immediately and return a proper response
			exit;
		}

		/* Ajax Migrate Forms */
		function migrate_previously_setup_forms() {
			$option_name = $_POST['option_name'];
			$done = $_POST['done_import'];
			// Create some starter forms for the user
			// based on previously imported lists (to our old version)
			if( $option_name == 'yikes-mc-lists' ) {
				$option_value = $_POST['option_value'];
				$new_options = json_decode( stripslashes_deep( $option_value ) , true );

				$list_id = $new_options['id'];
				$form_name = $new_options['name'];
				$fields = $new_options['fields']; // our fields array

				$custom_styles = isset( $new_options['custom_styles'] ) ? $new_options['custom_styles']: '0'; // store as an array with all of our styles
				$custom_template = isset( $new_options['custom_template'] ) ? $new_options['custom_template'] : '0'; // store template data as an array ( active , template used )
				$redirect_user_on_submit = isset( $new_options['yks_mailchimp_redirect_'.$list_id] ) ? '1' : '0';
				$redirect_page = isset( $new_options['page_id_'.$list_id] ) ? $new_options['page_id_'.$list_id] : '';

				/* Insert Forms Function  */
				$this->form_interface->create_form( array(
					'list_id'                 => $list_id,
					'form_name'               => $form_name,
					'form_description'        => '',
					'fields'                  => $fields,
					'custom_styles'           => $custom_styles,
					'custom_template'         => $custom_template,
					'redirect_user_on_submit' => $redirect_user_on_submit,
					'redirect_page'           => $redirect_page,
					'submission_settings'     => '',
					'optin_settings'          => '',
					'error_messages'          => '',
					'custom_notifications'    => '',
					'impressions'             => '0',
					'submissions'             => '0',
					'custom_fields'           => '',
				) );
			}
			if( $done == 'done' ) {
				wp_send_json( array( 'form_name' => $form_name, 'completed_import' => true ) );
			} else {
				wp_send_json( array( 'form_name' => $form_name, 'completed_import' => false ) );
			}
			wp_die();
			exit;
		}

		/*
		*	generate_options_pages_sidebar_menu();
		*	Render our sidebar menu on all of the setings pages (general, form, checkbox, recaptcha, popup, debug etc. )
		*	@since v5.6 - complete re-write
		*/
		public function generate_options_pages_sidebar_menu() {
			if( isset( $_REQUEST['section'] ) ) {
				$selected = $_REQUEST['section'];
			}
			$installed_addons = get_option( 'yikes-easy-mc-active-addons' , array() );
			// sort our addons array alphabetically so they appear in similar orders across all sites
			asort( $installed_addons );
			?>
				<h3><span><?php _e( 'Additional Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></span></h3>
				<div class="inside">
					<ul id="settings-nav">
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] == 'general-settings' || !isset( $_REQUEST['section'] ) ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'general-settings' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=general-settings' ) ) ); ?>"><?php _e( 'General Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] == 'integration-settings' ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'integration-settings' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=integration-settings' ) ) ); ?>"><?php _e( 'Integration Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] == 'recaptcha-settings' ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'recaptcha-settings' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=recaptcha-settings' ) ) ); ?>"><?php _e( 'ReCaptcha Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] == 'api-cache-settings' ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'api-cache-settings' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=api-cache-settings' ) ) ); ?>"><?php _e( 'API Cache Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] ==  'debug-settings' ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'debug-settings' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings' ) ) ); ?>"><?php _e( 'Debug Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
						<li><?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] ==  'import-export-forms' ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => 'import-export-forms' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=import-export-forms' ) ) ); ?>"><?php _e( 'Import/Export' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></li>
					</ul>
					<?php
						// create our add-on settings pages
						if( !empty( $installed_addons ) ) {
							?>
							<hr class="add-on-settings-divider" />
							<strong><?php _e( 'Addon Settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
							<ul id="addon-settings-nav">
							<?php
							foreach( $installed_addons as $addon_name ) {
								?>
									<li>
										<?php if( isset( $_REQUEST['section'] ) && $_REQUEST['section'] ==  $addon_name ) { ?><div class="option-menu-selected-arrow"></div><?php } ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'section' => $addon_name, 'addon' => 'true' ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section='.$addon_name ) ) ); ?>"><?php echo ucwords( str_replace( '-' , ' ' , $addon_name ) ); ?></a></li>
								<?php
							}
							?>
							</ul>
							<?php
						}
						?>
				</div> <!-- .inside -->
			<?php
		}

		/*
		*	generate_manage_forms_sidebar();
		*	Render our sidebar menu on all of the setings pages (general, form, checkbox, recaptcha, popup, debug etc. )
		*	@since v5.6 - complete re-write
		*/
		public function generate_manage_forms_sidebar( $lists ) {
			// create a custom URL to allow for creating fields
			$url = esc_url_raw(
				add_query_arg(
					array(
						'action' => 'yikes-easy-mc-create-form',
						'nonce' => wp_create_nonce( 'create_mailchimp_form' )
					)
				)
			);
			?>
			<h3><?php _e( 'Create a New Signup Form' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>

			<div class="inside">

				<p class="description"><?php _e( "Give your form a name, select a MailChimp list to assign users to, then click 'Create'.", 'yikes-inc-easy-mailchimp-extender' ); ?></p>

				<form id="import-list-to-site" method="POST" action="<?php echo $url; ?>">
					<input type="hidden" name="import-list-to-site" value="1" />
					<!-- Name your new form -->
					<label for="form-name"><strong><?php _e( 'Form Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
						<input type="text" class="widefat input-field" placeholder="<?php _e( 'Form Name' , 'yikes-inc-easy-mailchimp-extender' ); ?>" name="form-name" id="form-name" <?php $this->is_user_mc_api_valid_form( true ); ?> required>
					</label>
					<!-- Name your new form -->
					<label for="form-description"><strong><?php _e( 'Form Description' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
						<textarea class="widefat input-field form-description" placeholder="<?php _e( 'Form Description' , 'yikes-inc-easy-mailchimp-extender' ); ?>" name="form-description" id="form-description" <?php $this->is_user_mc_api_valid_form( true ); ?>></textarea>
					</label>
					<!-- Associate this form with a list! -->
					<label for="associated-list"><strong><?php _e( 'Associated List' , 'yikes-inc-easy-mailchimp-extender' ); ?></strong>
						<select name="associated-list" id="associated-list" class=" input-field" <?php $this->is_user_mc_api_valid_form( true ); disabled( true, empty( $lists ) ); ?>>
							<?php
							if ( ! empty( $lists ) ) {
								foreach( $lists as $mailing_list ) {
									?>
									<option value="<?php echo $mailing_list['id']; ?>"><?php echo stripslashes( $mailing_list['name'] ) . ' (' . $mailing_list['stats']['member_count'] . ') '; ?></option>
									<?php
								}
							} else {
								if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) == 'invalid_api_key' ) {
									?>
									<option><?php echo __( "Please enter a valid API key." , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
									<?php
								} else {
									?>
									<option><?php echo __( "No lists were found on the account." , 'yikes-inc-easy-mailchimp-extender' ); ?></option>
									<?php

								}
							}
							?>
						</select>

						<?php
						if ( isset( $_GET['transient-cleared'] ) ) {
							if ( 'true' === $_GET['transient-cleared'] ) {
								?>
								<div class="yikes-list-refreshed-notice">
									<p><?php esc_attr_e( 'MailChimp list data has been succesfully refreshed.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								</div>
								<?php
							}
						}

						if( isset( $lists ) && empty( $lists ) ) {
							if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) != 'invalid_api_key' ) {
								?>
									<p class="description">
										<?php printf( __( 'Head over to <a href="http://www.MailChimp.com" title="%s">MailChimp</a> to create a new list.', 'yikes-inc-easy-mailchimp-extender' ) , __( 'Create a list' , 'yikes-inc-easy-mailchimp-extender' ) ); ?>
									</p>
								<?php
							}
						}
						?>
					</label>
					<?php
						if( $this->is_user_mc_api_valid_form( false ) ) {
							echo submit_button( __( 'Create', 'yikes-inc-easy-mailchimp-extender' ) , 'primary' , '' , false , array( 'style' => 'margin:.75em 0 .5em 0;' ) );
						} else {
							echo '<p class="description">' . __( "Please enter a valid MailChimp API key to get started." , 'yikes-inc-easy-mailchimp-extender' ) . '</p>';
							?>
								<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&settings-updated=true' ) ); ?>"><?php _e( 'general settings' , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
							<?php
						}
					?>
				</form>

				<!-- Clear API CACHE -->
				<?php
				if( isset( $lists ) && ! empty( $lists ) ) {
					if ( false !== get_transient( 'yikes-easy-mailchimp-list-data' ) ) { ?>
						<form action="<?php echo esc_url_raw( add_query_arg( array( 'action' => 'yikes-easy-mc-clear-transient-data' , 'nonce' => wp_create_nonce( 'clear-mc-transient-data' ) ) ) ); ?>" method="post">
							<input type="submit" class="button-secondary clear-mailchimp-api-cache" value="<?php _e( 'Refresh Lists' , 'yikes-inc-easy-mailchimp-extender' ); ?>" />
						</form>
					<?php }
				}
				?>
			</div> <!-- .inside -->
			<?php
		}

		/*
		*	Generate a dropdown of post and pages
		*	so the user can send the user to on form submission
		*/
		public function generate_page_redirect_dropdown( $redirect, $redirect_page, $custom_redirect_url ) {
				$post_types = get_post_types();
				?>
				<label id="redirect-user-to-selection-label" for="redirect-user-to-selection" class="<?php if( $redirect == '0' ) { echo 'yikes-easy-mc-hidden'; } ?>">
					<?php _e( "Select A Page or Post" , 'yikes-inc-easy-mailchimp-extender' ); ?>
					<select id="redirect-user-to-selection" name="redirect-user-to-selection" onchange="shouldWeDisplayCustomURL( this );return;">
				<?php
					// loop over registered post types, and query!
						foreach( $post_types as $registered_post_type ) {
							// exclude a few built in custom post types
							if( ! in_array( $registered_post_type , array( 'attachment' , 'revision' , 'nav_menu_item' ) ) ) {
								// run our query, to retreive the posts
								$pages = get_posts( array(
									'order' => 'ASC',
									'orderby' => 'post_title',
									'post_type' => $registered_post_type,
									'post_status' => 'publish',
									'numberposts' => -1
								) );
								// only show cpt's that have posts assigned
								if( !empty( $pages ) ) {
									?>
									<optgroup label="<?php echo ucwords( str_replace( '_' , ' ' , $registered_post_type ) ); ?>">
									<?php
										foreach( $pages as $page ) {
											?><option <?php selected( $redirect_page , $page->ID ); ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option><?php
										}
									?>
									</optgroup>
									<?php
								}
							}
						}
					?>
						<!-- Add the Custom URL option -->
						<optgroup label="Custom URL">
							<option <?php selected( $redirect_page, 'custom_url' ); ?> value="custom_url"><?php echo __( 'Custom URL', 'yikes-inc-easy-mailchimp-extender' ); ?></option>
						</optgroup>
					</select>

					<label name="custom-redirect-url" class="custom_redirect_url_label" <?php if( ! isset( $redirect_page ) || $redirect_page != 'custom_url' ) { echo 'style="display:none;"'; } ?>>
						<?php _e( "Enter Custom URL" , 'yikes-inc-easy-mailchimp-extender' ); ?>
						<input type="text" class="widefat custom-redirect-url" name="custom-redirect-url" value="<?php echo $custom_redirect_url; ?>" />
					</label>

				</label>
			<?php
		}

		/*
		*	generate_show_some_love_container()
		*	Generate a container, with some author info
		*
		* 	Displayed in sidebars
		*/
		public function generate_show_some_love_container() {
			// if no active add-ons are installed,
			// lets display our branding and add-on sidebar
			if( get_option( 'yikes-easy-mc-active-addons' , array() ) == array() ) {

				/* On Edit Forms Page Display Upsell to Customizer */
				$screen = get_current_screen();
				if( isset( $screen ) && $screen->base == 'admin_page_yikes-mailchimp-edit-form' ) {
				?>

					<div class="postbox yikes-easy-mc-postbox show-some-love-container">

						<?php $this->generate_edit_forms_upsell_ad(); ?>

					</div>

				<?php } else { ?>

					<div class="postbox yikes-easy-mc-postbox show-some-love-container">

						<!-- review us container -->
						<h3 data-alt-text="<?php _e( 'About YIKES, Inc.', 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'Show Us Some Love' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
						<div id="review-yikes-easy-mc" class="inside">

							<p>
								<?php _e( 'Leave a review' , 'yikes-inc-easy-mailchimp-extender' ); ?>
								<p class="star-container">
									<a href="https://wordpress.org/support/view/plugin-reviews/yikes-inc-easy-mailchimp-extender" target="_blank">
										<b class="dashicons dashicons-star-filled"></b>
										<b class="dashicons dashicons-star-filled"></b>
										<b class="dashicons dashicons-star-filled"></b>
										<b class="dashicons dashicons-star-filled"></b>
										<b class="dashicons dashicons-star-filled"></b>
									</a>
								</p>
							</p>

							<?php _e( 'Tweet about it' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							<p class="sidebar-container">
								<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/" data-text="I'm using the Easy Forms for MailChimp plugin by @YikesInc to grow my mailing list - it's awesome! -" data-hashtags="MailChimp">Tweet</a>
								<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
							</p>

							<?php _e( 'Vote that the plugin works' , 'yikes-inc-easy-mailchimp-extender' ); ?>
							<p class="sidebar-container">
								<a href="https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/" target="_blank">
									<?php _e( 'Vote for Compatibility' , 'yikes-inc-easy-mailchimp-extender' ); ?>
								</a>
							</p>
						</div>

						<p class="description sidebar-footer-text"><?php printf( __( "This plugin made with %s by %s" , 'yikes-inc-easy-mailchimp-extender' ), '<span class="dashicons dashicons-heart yikes-love"></span>', '<a href="http://www.yikesinc.com" target="_blank" title="YIKES Inc.">YIKES Inc.</a>' ); ?> </p>

						<section id="about-yikes-inc" class="inside">
							<a href="https://www.yikesinc.com" target="_blank" title="YIKES Inc.">
								<img src="<?php echo YIKES_MC_URL . 'includes/images/About_Page/yikes-logo.png'; ?>" class="about-sidebar-yikes-logo" />
							</a>
							<p><strong>YIKES Inc.</strong> &mdash; <?php _e( 'is a web design and development company located in Philadelphia, Pennsylvania, US. YIKES specializes in custom WordPress theme and plugin development, site maintenance, eCommerce, custom-built web-based applications and more.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
						</section>

						<p class="description sidebar-footer-text"><a href="#" class="about-yikes-inc-toggle" data-alt-text="<?php _e( 'Show YIKES Some Love', 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'About YIKES', 'yikes-inc-easy-mailchimp-extender' ); ?></a></p>

					</div>

					<div class="postbox yikes-easy-mc-postbox">

						<!-- review us container -->
						<h3><?php _e( 'Easy Forms for MailChimp Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
						<div id="review-yikes-easy-mc" class="inside">
							<p><?php _e( "Check out available add-ons for some seriously enhanced features." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
							<p><a class="button-secondary" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-addons' ) ); ?>" title="<?php _e( 'View Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php _e( 'View Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></p>
						</div>

					</div>
				<?php }
			}

			/**
			*	Custom action hook for our extensions to hook into
			*	@parameter	get_current_screen()	current screen information
			*/
			do_action( 'yikes-mailchimp-admin-sidebar', get_current_screen() );

		}

		/*
		*	generate_form_editor( $list_id )
		*	Submit an API request to get our merge variables, and build up a small form editor
		*	for users to 'customize' their form
		*	-
		* @parameters - $list_id - pass in the list ID to retreive merge variables from
		*/
		public function generate_form_editor( $form_fields, $list_id, $merge_variables, $interest_groups ) {

			// if no list id, die!
			if( ! $list_id ) {
				wp_die( __( "We've encountered an error. No list ID was sent." , 'yikes-inc-easy-mailchimp-extender' ) );
			}

			if( ! $merge_variables ) {
				wp_die( __( "We've encountered an error. Reload the page and try again. If the error persists, please reach out to support." , 'yikes-inc-easy-mailchimp-extender' ) );
			}

			if( ! empty( $form_fields ) ) {

				// find any fields that are assigned to this form, that don't exist in MailChimp
				// or else were going to run into issues when we submit the form
				$available_merge_variables	= array();
				$available_interest_groups	= array();

				// Default variables as arrays - these are used for holding the MailChimp merge field ID
				$merge_field_ids			= array();
				$mailchimp_merge_field_ids	= array();

				// loop over merge variables
				if ( ! empty( $merge_variables['merge_fields'] ) ) {
					$available_merge_variables = wp_list_pluck( $merge_variables['merge_fields'], 'tag' );
					$mailchimp_merge_field_ids = wp_list_pluck( $merge_variables['merge_fields'], 'merge_id' );
					
					// Array will look like $merge_tag => $merge_id
					foreach( $available_merge_variables as $index => $merge_tag ) { 
						$merge_field_ids[$merge_tag] = $mailchimp_merge_field_ids[$index];
					}
				}

				// loop over interest groups
				if ( ! empty( $interest_groups ) ) {
					$available_interest_groups = array_keys( $interest_groups );
				}

				// build our assigned fields
				$assigned_fields = array_keys( $form_fields );
				$merged_fields   = array_merge( $available_merge_variables, $available_interest_groups );
				$excluded_fields = array_diff( $assigned_fields, $merged_fields );

				$i = 1;
				foreach( $form_fields as $field ) {

					if ( isset( $field['merge'] ) ) {
						// @todo: don't use in_array()
						$excluded_field = in_array( $field['merge'], $excluded_fields, true );
						?>
						<section class="draggable" id="<?php echo $field['merge']; ?>">
							<!-- top -->
							<a class="expansion-section-title settings-sidebar">
								<span class="dashicons dashicons-plus yikes-mc-expansion-toggle"></span>
								<span class="yikes-mc-expansion-section-field-label"> <?php echo stripslashes( $field['label'] ); ?> </span>
								<?php if ( $excluded_field ) { ?>
									<img src="<?php echo YIKES_MC_URL . 'includes/images/warning.svg'; ?>" class="field-doesnt-exist-notice" title="<?php _e( 'Field no longer exists.' , 'yikes-inc-easy-mailchimp-extender' ); ?>" alt="<?php _e( 'Field no longer exists.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
								<?php } ?>
								<input maxlength="45" type="text" class="yikes-mc-edit-field-label-input" value="<?php echo stripslashes( $field['label'] ); ?>" />
								<span class="dashicons dashicons-yes yikes-mc-save-field-label-edits-icon" title="<?php _e( 'Click to save changes.' , 'yikes-inc-easy-mailchimp-extender' ); ?>"></span>
								<span class="dashicons dashicons-edit yikes-mc-edit-field-label-icon" title="<?php _e( 'Click to edit the label' , 'yikes-inc-easy-mailchimp-extender' ); ?>"></span>
								<span class="yikes-mc-edit-field-label-message"></span>
								<span class="field-type-text"><small><?php echo __( 'type' , 'yikes-inc-easy-mailchimp-extender' ) . ' : ' . $field['type']; ?></small></span>
							</a>
							<!-- expansion section -->
							<div class="yikes-mc-settings-expansion-section">

								<?php if ( $excluded_field ) { ?>
									<p class="yikes-mc-warning-message"><?php _e( "This field no longer exists in this list. Delete this field from the form to prevent issues on your website." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								<?php } ?>

								<!-- store field data -->
								<input type="hidden" class="yikes-mc-merge-field-label" name="field[<?php echo $field['merge']; ?>][label]" value="<?php echo $field['label']; ?>" />
								<input type="hidden" class="yikes-mc-merge-field-type" name="field[<?php echo $field['merge']; ?>][type]" value="<?php echo $field['type']; ?>" />
								<input type="hidden" class="yikes-mc-merge-field-tag" name="field[<?php echo $field['merge']; ?>][merge]" value="<?php echo $field['merge']; ?>" />
								<input type="hidden" class="field-<?php echo $field['merge']; ?>-position position-input" name="field[<?php echo $field['merge']; ?>][position]" value="<?php echo $i++; ?>" />
								<?php if ( isset( $merge_field_ids[ $field['merge'] ] ) && is_int( $merge_field_ids[ $field['merge'] ] ) ) { ?> 
									<input type="hidden" class="yikes-mc-merge-field-id" name="field[<?php echo $field['merge']; ?>][id]" value="<?php echo $merge_field_ids[ $field['merge'] ] ?>" />  
								<?php } ?>

								<?php if ( $field['type'] == 'radio' || $field['type'] == 'dropdown' || $field['type'] == 'select' ) {
									$choices = json_decode( $field['choices'], true );
								?>
									<input type="hidden" name="field[<?php echo $field['merge']; ?>][choices]" value='<?php echo esc_attr( json_encode( $choices ) ); ?>' />
								<?php } ?>

								<!-- Single or Double Opt-in -->
								<p class="type-container"><!-- necessary to prevent skipping on slideToggle(); -->

									<table class="form-table form-field-container">

										<!-- Merge Tag -->
										<tr valign="top">
											<td scope="row">
												<label for="merge-tag">
													<?php _e( 'Merge Tag' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<input class="widefat merge-tag-text" type="text" readonly value="<?php echo $field['merge']; ?>">
											</td>
										</tr>

										<!-- Placeholder Value -->
										<?php switch( $field['type'] ) {

											case 'text':
											case 'email':
											case 'url':
											case 'number';
											case 'birthday':
											case 'date':
											case 'zip':
											case 'phone':
										?>
										<!-- Placeholder -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Placeholder' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<input type="text" class="widefat" name="field[<?php echo $field['merge']; ?>][placeholder]" value="<?php echo isset( $field['placeholder'] ) ? $field['placeholder'] : '' ; ?>" />
												<p class="description"><small><?php _e( "Assign a placeholder value to this field.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<?php
											break;
										}
										?>

										<!-- Default Value -->
										<?php switch( $field['type'] ) {
											default:
											case 'text':
											case 'number':
											case 'url':
										?>
											<tr valign="top">
												<td scope="row">
													<label for="placeholder">
														<?php _e( 'Default Value' , 'yikes-inc-easy-mailchimp-extender' ); ?>
													</label>
												</td>
												<td>
													<input <?php if( $field['type'] != 'number' ) { ?> type="text" <?php } else { ?> type="number" <?php } ?> class="widefat" name="field[<?php echo $field['merge']; ?>][default]" <?php if( $field['type'] != 'url' ) { ?> value="<?php echo isset( $field['default'] ) ? stripslashes( wp_strip_all_tags( $field['default'] ) ) : ''; ?>" <?php } else { ?> value="<?php echo isset( $field['default'] ) ? stripslashes( wp_strip_all_tags( esc_url_raw( $field['default'] ) ) ) : ''; ?>" <?php } ?> />
													<p class="description"><small><?php _e( "Assign a default value to populate this field with on initial page load.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
													<?php
													switch( $field['type'] ) {
														case 'text':
															?>
																<p><small class="pre-defined-tag-link"><a href="#TB_inline?width=600&height=550&inlineId=pre-defined-tag-container" onclick="storeGlobalClicked( jQuery( this ) );" class="thickbox"><?php _e( 'View Pre-Defined Tags' , 'yikes-inc-easy-mailchimp-extender' ); ?></a></small></p>
															<?php
														break;
													} ?>
												</td>
											</tr>
										<?php
												break;

											case 'radio':
											?>
												<tr valign="top">
													<td scope="row">
														<label for="placeholder">
															<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
														</label>
													</td>
													<td>
														<?php
														if ( ! isset( $field['default_choice'] ) ) {
															$field['default_choice'] = 0;
														}
														$x = 0;
														foreach ( $choices as $choice => $value ) { ?>
															<label for="<?php echo $field['merge'].'-'.$x; ?>">
																<input id="<?php echo $field['merge'].'-'.$x; ?>"
																       type="radio"
																       name="field[<?php echo $field['merge']; ?>][default_choice]"
																       value="<?php echo $x; ?>" <?php checked( $field['default_choice'], $x ); ?>>
																<?php echo $value; ?>&nbsp;
															</label>
														<?php $x++; } ?>
														<p class="description"><small><?php _e( "Select the option that should be selected by default.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
													</td>
												</tr>

											<?php
												break;

											case 'dropdown':
											?>
												<tr valign="top">
													<td scope="row">
														<label for="placeholder">
															<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
														</label>
													</td>
													<td>
														<select type="default" name="field[<?php echo $field['merge']; ?>][default_choice]">
															<?php foreach( json_decode( $field['choices'], true ) as $choice => $value ) { ?>
																<option value="<?php echo $choice; ?>" <?php selected( $field['default_choice'] , $choice ); ?>><?php echo $value; ?></option>
															<?php } ?>
														</select>
														<p class="description"><small><?php _e( "Which option should be selected by default?", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
													</td>
												</tr>

											<?php
												break;

												case "birthday":
												case "address":
													break;

										?>

										<?php } // end Default Value ?>


										<!-- Field Description -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Description' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<textarea class="widefat field-description-input" name="field[<?php echo $field['merge']; ?>][description]"><?php echo isset( $field['description'] ) ? stripslashes( esc_html( $field['description'] ) ) : '' ; ?></textarea>
												<p class="description"><small><?php _e( "Enter the description for the form field. This will be displayed to the user and will provide some direction on how the field should be filled out or selected.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Additional Classes -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Additional Classes' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<input type="text" class="widefat" name="field[<?php echo $field['merge']; ?>][additional-classes]" value="<?php echo isset( $field['additional-classes'] ) ? stripslashes( wp_strip_all_tags( $field['additional-classes'] ) ) : '' ; ?>" />
												<p class="description"><small><?php printf( __( "Assign additional classes to this field. %s.", 'yikes-inc-easy-mailchimp-extender' ), '<a target="_blank" href="' . esc_url( 'https://yikesplugins.com/support/knowledge-base/bundled-css-classes/' ) . '">' . __( 'View bundled classes', 'yikes-inc-easy-mailchimp-extender' ) . '</a>' );?></small></p>
											</td>
										</tr>
										<!-- Required Toggle -->
										<tr valign="top" class="yikes-checkbox-container">
											<td scope="row">
												<label for="field-required">
													<?php _e( 'Field Required?' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $checked = isset( $field['require'] ) ? $field['require'] : '0'; ?>
												<input type="checkbox" class="widefat" value="1" name="field[<?php echo $field['merge']; ?>][require]" <?php checked( $checked , 1 ); ?> <?php if( $field['merge'] == 'EMAIL' ) {  ?> disabled="disabled" checked="checked" title="<?php echo __( 'Email is a required field.' , 'yikes-inc-easy-mailchimp-extender' ); } ?>">
												<p class="description"><small><?php _e( "Require this field to be filled in before the form can be submitted.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Visible Toggle -->
										<tr valign="top" class="yikes-checkbox-container">
											<td scope="row">
												<label for="hide-field">
													<?php _e( 'Hide Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $hide = isset( $field['hide'] ) ? $field['hide'] : '0'; ?>
												<input type="checkbox" class="widefat" value="1" name="field[<?php echo $field['merge']; ?>][hide]" <?php checked( $hide , 1 ); ?> <?php if( $field['merge'] == 'EMAIL' ) {  ?> disabled="disabled" title="<?php echo __( 'Cannot toggle email field visibility.' , 'yikes-inc-easy-mailchimp-extender' ); } ?>">
												<p class="description"><small><?php _e( "Hide this field from being displayed on the front end.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Toggle Field Label Visibility -->
										<tr valign="top" class="yikes-checkbox-container">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Hide Label' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $hide_label = isset( $field['hide-label'] ) ? $field['hide-label'] : '0'; ?>
												<input type="checkbox" name="field[<?php echo $field['merge']; ?>][hide-label]" value="1" <?php checked( $hide_label , 1 ); ?>/>
												<p class="description"><small><?php _e( "Toggle field label visibility.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Display Phone/Date Formats back to the user -->
										<!-- Phone Format Initial Load -->
										<?php
											switch( $field['type'] ) {
												/* Store the phone format, for properly regex pattern */
												case 'phone':
												case 'birthday':
												case 'date':
													?>
														<tr valign="top">
															<td scope="row">
																<label for="placeholder">
																	<?php
																		switch( $field['type'] ) {
																			default:
																			case 'birthday':
																				$type = __( 'Date Format' , 'yikes-inc-easy-mailchimp-extender' );
																				$format = ( isset( $field['date_format'] ) ) ? $field['date_format'] : 'MM/DD';
																				$format_name = 'date_format';
																				break;

																			case 'date':
																				$type = __( 'Date Format' , 'yikes-inc-easy-mailchimp-extender' );
																				$format = ( isset( $field['date_format'] ) ) ? $field['date_format'] : 'MM/DD/YYYY';
																				$format_name = 'date_format';
																				break;

																			case 'phone':
																				$type = __( 'Phone Format' , 'yikes-inc-easy-mailchimp-extender' );
																				$format = isset( $field['phone_format'] ) && ! empty( $field['phone_format'] ) ? $field['phone_format'] : __( 'International', 'yikes-inc-easy-mailchimp-extender' );
																				$format_name = 'phone_format';
																				break;
																		}
																		echo $type;
																	?>
																</label>
															</td>
															<td>
																<strong><?php echo $format; ?></strong>
																<input type="hidden" name="field[<?php echo $field['merge']; ?>][<?php echo $format_name; ?>]" value="<?php echo $format; ?>" />
																<p class="description"><small>
																	<?php printf( __( 'To change the %s please head over to <a href="%s" title="MailChimp" target="_blank">MailChimp</a>. If you alter the format, you should re-import this field.', 'yikes-inc-easy-mailchimp-extender' ), strtolower( $type ), esc_url( 'http://www.mailchimp.com' ) ); ?>
																</small></p>
															</td>
														</tr>
													<?php
												break;
												// others..
												default:
													break;
											}
										?>
										<!-- End Date/Phone Formats -->
										<!-- Toggle Buttons -->
										<tr valign="top">
											<td scope="row">
												&nbsp;
											</td>
											<td>
												<span class="toggle-container">
													<a href="#" class="close-form-expansion"><?php _e( "Close" , 'yikes-inc-easy-mailchimp-extender' ); ?></a> |
													<a href="#" class="remove-field" alt="<?php echo $field['merge']; ?>"><?php _e( "Remove Field" , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
												</span>
											</td>
										</tr>
									</table>
								</p>

							</div>
						</section>
						<?php



					} else { // THIS IS AN INTEREST GROUP!

						?>
						<section class="draggable" id="<?php echo $field['group_id']; ?>">
							<!-- top -->
							<a href="#" class="expansion-section-title settings-sidebar">
								<span class="dashicons dashicons-plus yikes-mc-expansion-toggle"></span><?php echo stripslashes( $field['label'] ); ?>
								<?php if( in_array( $field['group_id'] , $excluded_fields ) ) { ?>
									<img src="<?php echo YIKES_MC_URL . 'includes/images/warning.svg'; ?>" class="field-no-longer-exists-warning" title="<?php _e( 'Field no longer exists.' , 'yikes-inc-easy-mailchimp-extender' ); ?>" alt="<?php _e( 'Field no longer exists.' , 'yikes-inc-easy-mailchimp-extender' ); ?>">
								<?php } ?>
								<span class="field-type-text"><small><?php echo __( 'type' , 'yikes-inc-easy-mailchimp-extender' ) . ' : ' . $field['type']; ?></small></span>
							</a>
							<!-- expansion section -->
							<div class="yikes-mc-settings-expansion-section">

								<!-- check if this field exists in the available interest group array -->
								<?php if( in_array( $field['group_id'] , $excluded_fields ) ) { ?>
									<p class="yikes-mc-warning-message"><?php _e( "This field no longer exists in this list. Delete this field from the form to prevent issues on the front end." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
								<?php } ?>

								<!-- store the label -->
								<input type="hidden" name="field[<?php echo $field['group_id']; ?>][label]" value="<?php echo $field['label']; ?>" />
								<input type="hidden" name="field[<?php echo $field['group_id']; ?>][type]" value="<?php echo $field['type']; ?>" />
								<input type="hidden" name="field[<?php echo $field['group_id']; ?>][group_id]" value="<?php echo $field['group_id']; ?>" />
								<input type="hidden" name="field[<?php echo $field['group_id']; ?>][groups]" value='<?php echo esc_attr( json_encode( json_decode( $field['groups'], true ) ) ); ?>' />

								<!-- Single or Double Opt-in -->
								<p class="type-container"><!-- necessary to prevent skipping on slideToggle(); -->

									<table class="form-table form-field-container">
										<!-- Default Value -->
										<?php switch( $field['type'] ) {
											default:
											case 'radio':
											case 'checkboxes':
											?>
												<tr valign="top">
													<td scope="row">
														<label for="placeholder">
															<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
														</label>
													</td>
													<td>
														<?php
														if( $field['type'] != 'checkboxes' ) {
															if ( ! isset( $field['default_choice'] ) ) {
																$group_options           = json_decode( stripslashes( $field['groups'] ), true );
																$field['default_choice'] = key( $group_options );
															}
														} else {
															if ( ! isset( $field['default_choice'] ) ) {
																$field['default_choice'] = array();
															}
														}

														foreach( json_decode( $field['groups'], true ) as $id => $group ) {
															$field_id   = "{$field['group_id']}-{$id}";
															$field_type = 'hidden' == $field['type'] ? 'checkbox' : $field['type'];
															$field_type = 'checkboxes' == $field_type ? 'checkbox' : $field_type;
															$field_name = "field[{$field['group_id']}][default_choice]";
															$field_name = 'checkbox' == $field_type ? $field_name . '[]' : $field_name;

															// Determine if the current group is checked.
															$checked = '';
															switch ( $field_type ) {
																case 'radio':
																default:
																	$checked = checked( $field['default_choice'], $id, false );
																	break;

																case 'checkbox':
																case 'hidden':
																	if ( in_array( $id, (array) $field['default_choice'] ) ) {
																		$checked = checked( true, true, false );
																	}
															}

															?>
															<label for="<?php echo $field_id; ?>">
																<input id="<?php echo $field_id; ?>"
																       type="<?php echo $field_type; ?>"
																       name="<?php echo $field_name; ?>"
																       value="<?php echo $id; ?>" <?php echo $checked; ?>>
																<?php echo stripslashes( str_replace( '\'' , '' , $group ) ); ?>&nbsp;
															</label>
															<?php
														} ?>
														<p class="description"><small><?php _e( "Select the option that should be selected by default.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
													</td>
												</tr>

											<?php
												break;

											case 'dropdown':
											?>
												<tr valign="top">
													<td scope="row">
														<label for="placeholder">
															<?php _e( 'Default Selection' , 'yikes-inc-easy-mailchimp-extender' ); ?>
														</label>
													</td>
													<td>
														<select type="default" name="field[<?php echo $field['group_id']; ?>][default_choice]">
															<?php foreach( json_decode( stripslashes_deep( $field['groups'] ) , true ) as $id => $group ) { ?>
																<option value="<?php echo $id; ?>" <?php selected( $field['default_choice'] , $id ); ?>><?php echo stripslashes( $group ); ?></option>
															<?php } ?>
														</select>
														<p class="description"><small><?php _e( "Which option should be selected by default?", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
													</td>
												</tr>

											<?php
												break;
										?>

										<?php } // end Default Value ?>

										<!-- Field Description -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Description' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<textarea class="widefat field-description-input" name="field[<?php echo $field['group_id']; ?>][description]"><?php echo isset( $field['description'] ) ? stripslashes( esc_html( $field['description'] ) ) : '' ; ?></textarea>
												<p class="description"><small><?php _e( "Enter the description for the form field. This will be displayed to the user and provide some direction on how the field should be filled out or selected.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>

										<!-- Additional Classes -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Additional Classes' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<input type="text" class="widefat" name="field[<?php echo $field['group_id']; ?>][additional-classes]" value="<?php echo isset( $field['additional-classes'] ) ? stripslashes( wp_strip_all_tags( $field['additional-classes'] ) ) : '' ; ?>" />
												<p class="description"><small><?php printf( __( "Assign additional classes to this field. %s.", 'yikes-inc-easy-mailchimp-extender' ), '<a target="_blank" href="' . esc_url( 'https://yikesplugins.com/support/knowledge-base/bundled-css-classes/' ) . '">' . __( 'View bundled classes', 'yikes-inc-easy-mailchimp-extender' ) . '</a>' );?></small></p>
											</td>
										</tr>
										<!-- Required Toggle -->
										<tr valign="top">
											<td scope="row">
												<label for="field-required">
													<?php _e( 'Field Required?' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $checked = isset( $field['require'] ) ? $field['require'] : '0'; ?>
												<input type="checkbox" class="widefat" value="1" name="field[<?php echo $field['group_id']; ?>][require]" <?php checked( $checked , 1 ); ?>>
												<p class="description"><small><?php _e( "Require this field to be filled in before the form can be submitted.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Visible Toggle -->
										<tr valign="top">
											<td scope="row">
												<label for="hide-field">
													<?php _e( 'Hide Field' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $hide = isset( $field['hide'] ) ? $field['hide'] : '0'; ?>
												<input type="checkbox" class="widefat" value="1" name="field[<?php echo $field['group_id']; ?>][hide]" <?php checked( $hide , 1 ); ?>>
												<p class="description"><small><?php _e( "Hide this field from being displayed on the front end.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Toggle Field Label Visibility -->
										<tr valign="top">
											<td scope="row">
												<label for="placeholder">
													<?php _e( 'Hide Label' , 'yikes-inc-easy-mailchimp-extender' ); ?>
												</label>
											</td>
											<td>
												<?php $hide = isset( $field['hide-label'] ) ? $field['hide-label'] : '0'; ?>
												<input type="checkbox" name="field[<?php echo $field['group_id']; ?>][hide-label]" value="1" <?php checked( $hide , 1 ); ?>/>
												<p class="description"><small><?php _e( "Toggle field label visibility.", 'yikes-inc-easy-mailchimp-extender' );?></small></p>
											</td>
										</tr>
										<!-- Toggle Buttons -->
										<tr valign="top">
											<td scope="row">
												&nbsp;
											</td>
											<td>
												<span class="toggle-container">
													<a href="#" class="close-form-expansion"><?php _e( "Close" , 'yikes-inc-easy-mailchimp-extender' ); ?></a> |
													<a href="#" class="remove-field" alt="<?php echo $field['group_id']; ?>"><?php _e( "Remove Field" , 'yikes-inc-easy-mailchimp-extender' ); ?></a>
												</span>
											</td>
										</tr>
									</table>
								</p>

							</div>
						</section>
						<?php
					}	// its an interest group!
				}
			} else {
				?>
					<h4 class="no-fields-assigned-notice non-draggable-yikes"><em><?php _e( 'No fields are assigned to this form. Select fields from the right hand column to add to this form.' , 'yikes-inc-easy-mailchimp-extender' ); ?></em></h4>
				<?php
			}
				/* Pre Defined Merge Tag Container - Always rendered so the modal appears and links are clickable on initial page load */
				add_thickbox();
				// enqueue jquery qtip for our tooltip
				wp_enqueue_script( 'jquery-qtip-tooltip' , YIKES_MC_URL . 'admin/js/min/jquery.qtip.min.js' , array( 'jquery' ) );
				wp_enqueue_style( 'jquery-qtip-style' ,  YIKES_MC_URL . 'admin/css/jquery.qtip.min.css' );
					$available_tags = array(
						array(
							'tag' => '{page_title}',
							'description' => '<h4 class="tooltip-title">' . __( 'Page Title', 'yikes-inc-easy-mailchimp-extender' ) . ' | <small>{page_title}</small></h4><hr />' . __( 'Pre-populate the field with the current page or post title that the user is on when opting in to your mailing list.' , 'yikes-inc-easy-mailchimp-extender' ),
							'title' => __( 'Page Title', 'yikes-inc-easy-mailchimp-extender' )
						),
						array(
							'tag' => '{page_id}',
							'description' => '<h4 class="tooltip-title">' . __( 'Page ID', 'yikes-inc-easy-mailchimp-extender' ) . ' | <small>{page_id}</small></h4><hr />' . __( 'Pre-populate the field with the current page or post ID that the user is on when opting in to your mailing list.' , 'yikes-inc-easy-mailchimp-extender' ),
							'title' => __( 'Page ID', 'yikes-inc-easy-mailchimp-extender' )
						),
						array(
							'tag' => '{page_url}',
							'description' => '<h4 class="tooltip-title">' . __( 'Page URL', 'yikes-inc-easy-mailchimp-extender' ) . ' | <small>{page_url}</small></h4><hr />' . __( 'Pre-populate the field with the current page URL that the user is on when opting in to your mailing list.' , 'yikes-inc-easy-mailchimp-extender' ),
							'title' => __( 'Page URL', 'yikes-inc-easy-mailchimp-extender' )
						),
						array(
							'tag' => '{blog_name}',
							'description' => '<h4 class="tooltip-title">' . __( 'Blog Name', 'yikes-inc-easy-mailchimp-extender' ) . ' | <small>{blog_name}</small></h4><hr />' . __( 'Pre-populate the field with the current blog name that the user is on when opting in to your mailing list. This is especially helpful for multi-site networks.' , 'yikes-inc-easy-mailchimp-extender' ),
							'title' => __( 'Blog Name', 'yikes-inc-easy-mailchimp-extender' )
						),
						array(
							'tag' => '{user_logged_in}',
							'description' => '<h4 class="tooltip-title">' . __( 'User Logged In', 'yikes-inc-easy-mailchimp-extender' ) . ' | <small>{user_logged_in}</small></h4><hr />' . __( 'Detects if a user is logged in and pre-populates the field with an appropriate value.' , 'yikes-inc-easy-mailchimp-extender' ),
							'title' => __( 'User Logged In', 'yikes-inc-easy-mailchimp-extender' )
						),
					);
				?>
				<!-- tooltips -->
				<script type="text/javascript">
					/* Initialize Qtip tooltips for pre-defined tags */
					jQuery( document ).ready( function() {
						jQuery( '.dashicons-editor-help' ).each( function() {
							 jQuery( this ).qtip({
								 content: {
									 text: jQuery( this ).next( '.tooltiptext' ),
									 style: {
										def: false
									 }
								 }
							 });
						 });
						 jQuery( '.qtip' ).each( function() {
							jQuery( this ).removeClass( 'qtip-default' );
						 });
					});
				</script>

				<div id="pre-defined-tag-container">
					<input type="hidden" value="" class="clicked-input">
					<div id="pre-defined-tag-interior-container">
						<h3><?php _e( 'Pre Defined Tags' , 'yikes-inc-easy-mailchimp-extender' ); ?></h3>
						<p class="description"><?php _e( 'You can use any of the following tags to populate a MailChimp text field with dynamic content. This can be used to determine which page the user signed up on, if the user was logged in and more.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
						<ul>
							<?php foreach( apply_filters( 'yikes-mailchimp-custom-default-value-tags' , $available_tags ) as $tag ) { ?>
								<li class="tooltop-tag">
									<!-- link/tag -->
									<a href="#" onclick="populateDefaultValue( '<?php echo $tag['tag']; ?>' );return false;" data-attr-tag="<?php echo $tag['tag']; ?>" title="<?php echo $tag['title']; ?>"><?php echo $tag['title']; ?></a>
									<!-- help icon -->
									<span class="dashicons dashicons-editor-help"></span>
									<!-- tooltip -->
									<div class="tooltiptext qtip-bootstrap yikes-easy-mc-hidden"><?php echo $tag['description']; ?></div>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php
		}

		/*
		*	build_available_merge_vars( $list_id )
		*	Submit an API request to get our merge variables, and build up a small form editor
		*	for users to 'customize' their form
		*	-
		* @parameters - $list_id - pass in the list ID to retreive merge variables from
		*/
		public function build_available_merge_vars( $form_fields , $available_merge_variables ) {
			$fields_assigned_to_form = array();
			foreach ( $form_fields as $field ) {
				if ( isset( $field['merge'] ) ) {
					$fields_assigned_to_form[ $field['merge'] ] = true;
				}
			}

			if ( ! empty( $available_merge_variables['merge_fields'] ) ) {
				?>
				<ul id="available-fields"><?php
				foreach ( $available_merge_variables['merge_fields'] as $merge_var ) {
					if ( isset( $fields_assigned_to_form[ $merge_var['tag'] ] ) ) {
						?>
						<li class="available-form-field not-available"
						    alt="<?php echo $merge_var['tag']; ?>"
						    data-attr-field-type="<?php echo esc_attr( $merge_var['type'] ); ?>"
						    data-attr-field-name="<?php echo esc_attr( $merge_var['name'] ); ?>"
						    data-attr-form-id="<?php echo esc_attr( $available_merge_variables['list_id'] ); ?>"
						    title="<?php esc_attr_e( 'Already assigned to your form', 'yikes-inc-easy-mailchimp-extender' ); ?>"
						    disabled="disabled">
							<?php echo stripslashes( $merge_var['name'] );
							if ( $merge_var['required'] ) {
								echo ' <span class="field-required" title="' . __( 'required field', 'yikes-inc-easy-mailchimp-extender' ) . '">*</span>';
							} ?>
							<small class="field-type-text"><?php echo $merge_var['type']; ?></small>
						</li>
						<?php
					} else {
						?>
						<li class="available-form-field"
						    alt="<?php echo $merge_var['tag']; ?>"
						    data-attr-field-type="<?php echo esc_attr( $merge_var['type'] ); ?>"
						    data-attr-field-name="<?php echo esc_attr( $merge_var['name'] ); ?>"
						    data-attr-form-id="<?php echo esc_attr( $available_merge_variables['list_id'] ); ?>">
							<?php echo stripslashes( $merge_var['name'] );
							if ( $merge_var['required'] ) {
								echo ' <span class="field-required" title="' . __( 'required field', 'yikes-inc-easy-mailchimp-extender' ) . '">*</span>';
							} ?>
							<small class="field-type-text"><?php echo $merge_var['type']; ?></small>
						</li>
						<?php
					}
				}
				?></ul>
				<a href="#" class="add-field-to-editor button-secondary yikes-easy-mc-hidden" style="display:none;">
					<small>
						<span class="dashicons dashicons-arrow-left-alt add-to-form-builder-arrow"></span> <?php _e( 'Add to Form Builder', 'yikes-inc-easy-mailchimp-extender' ); ?>
					</small>
				</a>
				<?php
			}
		}

		/*
		*	build_available_interest_groups( $form_fields , $available_interest_groups )
		*	Submit an API request to get our merge variables, and build up a small form editor
		*	for users to 'customize' their form
		*	-
		* @parameters - $list_id - pass in the list ID to retreive merge variables from
		*/
		public function build_available_interest_groups( $form_fields , $available_interest_groups , $list_id ) {
			$fields_assigned_to_form = array();
			if ( ! empty( $form_fields ) ) {
				foreach ( $form_fields as $field ) {
					if ( isset( $field['group_id'] ) ) {
						$fields_assigned_to_form[ $field['group_id'] ] = true;
					}
				}
			}

			if ( ! empty( $available_interest_groups ) ) {
				?>
				<ul id="available-interest-groups"><?php
				foreach ( $available_interest_groups as $interest_group ) {
					if ( isset( $fields_assigned_to_form[ $interest_group['id'] ] ) ) {
						?>
						<li class="available-interest-group not-available" alt="<?php echo $interest_group['id']; ?>" data-attr-field-name="<?php echo stripslashes( $interest_group['title'] ); ?>" data-attr-field-type="<?php echo $interest_group['type']; ?>" data-attr-form-id="<?php echo $list_id; ?>" title="<?php _e( 'Already assigned to your form', 'yikes-inc-easy-mailchimp-extender' ); ?>" disabled="disabled"><?php echo stripslashes( $interest_group['title'] ); ?>
							<small class="field-type-text"><?php echo $interest_group['type']; ?></small>
						</li>
						<?php
					} else {
						?>
						<li class="available-interest-group" alt="<?php echo $interest_group['id']; ?>" data-attr-field-name="<?php echo stripslashes( $interest_group['title'] ); ?>" data-attr-field-type="<?php echo $interest_group['type']; ?>" data-attr-form-id="<?php echo $list_id; ?>"><?php echo stripslashes( $interest_group['title'] ); ?>
							<small class="field-type-text"><?php echo $interest_group['type']; ?></small>
						</li>
						<?php
					}
				}
				?></ul>
				<a href="#" class="add-interest-group-to-editor button-secondary yikes-easy-mc-hidden" style="display:none;">
					<small>
						<span class="dashicons dashicons-arrow-left-alt add-to-form-builder-arrow"></span> <?php _e( 'Add to Form Builder', 'yikes-inc-easy-mailchimp-extender' ); ?>
					</small>
				</a>
				<?php
			}
		}

		/*
		*	Create A New Form!
		*	Probably Move these to its own file,
		*	and include it here for easy maintenance
		*	- must clean up db tables , ensure what data is going in and what is needed...
		*/
		public function yikes_easy_mailchimp_create_form() {
			$nonce = $_REQUEST['nonce'];
			if( ! wp_verify_nonce( $nonce, 'create_mailchimp_form' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) );
			}

			$result = $this->form_interface->create_form( array(
				'list_id'          => sanitize_key( $_POST['associated-list'] ),
				'form_name'        => stripslashes( $_POST['form-name'] ),
				'form_description' => stripslashes( $_POST['form-description'] ),
			) );

			// if an error occurs during the form creation process
			if ( false == $result ) {
				// write it to the error log
				// if the form was not created successfully
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->maybe_write_to_log( __( 'Error creating a new form', 'yikes-inc-easy-mailchimp-extender') , __( "Creating a new form" , 'yikes-inc-easy-mailchimp-extender' ) , __( "Forms" , 'yikes-inc-easy-mailchimp-extender' ) );
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-edit-form&sql_error=' . urlencode( __( 'Error creating a new form', 'yikes-inc-easy-mailchimp-extender' ) ) ) ) );
			} else {
				// redirect the user to the new form edit page
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-edit-form&id=' . $result) ) );
			}
			exit();
		}

		/*
		*	Delete A Form !
		*	Probably Move these to its own file,
		*	and include it here for easy maintenance
		*	- must clean up db tables , ensure what data is going in and what is needed...
		*/
		public function yikes_easy_mailchimp_delete_form() {
			// grab & store our variables ( associated list & form name )
			$nonce = $_REQUEST['nonce'];
			$post_id_to_delete = $_REQUEST['mailchimp-form'];
			// verify our nonce
			if( ! wp_verify_nonce( $nonce, 'delete-mailchimp-form-'.$post_id_to_delete ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}

			$this->form_interface->delete_form( $post_id_to_delete );

			// redirect the user to the manage forms page, display confirmation
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&deleted-form=true' ) ) );
			exit();
		}

		/*
		*	Duplicate an entire form !
		*	Probably Move these to its own file,
		*/
		public function yikes_easy_mailchimp_duplicate_form() {
			// grab & store our variables ( associated list & form name )
			$nonce = $_REQUEST['nonce'];
			$post_id_to_clone = $_REQUEST['mailchimp-form'];
			// verify our nonce
			if( ! wp_verify_nonce( $nonce, 'duplicate-mailchimp-form-'.$post_id_to_clone ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}

			// Get the current form data.
			$form_data = $this->form_interface->get_form( $post_id_to_clone );

			// Update some of the data before duplication
			$form_data['form_name'] .= ' - Copy - ';
			$form_data['impressions'] = $form_data['submissions'] = 0;

			// Create the new form, and handle the result.
			$result = $this->form_interface->create_form( $form_data );

			if ( false === $result ) {
				// redirect the user to the manage forms page, display error
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&duplicated-form=false' ) ) );
			} else {
				// redirect the user to the manage forms page, display confirmation
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&duplicated-form=true' ) ) );
			}

			exit();
		}

		/*
		*	Reset a forms impression stats
		*/
		public function yikes_easy_mailchimp_reset_impression_stats() {
			// grab & store our variables ( associated list & form name )
			$nonce = $_REQUEST['nonce'];
			$form_id_to_reset = $_REQUEST['mailchimp-form'];
			// verify our nonce
			if( ! wp_verify_nonce( $nonce, 'reset-stats-mailchimp-form-'.$form_id_to_reset ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}

			$result = $this->form_interface->update_form(
				$form_id_to_reset,
				array(
					'impressions' => 0,
					'submissions' => 0
				)
			);

			if ( false === $result ) {
				// redirect the user to the manage forms page, display error
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&reset-stats=false' ) ) );
			} else {
				// redirect the user to the manage forms page, display confirmation
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&reset-stats=true' ) ) );
			}

			exit();
		}

		/*
		*	Update an entire form !
		*	Probably Move these to its own file,
		*/
		public function yikes_easy_mailchimp_update_form() {
			// grab & store our variables ( associated list & form name )
			$nonce = $_REQUEST['nonce'];
			$form_id = $_REQUEST['id'];

			// verify our nonce
			if ( ! wp_verify_nonce( $nonce, 'update-mailchimp-form-' . $form_id ) ) {
				wp_die(
					__( "We've run into an error. The security check didn't pass. Please try again.", 'yikes-inc-easy-mailchimp-extender' ),
					__( "Failed nonce validation", 'yikes-inc-easy-mailchimp-extender' ),
					array(
						'response'  => 500,
						'back_link' => true,
				) );
			}

			// store our values!
			$list_id                 = $_POST['associated-list'];
			$form_name               = stripslashes( $_POST['form-name'] );
			$form_description        = sanitize_text_field( stripslashes( $_POST['form-description'] ) );
			$redirect_user_on_submit = $_POST['redirect-user-on-submission'];
			$redirect_page           = $_POST['redirect-user-to-selection'];

			// stripslashes_deep on save, to prevent foreign languages from added excessive backslashes
			$assigned_fields = isset( $_POST['field'] ) ? stripslashes_deep( $_POST['field'] ): array();

			// setup our submission settings serialized array
			$submission_settings = array(
				'ajax'                   => $_POST['form-ajax-submission'],
				'redirect_on_submission' => $_POST['redirect-user-on-submission'],
				'redirect_page'          => $_POST['redirect-user-to-selection'],
				'custom_redirect_url'    => esc_url( $_POST['custom-redirect-url'] ),
				'hide_form_post_signup'  => $_POST['hide-form-post-signup'],
				'replace_interests'      => $_POST['replace-interest-groups'],
			);

			// setup our opt-in settings serialized array
			$optin_settings = array(
				'optin'                => $_POST['single-double-optin'],
				'update_existing_user' => $_POST['update-existing-user'],
				'send_update_email'    => $_POST['update-existing-email'],
			);

			// Setup our error settings serialized array
			$error_settings = array(
				'success'				=> trim( $_POST['yikes-easy-mc-success-message'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-success-message'] ) ) : '',
				'success-single-optin'	=> trim( $_POST['yikes-easy-mc-success-single-optin-message'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-success-single-optin-message'] ) ) : '',
				'success-resubscribed'	=> trim( $_POST['yikes-easy-mc-user-resubscribed-success-message'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-user-resubscribed-success-message'] ) ) : '',
				'general-error'			=> trim( $_POST['yikes-easy-mc-general-error-message'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-general-error-message'] ) ) : '',
				'already-subscribed'	=> trim( $_POST['yikes-easy-mc-user-subscribed-message'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-user-subscribed-message'] ) ) : '',
				'update-link'			=> trim( $_POST['yikes-easy-mc-user-update-link'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-user-update-link'] ) ) : '',
				'email-subject'			=> trim( $_POST['yikes-easy-mc-user-email-subject'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-user-email-subject'] ) ) : '',
				'email-body'			=> trim( $_POST['yikes-easy-mc-user-email-body'] ) ? trim( stripslashes( $_POST['yikes-easy-mc-user-email-body'] ) ) : '',
			);

			// Setup the new form settings array
			// @since 6.0.3.8
			// To Do: Combine date & time so it's a single unix timestamp
			$form_settings = array(
				'yikes-easy-mc-form-class-names'                 => trim( $_POST['yikes-easy-mc-form-class-names'] ),
				'yikes-easy-mc-inline-form'                      => $_POST['yikes-easy-mc-inline-form'][0],
				'yikes-easy-mc-submit-button-type'               => $_POST['yikes-easy-mc-submit-button-type'][0],
				'yikes-easy-mc-submit-button-text'               => trim( $_POST['yikes-easy-mc-submit-button-text'] ),
				'yikes-easy-mc-submit-button-image'              => esc_url( trim( $_POST['yikes-easy-mc-submit-button-image'] ) ),
				'yikes-easy-mc-submit-button-classes'            => trim( $_POST['yikes-easy-mc-submit-button-classes'] ),
				'yikes-easy-mc-form-schedule'                    => ( isset( $_POST['yikes-easy-mc-form-schedule'] ) ) ? '1' : '0',
				'yikes-easy-mc-form-restriction-start'           => strtotime( $_POST['yikes-easy-mc-form-restriction-start-date'] . ' ' . $_POST['yikes-easy-mc-form-restriction-start-time'] ),
				'yikes-easy-mc-form-restriction-end'             => strtotime( $_POST['yikes-easy-mc-form-restriction-end-date'] . ' ' . $_POST['yikes-easy-mc-form-restriction-end-time'] ),
				'yikes-easy-mc-form-restriction-pending-message' => trim( $_POST['yikes-easy-mc-form-restriction-pending-message'] ),
				'yikes-easy-mc-form-restriction-expired-message' => trim( $_POST['yikes-easy-mc-form-restriction-expired-message'] ),
				'yikes-easy-mc-form-login-required'              => ( isset( $_POST['yikes-easy-mc-form-login-required'] ) ) ? '1' : '0',
				'yikes-easy-mc-form-restriction-login-message'   => trim( $_POST['yikes-easy-mc-form-restriction-login-message'] ),
			);

			// additional custom fields (extensions / user defined fields)
			$custom_fields = array();
			if ( isset( $_POST['custom-field'] ) ) {
				foreach ( $_POST['custom-field'] as $custom_field => $custom_value ) {
					if ( is_array( $custom_value ) ) {
						$custom_fields[ $custom_field ] = array_filter( stripslashes_deep( $custom_value ) ); // array_filters to remove empty items (don't save them!)
					} else {
						$custom_fields[ $custom_field ] = stripslashes( $custom_value );
					}
				}
			}

			$form_updates = yikes_deep_parse_args(
				array(
					'list_id'                 => $list_id,
					'form_name'               => $form_name,
					'form_description'        => $form_description,
					'fields'                  => $assigned_fields,
					'custom_template'         => 0,
					'redirect_user_on_submit' => $redirect_user_on_submit,
					'redirect_page'           => $redirect_page,
					'submission_settings'     => $submission_settings,
					'optin_settings'          => $optin_settings,
					'error_messages'          => $error_settings,
					'form_settings'           => $form_settings,
					'custom_fields'           => $custom_fields,
				),
				$this->form_interface->get_form_defaults()
			);

			$this->form_interface->update_form( $form_id, $form_updates );

			/* Custom action hook which allows users to update specific options when a form is updated - used in add ons */
			do_action( 'yikes-mailchimp-save-form', $form_id,  $custom_fields );

			// redirect the user to the manage forms page, display confirmation
			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-edit-form&id=' . $form_id . '&updated-form=true' ) ) );
			exit();
		}

		public static function generate_default_email_body() {
			$email_body  = '<p>' . __( 'Greetings,', 'yikes-inc-easy-mailchimp-extender' ) . '</p>'; 

			$email_body .= '<p>';
			$email_body .= 	__( 'A request has been made to update your MailChimp account profile information.', 'yikes-inc-easy-mailchimp-extender' );
			$email_body .=	__( ' To do so please use the following link: ', 'yikes-inc-easy-mailchimp-extender' );
			$email_body .=	'[link]';
			$email_body .=		__( 'Update MailChimp Profile Info', 'yikes-inc-easy-mailchimp-extender' );
			$email_body .= 	'[/link]';
			$email_body .= '</p>';

			$email_body .= '<p>' . __( 'If you did not request this update, please disregard this email.', 'yikes-inc-easy-mailchimp-extender' ) . '</p>';

			$email_body .= '<p>&nbsp;</p>';
			$email_body .= '<p>' . sprintf( __( 'This email was sent from: %s', 'yikes-inc-easy-mailchimp-extender' ), '[url]' ) . '</p>';
			$email_body .= '<p>&nbsp;</p>';
			$email_body .= '<p>&nbsp;</p>';
			$email_body .= '<p style="font-size:13px;margin-top:5em;"><em>This email was generated by the <a href="http://www.wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/" target="_blank">Easy Forms for MailChimp</a> plugin, created by <a href="http://www.yikesinc.com" target="_blank">YIKES Inc.</a></em></p>';

			return $email_body;
		}

		/* Unsubscribe a given user from our list */
		public function yikes_easy_mailchimp_unsubscribe_user() {
			$nonce    = $_REQUEST['nonce'];
			$list_id  = $_REQUEST['mailchimp-list'];
			$email_id = $_REQUEST['email_id'];

			// verify our nonce
			if( ! wp_verify_nonce( $nonce, 'unsubscribe-user-' . $email_id ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}

			$response = yikes_get_mc_api_manager()->get_list_handler()->member_unsubscribe( $list_id, $email_id );
			if ( is_wp_error( $response ) ) {
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				$error_logging->maybe_write_to_log(
					$response->get_error_code(),
					__( "Unsubscribe User", 'yikes-inc-easy-mailchimp-extender' ),
					__( "Manage List Page", 'yikes-inc-easy-mailchimp-extender' )
				);
			}

			wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-mailchimp-view-list&list-id=' . $list_id . '&user-unsubscribed=true' ) ) );
			exit;
		}

		public function yikes_easy_mailchimp_create_missing_error_log() {
			// grab our nonnce
			$nonce = $_REQUEST['nonce'];
			// validate nonce
			if( !wp_verify_nonce( $nonce, 'create_error_log' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}
			// setup the path to the error log
			$error_log = fopen( plugin_dir_path( __FILE__ ) . '../includes/error_log/yikes-easy-mailchimp-error-log.php' , 'w' );
			try {
				// create the file
				fwrite( $error_log , '' );
				// close out
				fclose( $error_log );
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings&error_log_created=true' ) ) );
			} catch ( Exception $e ) {
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=debug-settings&error_log_created=false&error_message='.urlencode( $e->getMessage() ) ) ) );
			}
		}

		/*
		*	Clear Transient Data !
		*	Probably Move these to its own file,
		*/
		public function yikes_easy_mailchimp_clear_transient_data() {

			// verify our nonce
			$nonce = $_REQUEST['nonce'];
			if( ! wp_verify_nonce( $nonce, 'clear-mc-transient-data' ) ) {
				wp_die( __( "We've run into an error. The security check didn't pass. Please try again." , 'yikes-inc-easy-mailchimp-extender' ) , __( "Failed nonce validation" , 'yikes-inc-easy-mailchimp-extender' ) , array( 'response' => 500 , 'back_link' => true ) );
			}

			// delete all of the list_id transients
			$list_ids = $this->get_mailchimp_list_ids_on_account();
			foreach ( $list_ids as $id ) {
				delete_transient( "yikes_eme_list_{$id}" );
				delete_transient( "yikes_eme_merge_variables_{$id}" );
				delete_transient( "yikes_eme_interest_categories_{$id}" );
				delete_transient( "yikes_eme_segments_{$id}" );
				delete_transient( "yikes_eme_members_{$id}" );
			}

			delete_transient( 'yikes-easy-mailchimp-list-data' );
			delete_transient( 'yikes-easy-mailchimp-account-data' );
			delete_transient( 'yikes-easy-mailchimp-profile-data' );
			delete_transient( 'yikesinc_eme_list_ids' );
			delete_transient( 'yikes_eme_lists' );

			// if the request came from the settings page, redirect to the settings page
			$referer = wp_get_referer();
			if ( $referer && ( strpos( $referer, 'yikes-inc-easy-mailchimp-settings' ) > 0 ) ) {
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings&section=api-cache-settings&transient-cleared=true' ) ) );
			} else {
				// else redirect to the manage forms page
				wp_redirect( esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp&transient-cleared=true' ) ) );
			}
			// redirect the user to the manage forms page, display confirmation
			exit;
		}

		/**
		*	Return an array of MailChimp lists associated with this account
		*
		*	Used when deleting the sites MailChimp cache stored
		*	@since 6.0.2
		*	@return 	$list_id_array - array of list id's to loop over
		*/
		public function get_mailchimp_list_ids_on_account() {
			$api_key = yikes_get_mc_api_key();
			if ( ! $api_key ) {
				// if no api key is set/site is not connected, return an empty array
				return array();
			}

			$lists = get_transient( 'yikesinc_eme_list_ids' );
			if ( false === $lists ) {
				$lists = yikes_get_mc_api_manager()->get_list_handler()->get_list_ids();
				if ( is_wp_error( $lists ) ) {
					return array();
				}
				set_transient( 'yikesinc_eme_list_ids', $lists, HOUR_IN_SECONDS );
			}

			return $lists;
		}

		/*
		*	Include our main Helper class file
		*	@since 6.0
		*/
		public function yikes_mailchimp_load_helper_class() {
			// check to see if it's already loaded up
			if( !class_exists( 'Yikes_Inc_Easy_Mailchimp_Forms_Helper' ) ) {
				// Include our main helper class file
				include_once( YIKES_MC_PATH . 'admin/partials/helpers/init.php' );
			}
		}

		/*
		*	Alter the color scheme based on the current user selection (this is done to help integrate the plugin into the dashboard more seamlessly)
		*
		*	@since 0.1
		*	@order 	requires that yikes-inc-easy-mailchimp-extender-admin.min.css be enqueued, so we can override the defaults (handle: yikes-inc-easy-mailchimp-extender-admin)
		* 	@retutrn print out custom styles to the admin header to alter the defualt blue color
		*/
		public function alter_yikes_easy_mc_color_scheme() {
			// get the current set color scheme for the logged in user
			$current_color_scheme = get_user_option( 'admin_color' );
			// switch over each color scheme, and set our variable
			switch ( $current_color_scheme ) {
				default:
				case 'fresh': // default blue (defined by this plugin)
					$main_color = '#00a0d2';
					break;
				case 'light': // light grey
					$main_color = '#E5E5E5';
					break;
				case 'blue': // light blue
					$main_color = '#52ACCC';
					break;
				case 'coffee': // light brown-ish
					$main_color = '#59524C';
					break;
				case 'ectoplasm': // purple
					$main_color = '#523F6D';
					break;
				case 'midnight': // black
					$main_color = '#363B3F';
					break;
				case 'ocean': // green/teal-ish
					$main_color = '#738E96';
					break;
				case 'sunrish': // red/orange
					$main_color = '#CF4944';
					break;
			}
			ob_start();
			?>
				<style>
					.yikes-easy-mc-postbox h3,
					.column-columnname .form-id-container,
					.mv_ig_list .nav-tab-active {
						background: <?php echo $main_color; ?>;
					}
					.mv_ig_list .arrow-down {
						border-top: 9pt solid <?php echo $main_color; ?>;
					}
				</style>
			<?php
			$override_admin_styles = ob_get_clean();
			// add our inline styles
			echo $override_admin_styles;
		}

		/**
		*	Hook in and display our support page/knowledge base articles
		*	on the support page
		*	@since 6.0.3.8
		*/
		public function hook_and_display_kb_article_RSS() {
			// include_once( YIKES_MC_PATH . 'admin/partials/helpers/knowledge-base-articles-RSS.php' );
			include_once( YIKES_MC_PATH . 'admin/partials/helpers/knowledge-base-article-links.php' );
		}

		/**
		*	Check the users version number, and display a notice to upgrade the databse if needed
		*	@since 6.0.4
		*/
		public function check_yikes_mc_table_version() {
			if( get_option( 'yikes_mc_database_version', '0.00' ) < '1.0' ) {
				require_once YIKES_MC_PATH . 'includes/class-yikes-inc-easy-mailchimp-extender-activator.php';
				global $wpdb;
				Yikes_Inc_Easy_Mailchimp_Extender_Activator::_activate_yikes_easy_mailchimp( $wpdb );
				// update the database option
				update_option( 'yikes_mc_database_version', '1.0' );
			}
		}

		/*
		*	Process [yikes-mailchimp-form-description] into the shortcode
		*	@since 6.0.4.4
		*/
		public function process_subscriber_count_shortcode_in_form_descriptions( $form_description, $form_id ) {
			$form_description = str_replace( '[yikes-mailchimp-subscriber-count]', do_shortcode( '[yikes-mailchimp-subscriber-count form="' . $form_id . '"]' ), $form_description );
			return $form_description;
		}

		/*
		*	Generate the sidebar advertisment on the 'Edit Form' page
		*	@since 6.0.3
		*/
		public function generate_edit_forms_upsell_ad() {
			$upsell_ads = glob( YIKES_MC_PATH . 'includes/upsells/*.php' );
			if ( $upsell_ads && ! empty( $upsell_ads ) ) {
				$ad_count = absint( count( $upsell_ads ) - 1 );
				$ad = $upsell_ads[ mt_rand( 0, $ad_count ) ];
				ob_start();
				include_once( $ad );
				$ad_content = ob_get_contents();
				ob_get_clean();
			}
			echo wp_kses_post( $ad_content );
		}

		/***
		 * Helper function to clear out transients stored by this plugin
		 *
		 * Mainly used when the API key is altered, changed or removed.
		 * @since 6.1.3
		 */
		public function delete_yikes_mailchimp_transients() {
			/* Clear All Transient Data */
			// Delete list data transient data
			delete_transient( 'yikes-easy-mailchimp-list-data' );
			// Delete list account data transient data
			delete_transient( 'yikes-easy-mailchimp-account-data' );
			// Delete profile data transient data
			delete_transient( 'yikes-easy-mailchimp-profile-data' );
			// Delete account activity transient data
			delete_transient( 'yikes-easy-mailchimp-account-activity' );
		}

	/**
	 * Perform a DB version check to see if we need to migrate our forms.
	 *
	 * @author Jeremy Pry
	 */
	public function check_db_version() {
		$option = get_option( 'yikes_easy_mailchimp_extender_version', '0.0.0' );
		if ( version_compare( $option, '6.2.0', '<' ) ) {
			$this->convert_db_to_option();
		}

		if ( version_compare( $option, YIKES_MC_VERSION, '<' ) ) {
			update_option( 'yikes_easy_mailchimp_extender_version', YIKES_MC_VERSION );
		}
	}

	/**
	 * Handle the conversion from custom table to WP Options.
	 *
	 * @author Jeremy Pry
	 */
	public function convert_db_to_option() {
		/** @var wpdb */
		global $wpdb;

		$db_interface     = new Yikes_Inc_Easy_MailChimp_Extender_Forms( $wpdb );
		$option_interface = new Yikes_Inc_Easy_MailChimp_Extender_Option_Forms();
		$form_option      = array();
		$form_ids         = $db_interface->get_form_ids();

		if ( empty( $form_ids ) ) {
			return;
		}

		foreach ( $form_ids as $form_id ) {
			$form_option[ $form_id ] = $db_interface->get_form( $form_id );
		}

		$option_interface->import_forms( $form_option, true );
	}

	/**
	 * Register the Opt-in widget.
	 *
	 * @author Jeremy Pry
	 */
	public function register_optin_widget() {
		register_widget( 'Yikes_Inc_Easy_Mailchimp_Extender_Widget' );
	}
}
