<?php

if (!function_exists('add_action')) {
	die('Unauthorized access.');
}

/**
 * Class Videe_Admin
 */
class Videe_Admin extends Videe_Abstract
{

	const NONCE = 'videe-update-key';
	const OPTION_ACTIVATION_REDIRECT = 'videe_activation_redirect';

	/**
	 *
	 * @var boolean
	 */
	private $initiated = false;

	public function init() {
		if (!$this->initiated) {
			$this->initHooks();
		}
	}

	public function activationRedirect() {
		if ($this->locator->getOption('activationRedirect')) {
			$this->locator->deleteOption('activationRedirect');
			
			if ($this->locator->getOption('manualRegistration')) {
				wp_redirect($this->getPageUrl('registration'));
			} else {
				wp_redirect($this->getPageUrl('tutorial'));
			}
			
		}
	}

	/**
	 * Plugin activation hook
	 */
	public function pluginActivation() {

		if (!current_user_can('activate_plugins')) {
			return;
		}

		$error = $this->checkPluginCompatibility();
		
		if (!$this->locator->user->register()) {
			$this->locator->setOption('manualRegistration', true);
		}
		$this->locator->piwik->trackActivation();
		$this->locator->setOption('activationRedirect', true);
	}

	private function checkPluginCompatibility() 
	{
		$error = false;
		$wpVersion = $GLOBALS['wp_version'];
		$phpVersion = phpversion();

		if (version_compare($wpVersion, $this->config['videeMinimumWpVersion'], '<')) {

			$error = array('msg' => sprintf(__('The «Videe.TV Video Monetization» plugin only supports PHP versions starting v.%s. ', 'videe'), $this->config['videeMinimumWpVersion'])
				. __('Please <a href="https://wordpress.org/download/release-archive/" target="_blank">upgrade WordPress</a> to a newer version.', 'videe'),
				'code' => 'WORDPRESS_VERSION_' . $wpVersion . ' < ' . $this->config['videeMinimumWpVersion']);
		}

		if (version_compare($wpVersion, $this->config['videeMaximumWpVersion'], '>')) {

			$error = array('msg' => sprintf(__('The «Videe.TV Video Monetization» plugin is not yet compatible with the Wordpress v.%s. ', 'videe'), $wpVersion)
				. __('We are working on this.', 'videe'),
				'code' => 'WORDPRESS_VERSION_' . $wpVersion . ' > ' . $this->config['videeMaximumWpVersion']);
		}

		if (version_compare($phpVersion, $this->config['minimumPhpVersion'], '<')) {

			$error = array('msg' => sprintf(__('The «Videe.TV Video Monetization» plugin only supports PHP versions starting v.%s. ', 'videe'), $this->config['minimumPhpVersion'])
				. __('Please update to activate the plugin.', 'videe'),
				'code' => 'PHP_VERSION_' . $phpVersion . ' < ' . $this->config['minimumPhpVersion']);
		}

		if (!function_exists('curl_version')) {
			$error = array('msg' => 'Please install  php5-curl extension  to run «Videe.TV Video Monetization» plugin.',
				'code' => 'CURL_NOT_INSTALLED');
		}

		return $error;
	}

	/**
	 * Show message on activation fail
	 *
	 * @param $message
	 */
	private function activationFail($error) {
		load_plugin_textdomain('videe');

		$errorCode = isset($error['code']) ? $error['code'] : '';
		$this->locator->piwik->trackActivationError($errorCode);

		deactivate_plugins($this->config['videePluginBasename']);
		wp_die($error['msg']);
	}

	/**
	 * Plugin deactivation hook
	 */
	public function pluginDeactivation() {

		if (!current_user_can('activate_plugins')) {
			return;
		}

		$this->locator->piwik->trackDeactivation();
		
		foreach (Videe_Options::getDbOptions() as $option) {
			delete_option($option);
		}

		remove_all_actions('add_meta_boxes');
	}

	public static function pluginUninstall() {
		if (!current_user_can('activate_plugins')) {
			return;
		}

		global $locator;
		$locator->piwik->trackUninstall();
	}

