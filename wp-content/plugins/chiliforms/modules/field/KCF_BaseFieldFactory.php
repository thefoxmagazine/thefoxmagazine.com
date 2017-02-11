<?php

/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */
class KCF_BaseFieldFactory {

	/**
	 * These IDs are used to store field types in DB
	 */
	public static $field_type_ids = array(
		'SINGLE_LINE_INPUT' => 1,
		'MULTI_LINE_INPUT'  => 2,
		'DROPDOWN'          => 3,
		'CHECKBOX'          => 4,
		'RADIO'             => 5,
		'EMAIL'             => 6,
		'URL'               => 7,
		'NUMBER'            => 8,
		'HIDDEN'            => 9,
		'HTML'              => 10,
	);

	protected $field_types = null;

	public function __construct() {
		$this->initialize();
	}

	protected function initialize() {
		$this->init_field_types();
	}

	protected function init_field_types() {
		$this->field_types = array(
			array(
				'type_id'     => self::$field_type_ids['SINGLE_LINE_INPUT'],
				'type_slug'        => 'single-line-input',
				'type'        => 'Text',
				'placeholder' => 'Enter your name',
				'label'       => 'Your name'
			),
			array(
				'type_id'     => self::$field_type_ids['MULTI_LINE_INPUT'],
				'type_slug'        => 'multi-line-input',
				'type'        => 'Textarea',
				'placeholder' => 'Type your message here',
				'label'       => 'Message'
			),
			array(
				'type_id'     => self::$field_type_ids['DROPDOWN'],
				'type_slug'        => 'dropdown',
				'type'        => 'Dropdown',
				'placeholder' => 'Select option',
				'label'       => 'Select one option',
				'options'     => array(
					array(
						'value' => 'option1',
						'text'  => 'Option 1'
					),
					array(
						'value' => 'option2',
						'text'  => 'Option 2'
					),
				)
			),
			array(
				'type_id' => self::$field_type_ids['CHECKBOX'],
				'type_slug'    => 'checkbox',
				'type'    => 'Checkbox',
				'label'   => 'Select options',
				'options' => array(
					array(
						'value' => 'option1',
						'text'  => 'Option 1'
					),
					array(
						'value' => 'option2',
						'text'  => 'Option 2'
					),
					array(
						'value' => 'option3',
						'text'  => 'Option 3'
					),
				)
			),
			array(
				'type_id' => self::$field_type_ids['RADIO'],
				'type_slug'    => 'radio',
				'type'    => 'Radio',
				'label'   => 'Select one option',
				'options' => array(
					array(
						'value' => 'option1',
						'text'  => 'Option 1'
					),
					array(
						'value' => 'option2',
						'text'  => 'Option 2'
					),
					array(
						'value' => 'option3',
						'text'  => 'Option 3'
					),
				)
			),
			array(
				'type_id'     => self::$field_type_ids['EMAIL'],
				'type_slug'        => 'email',
				'type'        => 'Email',
				'placeholder' => 'test@server.com',
				'label'       => 'Enter your email'
			),
			array(
				'type_id'     => self::$field_type_ids['URL'],
				'type_slug'        => 'url',
				'type'        => 'URL',
				'placeholder' => 'www.mysite.org',
				'label'       => 'Enter your website'
			),
			array(
				'type_id'     => self::$field_type_ids['NUMBER'],
				'type_slug'        => 'number',
				'type'        => 'Number',
				'placeholder' => '1',
				'label'       => 'Number of tables',
				'min'         => 1,
				'max'         => 12
			),
			array(
				'type_id' => self::$field_type_ids['HIDDEN'],
				'type_slug'    => 'hidden',
				'type'    => 'Hidden',
				'value'   => 'hidden-value',
			),
			array(
				'type_id' => self::$field_type_ids['HTML'],
				'type_slug'    => 'html',
				'type'    => 'HTML',
				'value'   => 'Enter some <strong>HTML</strong> here',
				'label'   => 'Custom HTML block'
			)
		);
	}

	/**
	 * @param $type_id
	 *
	 * @return bool
	 */
	final public function get_type_name( $type_id ) {
		$type_data = $this->get_type_data_by_id($type_id);

		return $type_data && $type_data['type'] ? $type_data['type'] : 'Unknown';
	}

	/**
	 * @param $type_id
	 *
	 * @return bool
	 */
	final public function get_type_slug( $type_id ) {
		$type_data = $this->get_type_data_by_id($type_id);

		return $type_data && $type_data['type_slug'] ? $type_data['type_slug'] : 'unknown';
	}

	/**
	 * @param $type_id
	 *
	 * @return null
	 */
	final protected function get_type_data_by_id($type_id) {
		foreach ( $this->field_types as $type ) {
			if ( $type['type_id'] === $type_id ) {
				return $type;
			}
		}

		return null;
	}

	/**
	 * @param $type_id
	 * @param $type
	 *
	 * @return bool
	 */
	final public function is_of_type($type_id, $type) {
		if (array_key_exists($type, self::$field_type_ids)) {
			return self::$field_type_ids[$type] === $type_id;
		} else {
			return false;
		}
	}

	/**
	 * @return null
	 */
	protected function _get_all_field_types() {
		return $this->field_types;
	}

	/**
	 *
	 */
	public function get_field_types() {
		// to be overridden
	}
}