<?php

if (!function_exists('add_action')) {
	die('Unauthorized access.');
}


require_once  plugin_dir_path(__FILE__) . 'libs/piwik.php';

class Videe_Piwik extends Videe_Abstract
{

	/**
	 * Piwik actions list
	 */
	const PLUGIN_ACTIVATED = 'PLUGIN_ACTIVATED';
	const WORDPRESS_VERSION = 'WORDPRESS_VERSION';
	const FAILED_ACTIVATION = 'FAILED_ACTIVATION';
	const PLUGIN_DEACTIVATED = 'PLUGIN_DEACTIVATED';
	const PLUGIN_USAGE_PERIOD = 'PLUGIN_USAGE_PERIOD';
	const PLUGIN_UNINSTALLED = 'PLUGIN_UNINSTALLED';
	const CHANGE_TAB = 'CHANGE_TAB';
	const MONETIZATION_ACTIVATED = 'MONETIZATION_ACTIVATED';
	const MONETIZATION_DEACTIVATED = 'MONETIZATION_DEACTIVATED';
	const USER_LOGGING_IN = 'USER_LOGGING_IN';
	const USER_LOGGING_OUT = 'USER_LOGGING_OUT';
	const VIDEE_VIDEO_ADDED = 'VIDEE_VIDEO_ADDED';
	const WIDGET_VIDEE_VIDEO_ADDED = 'WIDGET_VIDEE_VIDEO_ADDED';
	const WORDPRESS_VIDEO_ADDED = 'WORDPRESS_VIDEO_ADDED';
	const PLUGIN_DEACTIVATED_AFTER_LOG_IN = 'PLUGIN_DEACTIVATED_AFTER_LOG_IN';

	static private $options = array(
		'pluginActivationTime' => 'videe_plugin_activation_time',
		'videeVideoAdded' => 'videe_video_added',
		'wordpressVideoAdded' => 'wordpress_video_added',
		'widgetVideeVideoAdded' => 'widget_videe_video_added',
	);
	
	public function setServiceLocator($locator) {
		parent::setServiceLocator($locator);
		$this->initTracker();
	}
	
	protected function initTracker()
	{
		$this->tracker = new PiwikTracker($this->config['piwikSiteId'], 
			$this->config['piwikApiUrl']);
	}


	public function trackVideeVideoAdded() {

		if (add_option(self::$options['videeVideoAdded'], true)) {
			$this->trackEvent(array(self::VIDEE_VIDEO_ADDED));
		}
	}

	public function trackWordpressVideoAdded() {

		if (add_option(self::$options['wordpressVideoAdded'], true)) {
			$this->trackEvent(array(self::WORDPRESS_VIDEO_ADDED));
		}
	}
	
	public function trackWidgetVideeVideoAdded() {

		if (add_option(self::$options['widgetVideeVideoAdded'], true)) {
			$this->trackEvent(array(self::WIDGET_VIDEE_VIDEO_ADDED));
		}
	}
	
	public function trackActivation() {
		$this->trackEvent(array(self::PLUGIN_ACTIVATED));
		$this->locator->setOption('pluginActivationTime', time());

		if (isset($GLOBALS['wp_version'])) {
			$this->trackEvent(array(self::WORDPRESS_VERSION, $GLOBALS['wp_version']));
		}
	}

	public function trackActivationError($error = '') {
		$this->trackEvent(array(self::FAILED_ACTIVATION, $error));
	}

	public function trackDeactivation() {
		$activationTime = get_option(self::$options['pluginActivationTime'], false);
		if ($activationTime) {
			$period = $this->period($activationTime, time());
			$this->trackEvent(array(self::PLUGIN_USAGE_PERIOD, $period));
		}

		foreach (self::$options as $option) {
			delete_option($option);
		}

		if ($this->locator->getOption('userId')) {
			$this->trackEvent(array(self::PLUGIN_DEACTIVATED_AFTER_LOG_IN));
		}


		$this->trackEvent(array(self::PLUGIN_DEACTIVATED));
	}

	private function period($start, $end) {

		$diff = $end - $start;
		
		$days = floor($diff / 86400);
		$hours = floor(($diff - ($days * 86400)) / 3600);
		$minutes = floor(($diff - ($days * 86400) - ($hours * 3600)) / 60);
		$seconds = floor(($diff - ($days * 86400) - ($hours * 3600) - ($minutes * 60)));

		return sprintf('%d days %02d:%02d:%02d', $days, $hours, $minutes, $seconds);
	}

	public function trackUninstall() {
		$this->trackEvent(array(self::PLUGIN_UNINSTALLED));
	}

	public function trackChangeTab($tab) {
		$this->trackEvent(array(self::CHANGE_TAB, $tab));
	}

	public function trackMonetizationActivated() {
		$this->trackEvent(array(self::MONETIZATION_ACTIVATED));
	}

	public function trackMonetizationDeactivated() {
		$this->trackEvent(array(self::MONETIZATION_DEACTIVATED));
	}
	
	public function trackLogInOut() {

		$token = $this->locator->getOption('token');

		if (!empty($token)) {
			$this->trackEvent(array(self::USER_LOGGING_IN));
		} else {
			$this->trackEvent(array(self::USER_LOGGING_OUT));
		}
	}

	public function trackEvent($event) {

		$userId = $_SERVER['SERVER_NAME'];

		if (strpos($userId, '~^(') !== false) {
			$refferer = $_SERVER["HTTP_REFERER"];
			$parsedUrl = parse_url($refferer);
			$userId = $parsedUrl['host'];
		}

		$forbidden = array( '.*\.loc$', '.*locahost.*', '.*videe.*', '~\^', '.*soldatova.*');

		$forbiddenFound = preg_match("/(" . implode($forbidden, "|") . ")/i", $userId);

		if ($forbiddenFound) {
			return;
		}

		$category = 'PLUGIN_' . $this->config['videeVersion'];

		foreach (array('action', 'name', 'value') as $param) {
			${$param} = ($element = array_shift($event)) ? $element : false;
		}

		$this->tracker->setUserId($userId);
		$this->tracker->doTrackEvent($category, $action, $name, $value);
	}

}