	/**
	 * Initiate WPHooks
	 */
	public function initHooks() {
		$this->initiated = true;

		add_filter('mce_external_plugins', array($this, 'tinymcePlugins'));

		add_action('admin_init', array($this, 'adminInit'));
		add_action('admin_menu', array($this, 'adminMenu'), 4);
		add_action('admin_notices', array($this, 'displayNotice'));

		add_action('admin_enqueue_scripts', array($this, 'loadResources'));
	}

	/**
	 * Register resources
	 */
	public function loadResources() {

		remove_action('admin_print_styles', 'print_emoji_styles');
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
		remove_filter('the_content_feed', 'wp_staticize_emoji');
		remove_filter('comment_text_rss', 'wp_staticize_emoji');

		wp_register_script('dismiss.js', $this->config['videePluginUrl'] . '_inc/libs/dismiss_verify.js', array('jquery'), $this->config['videeVersion']);
		wp_enqueue_script('dismiss.js');

		$params['settingsUrl'] = $this->getPageUrl('settings');
		$params['postUrl'] = $this->getPageUrl('config');
		wp_localize_script('dismiss.js', 'params', $params);

		//wp_register_script('init.track', $this->config['videePluginUrl'] . '_inc/libs/init.track.js', array(), $this->config['videeVersion']);
		//wp_enqueue_script('init.track');
		//
        //wp_register_script('track.js', 'https://d2zah9y47r7bi2.cloudfront.net/releases/current/tracker.js', array('init.track'), $this->config['videeVersion']);
		//wp_enqueue_script('track.js');

		wp_register_style('datepicker.css', $this->config['videePluginUrl'] . '_inc/css/datepicker.css', array(), $this->config['videeVersion']);
		wp_enqueue_style('datepicker.css');

		wp_register_script('datepicker', $this->config['videePluginUrl'] . '_inc/libs/jquery.pickmeup.min.js', array('jquery'), $this->config['videeVersion']);
		wp_enqueue_script('datepicker');

		wp_register_style('select2.css', $this->config['videePluginUrl'] . '_inc/css/select2.min.css', array(), $this->config['videeVersion']);
		wp_enqueue_style('select2.css');

		wp_register_script('select2', $this->config['videePluginUrl'] . '_inc/libs/select2.min.js', array('jquery'), $this->config['videeVersion']);
		wp_enqueue_script('select2');

		wp_register_script('knockout-3.4.0.js', $this->config['videePluginUrl'] . '_inc/libs/knockout-3.4.0.js', array(), $this->config['videeVersion']);
		wp_enqueue_script('knockout-3.4.0.js');

		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-droppable');

		wp_register_style('styles.css', $this->config['videePluginUrl'] . '_inc/css/constructor/styles.css', array(), $this->config['videeVersion']);
		wp_enqueue_style('styles.css');

		wp_register_style('widget.css', $this->config['videePluginUrl'] . '_inc/plugin/widget.css', array(), $this->config['videeVersion']);
		wp_enqueue_style('widget.css');

		wp_register_script('rangeslider.min.js', $this->config['videePluginUrl'] . '_inc/libs/rangeslider.min.js', array(), $this->config['videeVersion']);
		wp_enqueue_script('rangeslider.min.js');

		wp_register_script('videeConfig.js', $this->config['videePluginUrl'] . '_inc/plugin/config.js', array('jquery'), $this->config['videeVersion']);
		wp_enqueue_script('videeConfig.js');


		$params['videeToken'] = $this->locator->getOption('token');
		$params['videeUserId'] = $this->locator->getOption('userId');
		$params['videePluginUrl'] = $this->config['videePluginUrl'];
		$params['videeApiUrl'] = $this->config['videeApiUrl'];
		wp_localize_script('videeConfig.js', 'defaultOptions', $params);


		wp_register_script('utils.js', $this->config['videePluginUrl'] . '_inc/plugin/utils.js', array('videeConfig.js'), $this->config['videeVersion']);
		wp_enqueue_script('utils.js');

		wp_register_script('videosViewModel.js', $this->config['videePluginUrl'] . '_inc/plugin/videosViewModel.js', array('videeConfig.js'), $this->config['videeVersion']);
		wp_enqueue_script('videosViewModel.js');

		wp_register_script('playlistsViewModel.js', $this->config['videePluginUrl'] . '_inc/plugin/playlistsViewModel.js', array('videeConfig.js'), $this->config['videeVersion']);
		wp_enqueue_script('playlistsViewModel.js');

		wp_register_script('ko.widget.js', $this->config['videePluginUrl'] . '_inc/plugin/ko.widget.js', array('videeConfig.js'), $this->config['videeVersion']);
		wp_enqueue_script('ko.widget.js');

		wp_register_script('ko.modal.widget.js', $this->config['videePluginUrl'] . '_inc/plugin/ko.modal.widget.js', array('videeConfig.js'), $this->config['videeVersion']);
		wp_enqueue_script('ko.modal.widget.js');
	}

