<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

class KCF_FieldFactory extends KCF_BaseFieldFactory {

	protected $allowed_field_ids = null;

	protected function set_allowed_field_ids() {
		$this->allowed_field_ids = array(
			self::$field_type_ids['SINGLE_LINE_INPUT'],
			self::$field_type_ids['MULTI_LINE_INPUT'],
			self::$field_type_ids['DROPDOWN'],
			self::$field_type_ids['CHECKBOX'],
			self::$field_type_ids['RADIO'],
			self::$field_type_ids['EMAIL'],
			self::$field_type_ids['URL']
		);
	}

	public function __construct() {
		parent::__construct();

		$this->set_allowed_field_ids();
	}

	protected function allowed_fields_filter($field) {
		return in_array($field['type_id'], $this->allowed_field_ids);
	}

	public function get_field_types() {
		return array_filter($this->_get_all_field_types(), array($this, 'allowed_fields_filter'));
	}
}