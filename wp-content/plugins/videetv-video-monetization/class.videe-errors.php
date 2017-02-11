<?php

if (!function_exists('add_action')) {
	die('Unauthorized access.');
}

class Videe_Errors {

	const ERROR_CANT_WRITE_TO_FILE = 1;
	const ERROR_DOMAIN_UNAVAILABLE_OUTSIDE = 2;
	const ERROR_CANT_CREATE_FOLDER = 3;

	private $errors;
	public static $errorMessages = array(
		self::ERROR_CANT_WRITE_TO_FILE =>
		'Unable to activate plugin. <br/> Please set correct permission and owner on folder %(folder)s.',
		self::ERROR_CANT_CREATE_FOLDER =>
		'Unable to activate plugin. <br/> Please create folder %(folder)s and set correct permission and owner on this folder. <br/> For more details please contact support@videe.tv.',
		self::ERROR_DOMAIN_UNAVAILABLE_OUTSIDE =>
		'Unable to activate plugin.<br/> Your website should be pointed to an external IP address of the server and not be hosted locally on your PC/laptop. In other words you should setup WordPress to your hosting server and link with a certain domain name.<br/> For more details please contact support@videe.tv.'
	);

	public function addError($error, $params = array()) {
		if (isset(self::$errorMessages[$error])) {
			$this->errors[] = $this->vsprintfNamed(self::$errorMessages[$error], $params);
		}
	}

	public function hasErrors() {
		return (boolean) count($this->errors);
	}

	public function getErrors() {
		return $this->errors;
	}

	public function vsprintfNamed($format, $args) {
		$names = preg_match_all('/%\((.*?)\)/', $format, $matches, PREG_SET_ORDER);

		$values = array();
		foreach ($matches as $match) {
			$values[] = $args[$match[1]];
		}

		$format = preg_replace('/%\((.*?)\)/', '%', $format);
		return vsprintf($format, $values);
	}

}