	public function tinymcePlugins($plugins) {
		$plugins['editvidee'] = $this->config['videePluginUrl'] . '_inc/plugin/tinymce.editvidee.js';
		$plugins['sctovidee'] = $this->config['videePluginUrl'] . '_inc/plugin/tinymce.sctovidee.js';

		return $plugins;
	}

	/**
	 * Init Videe Admin
	 */
	public function adminInit() {
		load_plugin_textdomain('videe');
		add_action('add_meta_boxes', array($this, 'addMetaBoxes'));
	}

	public function addMetaBoxes() {
		add_meta_box('wpt_events_location', 'Videe.TV', array($this, 'loadPostSettingsBox'), 'post', 'side', 'high');
		add_meta_box('wpt_events_location', 'Videe.TV', array($this, 'loadPostSettingsBox'), 'page', 'side', 'high');
	}

	public function loadPostSettingsBox() {
		$this->view('postwidget');
	}

	/**
	 * Init Videe Admin Menu
	 */
	public function adminMenu() {
		$hook = add_menu_page(__('Videe.TV', 'videe'), __('Videe.TV', 'videe'), 'manage_options', 'videe-key-config', array($this, 'displayPage'));
		//add_submenu_page('videe-key-config', 'How to Use',        'How to Use',        'manage_options', 'videe-key-config#tab=howto',            array($this, 'displayPage'));
		add_submenu_page('videe-key-config', 'Standard Library', 'Standard Library', 'manage_options', 'videe-key-config#tab=library', array($this, 'displayPage'));
		add_submenu_page('videe-key-config', 'Custom Library', 'Custom Library', 'manage_options', 'videe-key-config#tab=my_library', array($this, 'displayPage'));
		add_submenu_page('videe-key-config', 'Premium Library', 'Premium Library', 'manage_options', 'videe-key-config#tab=premium_library', array($this, 'displayPage'));
		add_submenu_page('videe-key-config', 'Playlists', 'Playlists', 'manage_options', 'videe-key-config#tab=playlists', array($this, 'displayPage'));
		add_submenu_page('videe-key-config', 'Statistics', 'Statistics', 'manage_options', 'videe-key-config#tab=statistics', array($this, 'displayPage'));
		add_submenu_page('videe-key-config', 'Payouts', 'Payouts', 'manage_options', 'videe-key-config#tab=payouts', array($this, 'displayPage'));
		add_submenu_page('videe-key-config', 'Contact Us', 'Contact Us', 'manage_options', 'videe-key-config#tab=contact_	us', array($this, 'displayPage'));
		add_submenu_page('videe-key-config', 'Help', 'Help', 'manage_options', 'videe-key-config#tab=help', array($this, 'displayPage'));
		add_submenu_page('videe-key-config', 'Account Settings', 'Account Settings', 'manage_options', 'account-settings', array($this, 'initAccountSettingsPage'));
		remove_submenu_page('videe-key-config', 'videe-key-config');
	}

	public function initAccountSettingsPage() {
		$this->locator->piwik->trackChangeTab('settings');
		$this->view('account-settings');
	}

	public function displayPage() {
		$parameter = $_SERVER['QUERY_STRING'];
		// track page change
		if (preg_match('/tab=(?<tab>[^&]+?)/Uis', $parameter, $matched)) {
			$this->locator->piwik->trackChangeTab($matched['tab']);
		}

		$this->view('content');
	}

