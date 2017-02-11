<?php

if (!function_exists('add_action')) {
	die('Unauthorized access.');
}

class Videe_Options 
{	
	protected static $options = array(
		'token' => 'videe_token',
		'userId' => 'videe_user',
		'aid' => 'videe_aid',
		'waitVerify' => 'videe_waitverify',
		'verified' => 'videe_verified',
		'email' => 'videe_email',
		'dismissVerifyNotice' => 'videe_dismiss',
		'enableMonetization' => 'setting_enable_monetization',
		'activationRedirect' => 'videe_activation_redirect',
		'manualRegistration' => 'videe_manual_registration',
		'settingEnableMonetization' => 'setting_enable_monetization',
		'settingSubstitutePlayer' => 'setting_substitute_player_setting',
		'settingEnableMonetization' => 'setting_enable_monetization'
	);
	
	protected static $defaultOptionValues = array(
		'waitVerify' => 0,
		'verified' => 0,
		'settingSubstitutePlayer' => 0,
		'enableMonetization' => 0,
		'manualRegistration' => 0
	);
	
	protected $cachedOptoins = array();
	
	public static function getDbOption($name)
	{
		return self::$options[$name];
	}
	
	public static function getDbOptions()
	{
		return self::$options;
	}

	public function __set($name, $value) 
	{	
		if (isset(self::$options[$name])) {
			update_option(self::$options[$name], $value);
			self::$options[self::$options[$name]] = $value;
		}
	}
	
	public function __get($name) 
	{
		if (isset(self::$options[$name])) {
			$optionName = self::$options[$name];
			$defaultValue = isset(self::$defaultOptionValues[$name]) ? 
				self::$defaultOptionValues[$name]: null;
			
			return isset($this->cachedOptoins[self::$options[$name]]) ?
				$this->cachedOptoins[self::$options[$name]] : get_option($optionName, $defaultValue);
		}
		
		return false;
	}

	public function delete($name)
	{
		if (isset(self::$options[$name])) {
			delete_option(self::$options[$name]);
		}
	}

}