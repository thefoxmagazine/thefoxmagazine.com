<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

class KCF_Options {

	const OPTION_KEY = 'kcf_chiliforms_options';

	public function __construct() {
		self::register();
	}

	public static function register() {
		add_option( self::OPTION_KEY, '{"version": "' . KCF_VERSION . '"}');
	}

	public static function save($options) {
		update_option(self::OPTION_KEY, json_encode($options));
	}

	public static function get() {
		global $kcf_chiliforms_options;

		if (!$kcf_chiliforms_options) {
			$kcf_chiliforms_options = json_decode(get_option(self::OPTION_KEY), true);
		}

		return $kcf_chiliforms_options;
	}

	public static function option($key) {
		$all_options = self::get();

		if (array_key_exists($key, $all_options)) {
			return $all_options[$key];
		} else {
			return null;
		}
	}
}