	/**
	 * Display videe notices
	 */
	public function displayNotice() {

		global $hook_suffix;

		$notices = array();


		if ((isset($_GET['page']) && $_GET['page'] === 'videe-key-config') 
			|| $hook_suffix == 'plugins.php') {

			$error = $this->checkPluginCompatibility();

			if ($error) {
				$notices[] = array('type' => 'compatibility', 'error' => $error['msg']);
			}

			if (!$this->locator->getOption('verified') 
				&& !$this->locator->getOption('dismissVerifyNotice') 
				&& !$this->locator->getOption('manualRegistration')) {
				$notices[] = array('type' => 'verify');
			}

			if (($this->locator->getOption('verified') 
				|| ($this->locator->getOption('manualRegistration') && $this->locator->getOption('userId')))
				&& !$this->locator->user->getBillingPaypalInfo()) {
				$notices[] = array('type' => 'billing');
			}
		}

		if ((isset($_GET['page']) && $_GET['page'] === 'videe-key-config') 
			|| in_array($hook_suffix, array('post.php', 'post-new.php'))) {

			if ($this->newPluginVersionAvailable()) {
				$notices[] = array('type' => 'new_version');
			}
		}

		if (count($notices) > 0) {
			$this->view('notice', array('notices' => $notices));
		}
	}

	public function newPluginVersionAvailable() {

		$version = $this->getVersionFromWordpress();

		if ($version && version_compare($this->config['videeVersion'], $version, '<')) {
			return true;
		} else {
			return false;
		}
	}

	public function getVersionFromWordpress() {

		$url = sprintf('https://api.wordpress.org/plugins/info/1.0/%s.json', $this->config['pluginSlugName']);

		try {

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			$response = curl_exec($ch);

			curl_close($ch);

			$data = json_decode($response, true);
			if (!array_key_exists('version', $data)) {
				throw new UnexpectedValueException('Can\'t retrive plugin version');
			}

			$version = (float) $data['version'];
			return $version;
		} catch (Exception $e) {
			return null;
		}
	}

	/**
	 * Generate URL for a page
	 *
	 * @param string $page
	 * @return string
	 */
	public function getPageUrl($page = 'config', $ajax = false) {
		$args = array('page' => 'videe-key-config');

		switch ($page) {
			case 'delete_key':
				$args = array('page' => 'videe-key-config',
					'view' => 'start',
					'action' => 'delete-key',
					'_wpnonce' => wp_create_nonce(self::NONCE));
				break;
			case 'settings':
				$args = array('page' => 'account-settings');
				break;
			case 'registration':
				$redirectUri = urlencode($_SERVER['REQUEST_URI']);
				$args = array(
					'page' => 'videe-key-config#tab=library',
					'modal' => 'true'
				);
				break;
			case 'login':
				$redirectUri = urlencode($_SERVER['REQUEST_URI']);
				$args = array(
					'page' => 'videe-key-config#tab=library',
					'modal' => 'true',
					'redirect' => $redirectUri
				);
				break;
			case 'tutorial':
				$redirectUri = urlencode($_SERVER['REQUEST_URI']);
				$args = array(
					'page' => 'videe-key-config#tab=library',
					'tutorial' => 'true'
				);
				break;
			case 'verify':
				$redirectUri = urlencode($_SERVER['REQUEST_URI']);
				$args = array(
					'page' => 'videe-key-config#tab=library',
					'verify' => 'true'
				);
				break;
			case 'upload':
				$args = array('page' => 'videe-key-config#tab=my_library',
					'modal' => 'true');
				break;
			case 'help':
				$args = array('page' => 'videe-key-config#tab=help');
				break;
			case 'playlist':
				$args = array('page' => 'videe-key-config#tab=library');
				break;
			default:
				break;
		}

		if ($ajax) {
			$args['action'] = $args['page'];
			unset($args['page']);
		}

		$url = add_query_arg($args, admin_url('admin' . ($ajax ? '-ajax' : '') . '.php'));

		return $url;
	}

	/**
	 * Add help to the Videe page
	 *
	 * @return false if not the Videe page
	 */
	public function adminHelp() {
		$current_screen = get_current_screen();
	}

	/**
	 * Includes required view
	 *
	 * @param string $name
	 * @param array $args
	 */
	public function view($name, array $args = array()) {
		$args = apply_filters('videe_view_arguments', $args, $name);

		extract($args);

		load_plugin_textdomain('videe');

		$file = $this->config['videePluginDir'] . 'views/' . $name . '.php';

		include($file);
	}

}