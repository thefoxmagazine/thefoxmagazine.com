<?php

if (!function_exists('add_action')) {
	die('Unauthorized access.');
}

/**
 * Class Videe_User
 */
class Videe_User extends Videe_Abstract
{

	const API_END_POINT_BILLING = 'paymentsinfo';
	const API_END_POINT_VERIFY = 'publishers/verifyWordPressAccount';
	const API_END_POINT_REGISTER = 'publishers/oneClickRegistration';
	const API_END_POINT_USERINFO = 'users/isLoggedIn';

	const PARAM_DOMAIN = 'domain';
	const PARAM_VERIFY_FILE_URL = 'verifyFile';
	const PARAM_VERIFY_FILE_PATH = 'verifyFilePath';
	const TMP_FOLDER = 'verify';
	
	public $errors;

	public function setServiceLocator($locator) {
		parent::setServiceLocator($locator);
		$this->errors = $this->locator->errors;
	}

	public function init() {

		$this->checkVerified();

		if (isset($_POST['action']) && $_POST['action'] == 'enter-key') {
			$this->saveLoginParams($_POST['userId'], $_POST['token']);
		}

		if (isset($_POST['action']) && $_POST['action'] == 'waitVerify') {
			$this->waitVerify($_POST['email']);
		}

		if (isset($_POST['action']) && $_POST['action'] == 'dismissVerifyNotice') {
			$this->dismissVerifyNotice();
		}
	}

	private function checkVerified() {

		if ($this->locator->getOption('verified')) {
			return;
		}

		$response = $this->getUserInfo();

		if (isset($response['items']['verified_wordpress_publisher']) 
			&& $response['items']['verified_wordpress_publisher'] === true) {
			$this->locator->setOption('verified', 1);
		}
	}

	/**
	 * Save token action
	 */
	private function saveLoginParams($userId, $token) {

		$this->locator->setOption('userId', $userId);
		$this->locator->setOption('token', $token);

		$this->locator->piwik->trackLogInOut();

		$aid = $this->requestAid($token);
		$this->locator->setOption('aid', $aid);

		die(); //this is ajax
	}

	private function requestAid($token) {

		if (empty($token)) {
			return null;
		}

		$url = sprintf('%svideoaids?auth_token=%s', $this->config['videeApiUrl'], $token);

		try {

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			$response = curl_exec($ch);

			curl_close($ch);

			$data = json_decode($response, true);
			if (!isset($data['items']) || !is_array($data['items']) 
				|| count($data['items']) === 0) {
				throw new Exception('Can\'t retrive aid');
			}

			$aid = $data['items'][0]['aid'];
			return $aid;
		} catch (Exception $e) {
			return null;
		}
	}

	public function register() {

		try {
			$data = $this->getRegisterData();
		} catch (Exception $e) {
			return false;
		}

		$response = $this->registerRequest($data);

		if (isset($response['success']) && $response['success'] == true) {

			$item = $response['items'];

			$this->locator->setOption('aid', $item['aid']);
			$this->locator->setOption('token', $item['token']);
			$this->locator->setOption('userId', $item['user_id']);
			$verified = isset($item['verified']) && $item['verified'] == 'true' ? 1 : 0;
			$this->locator->setOption('verified', $verified);

			$this->deleteVerifyFile();

			return true;
		} else {
			$this->errors->addError(Videe_Errors::ERROR_DOMAIN_UNAVAILABLE_OUTSIDE);
			$this->deleteVerifyFile();
			return false;
		}
	}

	private function getRegisterData() {

		$domainName = $this->getHost();
		$fileName = $this->createTmpFile($domainName);

		return array(self::PARAM_VERIFY_FILE_URL => $this->getTmpFileUrl($fileName),
			self::PARAM_DOMAIN => $domainName);
	}

