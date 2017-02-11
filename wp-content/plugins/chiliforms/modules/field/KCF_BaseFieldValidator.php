<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

abstract class KCF_BaseFieldValidator {
	public static function validate($form, $data) {
		$_self = __CLASS__;

		$errors = array('global' => array(), 'fields' => array(), 'recaptcha' => array());

		$fields = $form->fields;
		$sent_fields = $data["values"];

		$fields_by_id = array_reduce($fields, function($result, $field) {
			$result[$field->id] = $field;

			return $result;
		}, array());

		$errors['fields'] = array_reduce($sent_fields, function($errors, $field) use ($fields_by_id, $_self) {
			$id = $field["id"];
			$value = array_key_exists("value", $field) ? $field["value"] : null;

			$field_config = $fields_by_id[$id];
			$field_type = $field_config->type_slug;

			$options = array_reduce($field_config->options, function($result, $option) {
				$keys = array_keys($option);
				$key = $keys[0];

				$result[$key] = $option[$key];

				return $result;
			}, array());

			$is_required = array_key_exists( "required", $options ) &&
			               filter_var( $options["required"], FILTER_VALIDATE_BOOLEAN ) === true;

			$has_custom_validation = array_key_exists( "field_validation_type", $options ) &&
			                  $options["field_validation_type"] !== "none";

			if ($is_required && !$_self::validate_required($value, $field_type)) {
				$errors[]= array('id' => $id, 'error_message' => "This field is required");
			} else if ($_self::has_field_type_validation($field_type)) {
				$result = $_self::validate_by_field_type($value, $field_type);

				if ($result !== true) {
					$errors[]= array('id' => $id, 'error_message' => $result["error_message"]);
				}
			} else if ($has_custom_validation) {
				$result = $_self::validate_by_custom_validation($value, $options["field_validation_type"]);

				if ($result !== true) {
					$errors[]= array('id' => $id, 'error_message' => $result["error_message"]);
				}
			}

			return $errors;
		}, $errors['fields']);

		$recaptcha_validate = self::validate_recaptcha($form, $data);

		if ($recaptcha_validate !== true) {
			array_push($errors['recaptcha'], $recaptcha_validate);
		}

		return $errors;
	}

	protected static function validate_recaptcha($form, $data) {
		if (!KCF_Options::option('recaptcha_public_key') || !KCF_Options::option('recaptcha_secret_key')) {
			// reCAPTCHA not configured, validation not possible
			return true;
		}

		$form_settings = $form->config["settings"];

		if (!array_key_exists("form_recaptcha_on", $form_settings) ||
		    filter_var($form_settings["form_recaptcha_on"], FILTER_VALIDATE_BOOLEAN) === false) {
			// reCAPTCHA configured, but not enabled for this form
			return true;
		}

		if (!array_key_exists('recaptchaResult', $data) || !$data['recaptchaResult']) {
			// reCAPTCHA is configured and enabled, but was not received from FE
			return array('error_message' => 'No reCAPTCHA user response received for validation');
		}

		$recaptcha_client_result = $data['recaptchaResult'];

		$response = wp_remote_post("https://www.google.com/recaptcha/api/siteverify", array(
				'method' => 'POST',

				'body' => array(
					'secret' => KCF_Options::option('recaptcha_secret_key'),
					'response' => $recaptcha_client_result
				)
			)
		);

		if ( is_wp_error( $response ) ) {
			// couldn't connect to reCAPTCHA verification endpoint, validation not possible
			return array('error_message' => 'Couldn\'t connect reCAPTCHA validation service');
		} else {
			// reCAPTCHA response received, let's parse it
			try {
				$recaptcha_response = json_decode($response['body'], true);

				if ($recaptcha_response['success'] === true) {
					return true;
				}

			} catch (Exception $e) {
				return array('error_message' => 'Couldn\'t parse reCAPTCHA validation response');
			}
		}

		return array('error_message' => 'Couldn\'t validate reCAPTCHA');
	}

	protected static function validate_required($value, $field_type) {
		switch ($field_type) {
			case "checkbox":
				return count(array_filter($value, function($option) {
					return filter_var($option["value"], FILTER_VALIDATE_BOOLEAN) === true;
				})) > 0;

			case "dropdown":
			case "radio":
				return $value['key'] !== "" || $value['label'] !== '';
				break;

			case "single-line-input":
			case "multi-line-input":
			case "url":
			case "email":
				return isset($value) && trim($value) !== "" ? true : false;
		}

		return false;
	}

	protected static function validate_by_custom_validation($value, $validation_type) {
		switch ($validation_type) {
			case "alpha":
				return self::is_alpha($value) ? true : array(
					"error_message" => 'Only characters allowed'
				);

			case "num":
				return self::is_num($value) ? true : array(
					"error_message" => 'Only numbers allowed'
				);

			case "alphanum":
				return self::is_alphanum($value) ? true : array(
					"error_message" => 'Only numbers and characters allowed'
				);

		}

		return false;
	}

	protected static function has_field_type_validation($field_type) {
		switch ($field_type) {
			case "email":
			case "url":
				return true;

			default:
				return false;
		}
	}

	protected static function validate_by_field_type($value, $field_type) {
		switch ( $field_type ) {
			case "email":
				return self::is_email( $value ) ? true : array(
					"error_message" => "Please enter a valid email"
				);

			case "url":
				return self::is_url( $value ) ? true : array(
					"error_message" => "Please enter a valid url"
				);

		}

		return true;
	}

	protected static function is_email($value) {
		return trim($value) !== "" ? filter_var($value, FILTER_VALIDATE_EMAIL) : true;
	}

	protected static function is_url($value) {
		return trim($value) !== "" ?
			preg_match('/^(http(s)?(:\/\/))?(www\.)?[a-zA-Z0-9-_\.]+(\.[a-zA-Z0-9]{2,})([-a-zA-Z0-9:%_\+.~#?&\/\/=]*)/',
				$value) : true;
	}

	protected static function is_alpha($value) {
		return preg_match('/^[a-zA-Z]*$/', $value);
	}

	protected static function is_num($value) {
		return preg_match('/^[0-9]*$/', $value);
	}

	protected static function is_alphanum($value) {
		return preg_match('/^[a-zA-Z0-9]*$/', $value);
	}
}