	private function getHost() {

		$host = $_SERVER['SERVER_NAME'];

		if (strpos($host, '~^(') !== false) {
			$refferer = $_SERVER["HTTP_REFERER"];
			$parsedUrl = parse_url($refferer);
			$host = $parsedUrl['host'];
		}

		return $host;
	}

	private function createTmpFile($domainName = null) {

		$domainName = !$domainName ? $this->getHost() : $domainName;

		$fileName = sprintf('videe%s.txt', uniqid());
		$filePath = $this->getTmpFilePath($fileName);
		$data = hash_hmac('sha256', $domainName, 'videe.tv');

		if (!file_put_contents($filePath, $data)) {
			$folder = dirname($filePath);
			$this->errors->addError(Videe_Errors::ERROR_CANT_WRITE_TO_FILE, array('folder' => $folder));
			throw new Exception('Can\'t write to file');
		}

		return $fileName;
	}

	private function getTmpFilePath($fileName) {

		$folder = $this->config['videePluginDir'] . self::TMP_FOLDER;

		if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
			$this->errors->addError(Videe_Errors::ERROR_CANT_CREATE_FOLDER, array('folder' => $folder));
			throw new Exception('Can\'t create folder');
		}

		return $folder . DIRECTORY_SEPARATOR . $fileName;
	}

	private function getTmpFileUrl($fileName) {
		return $this->config['videePluginUrl'] . self::TMP_FOLDER 
			. DIRECTORY_SEPARATOR . $fileName;
	}

	private function deleteVerifyFile() {

		$folder = $this->config['videePluginDir'] . self::TMP_FOLDER . '/*';

		$files = glob($folder); // get all file names
		foreach ($files as $file) { // iterate files
			if (is_file($file) && strpos($file, '.keep') === false)
				unlink($file);
		}
	}

	private function registerRequest($data) {

		try {

			$url = $this->config['videeApiUrl'] . self::API_END_POINT_REGISTER;

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

			$response = json_decode(curl_exec($ch), true);
			curl_close($ch);

			return $response;
		} catch (Exception $e) {
			return null;
		}
	}

	private function getUserInfo() {

		try {
			$url = $this->config['videeApiUrl'] . self::API_END_POINT_USERINFO 
				. '?auth_token=' . $this->locator->getOption('token');

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$response = json_decode(curl_exec($ch), true);
			curl_close($ch);

			return $response;
		} catch (Exception $e) {
			return null;
		}
	}

	private function waitVerify($email) {

		$this->locator->setOption('email', $email);
		$this->locator->setOption('waitVerify', 1);
		die();
	}

	private function dismissVerifyNotice() {
		$this->locator->setOption('dismissVerifyNotice', 1);
		die();
	}

	public function getBillingPaypalInfo() {
		try {

			$url = $this->config['videeApiUrl'] . self::API_END_POINT_BILLING 
				. '?auth_token=' . $this->locator->getOption('token');
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


			$data = json_decode(curl_exec($ch), true);

			curl_close($ch);
		} catch (Exception $e) {
			return false;
		}

		if (!isset($data['items'])) {
			return false;
		}


		foreach ($data['items'] as $item) {
			// if paypal email is set
			if ($item['payment_processor'] == 'paypal' && !empty($item['paypal_email'])) {
				return $item;
			}
		}

		return false;
	}

	public function setBillingPaypalInfo($email) {
		try {

			$url = $this->config['videeApiUrl'] . self::API_END_POINT_BILLING 
				. '?auth_token=' . $this->locator->getOption('token');

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_POST, 1);


			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");


			curl_setopt(
				$ch, CURLOPT_POSTFIELDS, array(
				'is_confirm' => 1,
				'is_default' => 1,
				'r' => '/api/' . self::API_END_POINT_BILLING,
				'payment_processor' => 'paypal',
				'paypal_email' => $email,
				'paypal_purpose_of_payment' => 'fun'
				)
			);

			$res = json_decode(curl_exec($ch), true);
			curl_close($ch);
		} catch (Exception $e) {
			return false;
		}

		return $res;
	}
}
