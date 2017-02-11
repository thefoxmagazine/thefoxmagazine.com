<?php

/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */
class KCF_DbModel {
	const PLUGIN_PREFIX = 'kcf_';
	const FORMS_TABLE_NAME = 'forms';
	const FIELDS_TABLE_NAME = 'fields';
	const ENTRIES_TABLE_NAME = 'entries';
	const ENTRIES_META_TABLE_NAME = 'entries_meta';

	/**
	 * @return string
	 */
	public static function get_forms_table_name() {
		return self::get_table_name_for( self::FORMS_TABLE_NAME );
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	private static function get_table_name_for( $name ) {
		global $wpdb;

		return $wpdb->prefix . self::PLUGIN_PREFIX . $name;
	}

	/**
	 * @return string
	 */
	private static function get_wp_charset_collate() {
		global $wpdb;
		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = " DEFAULT CHARACTER SET $wpdb->charset";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		return $charset_collate;
	}

	/**
	 * @return string
	 */
	private static function get_forms_structure() {
		$table_name = self::get_table_name_for( self::FORMS_TABLE_NAME );

		return "CREATE TABLE $table_name (
		      id int unsigned NOT NULL auto_increment,
		      name varchar(255) default '',
		      slug varchar(255) default '',
		      description varchar(255) default '',
		      created_at datetime NOT NULL,
		      modified_at datetime NOT NULL,
		      config blob default NULL,
		      is_active tinyint(1) default 1,
		      PRIMARY KEY  (id)
		    )";
	}

	/**
	 * @return string
	 */
	private static function get_fields_structure() {
		$table_name      = self::get_table_name_for( self::FIELDS_TABLE_NAME );

		return "CREATE TABLE $table_name (
		      id int unsigned NOT NULL AUTO_INCREMENT,
		      name varchar(255) default '',
		      slug varchar(255) default '',
		      type int NOT NULL,
		      field_order int NOT NULL,
		      created_at datetime NOT NULL,
		      modified_at datetime NOT NULL,
		      options longtext default NULL,
		      form_id int unsigned NOT NULL,
		      PRIMARY KEY  (id)
		    )";
	}

	/**
	 * @return string
	 */
	private static function get_entries_structure() {
		$table_name = self::get_table_name_for( self::ENTRIES_TABLE_NAME );

		return "CREATE TABLE $table_name (
		      id int unsigned NOT NULL AUTO_INCREMENT,
		      created_at datetime NOT NULL,
		      modified_at datetime NOT NULL,
		      info blob default NULL,
		      form_id int unsigned NOT NULL,
		      is_read tinyint(1) default 0,
		      is_starred tinyint(1) default 0,
		      PRIMARY KEY  (id)
		    )";
	}

	/**
	 * @return string
	 */
	private static function get_entries_meta_structure() {
		$table_name = self::get_table_name_for( self::ENTRIES_META_TABLE_NAME );

		return "CREATE TABLE $table_name (
		      id int unsigned NOT NULL AUTO_INCREMENT,
		      field_value longtext,
		      type int NOT NULL,
		      label varchar(255) default '',
		      form_id int unsigned NOT NULL,
		      field_id int unsigned NOT NULL,
		      entry_id int unsigned NOT NULL,
		      PRIMARY KEY  (id)
		    )";
	}

	/**
	 *
	 */
	public static function create_schema() {
		$wp_charset_collate = self::get_wp_charset_collate();
		$sql_postfix        = $wp_charset_collate . ';';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( self::get_forms_structure() . $sql_postfix );
		dbDelta( self::get_fields_structure() . $sql_postfix );
		dbDelta( self::get_entries_structure() . $sql_postfix );
		dbDelta( self::get_entries_meta_structure() . $sql_postfix );
	}

	/**
	 * @param null $form_id
	 *
	 * @return array|null|object
	 */
	protected static function get_forms_from_db( $form_id = null ) {
		if ( ! $form_id ) {
			return self::get_all_forms_from_db();
		} else {
			return self::get_form_by_id_from_db( (int) $form_id );
		}
	}

	/**
	 * @return array|null|object
	 */
	protected static function get_all_forms_from_db() {
		global $wpdb;

		$table_name = self::get_table_name_for( self::FORMS_TABLE_NAME );

		$results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );

		return $results;
	}

	/**
	 * @param $form_id
	 *
	 * @return array|null|object
	 */
	protected static function get_form_by_id_from_db( $form_id ) {
		global $wpdb;

		$table_name = self::get_table_name_for( self::FORMS_TABLE_NAME );

		$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE id=$form_id" );

		return $results;
	}

	/**
	 * @return mixed
	 */
	public static function get_all_forms() {
		return self::convert_db_forms_to_objects( self::get_forms_from_db() );
	}

	/**
	 * @param $form_id
	 *
	 * @return mixed
	 */
	public static function get_form_by_id( $form_id ) {
	    $result = null;

		$form_object_array = self::convert_db_forms_to_objects( self::get_forms_from_db( $form_id ) );

		if (!empty($form_object_array)) {
		    return $form_object_array[0];
		} else {
		    return null;
		}
	}

	/**
	 * @param $form_db_array
	 *
	 * @return mixed
	 */
	protected static function convert_db_forms_to_objects( $form_db_array ) {
		global $wpdb;

		$form_ids = array();
		$entries_table_name = self::get_table_name_for( self::ENTRIES_TABLE_NAME );

		if ( ! empty( $form_db_array ) ) {
			foreach ( $form_db_array as $form ) {
				$form->entries_count = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*)
                        FROM $entries_table_name
                        WHERE form_id = %d",
						$form->id
					)
				);

				self::process_form_db_fields( $form );

				array_push( $form_ids, $form->id );
			}

			$form_fields         = self::get_all_fields_for_form_ids( $form_ids );
			$form_fields_grouped = array();

			// group fields by form_id
			foreach ( $form_db_array as $form ) {
				$form_fields_grouped[ $form->id ] = array();

				foreach ( $form_fields as $field ) { // TODO: optimize all this
					$field->form_id = (int) $field->form_id;

					if ( $field->form_id === $form->id ) {
						array_push( $form_fields_grouped[ $form->id ], $field );
					}
				}

				$form->fields = $form_fields_grouped[ $form->id ];
			}

			// set fields to appropriate forms
			foreach ( $form_db_array as $form ) {
				if ( ! empty( $form_fields_grouped[ $form->id ] ) ) {
					foreach ( $form_fields_grouped[ $form->id ] as $field ) {
						self::process_field_db_fields( $field );
					}
				}
			}
		}

		return $form_db_array;
	}

	/**
	 * @param $form
	 */
	protected static function process_form_db_fields( $form ) {
		$form->id     = (int) $form->id;
		$form->active = (bool) $form->is_active;
		$form->name = stripslashes($form->name);
		$form->description = stripslashes($form->description);
		$form->config = stripslashes_deep(json_decode($form->config, true));

		unset( $form->is_active );
	}

	/**
	 * @param $field
	 */
	protected static function process_field_db_fields( $field ) {
		global $kcf_fieldFactory;

		$field->id        = (int) $field->id;
		$field->order     = (int) $field->field_order;
		$field->type      = (int) $field->type;
		$field->type_id   = $field->type;
		$field->type      = $kcf_fieldFactory->get_type_name( $field->type );
		$field->type_slug = $kcf_fieldFactory->get_type_slug( $field->type_id );
		$field->options   = stripslashes_deep(json_decode( $field->options, true ));

		unset( $field->form_id );
		unset( $field->field_order );
	}

	/**
	 * @param $form_ids
	 *
	 * @return array|null|object
	 */
	public static function get_all_fields_for_form_ids( $form_ids ) {
		global $wpdb;

		$table_name      = self::get_table_name_for( self::FIELDS_TABLE_NAME );
		$form_ids_string = join( ',', $form_ids );

		$results = $wpdb->get_results( "SELECT * from $table_name WHERE form_id IN ($form_ids_string) ORDER BY field_order" );

		return $results;
	}

	public static function get_last_field_id() {
		global $wpdb;

		$table_name = self::get_table_name_for( self::FIELDS_TABLE_NAME );

		$results = $wpdb->get_results( "SELECT MAX(id) AS max_id FROM $table_name" );

		return ( $results && $results[0] ) ? intval( $results[0]->max_id ) : - 1;
	}

	public static function get_all_entries() {
		global $wpdb;
		global $kcf_fieldFactory;

		$entries_table_name      = self::get_table_name_for( self::ENTRIES_TABLE_NAME );
		$fields_table_name       = self::get_table_name_for( self::FIELDS_TABLE_NAME );
		$entries_meta_table_name = self::get_table_name_for( self::ENTRIES_META_TABLE_NAME );

		$entries = $wpdb->get_results( "SELECT * FROM $entries_table_name ORDER BY created_at DESC");

		foreach ( $entries as $entry ) {
			$entry->id         = intval( $entry->id );
			$entry->form_id    = intval( $entry->form_id );
			$entry->is_read    = (bool) $entry->is_read;
			$entry->is_starred = (bool) $entry->is_starred;
			$entry->info       = $entry->info ? json_decode( $entry->info, true ) : new stdClass();

			$entry_id = $entry->id;

			$fields = $wpdb->get_results( "SELECT $entries_meta_table_name.*," .
			                              "$fields_table_name.field_order," .
			                              "$fields_table_name.name," .
			                              "$fields_table_name.type," .
			                              "$fields_table_name.options " .
			                              "from $entries_meta_table_name " .
			                              "LEFT JOIN $fields_table_name " .
			                              "ON $entries_meta_table_name.field_id=$fields_table_name.id " .
			                              "WHERE entry_id=$entry_id " .
			                              "ORDER BY $fields_table_name.field_order" );

			foreach ( $fields as $field ) {
				$field->id        = intval( $field->id );
				$field->field_id  = intval( $field->field_id );
				$field->type_id   = (int) $field->type;
				$field->type_slug = $kcf_fieldFactory->get_type_slug( $field->type_id );
				$field->order     = intval( $field->field_order );
				$field->value     = stripslashes_deep($field->field_value);
				$field->options   = stripslashes_deep(json_decode( $field->options, true ));

				unset( $field->form_id );
				unset( $field->entry_id );
				unset( $field->field_value );
				unset( $field->field_order );
			}

			$entry->fields = $fields;
		}

		return $entries;
	}

	/**
	 * @return int
	 */
	public static function create_form() {
		global $wpdb;

		$creation_timestamp = current_time( 'mysql' );

		$wpdb->insert(
			self::get_table_name_for( self::FORMS_TABLE_NAME ),
			array(
				'name'        => 'New Form',
				'slug'        => 'new-form',
				'created_at'  => $creation_timestamp,
				'modified_at' => $creation_timestamp,
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s'
			)
		);

		return $wpdb->insert_id;
	}

	/**
	 * @param $form_id
	 *
	 * @return false|int
	 */
	public static function delete_form( $form_id ) {
		global $wpdb;

		$wpdb->delete(
			self::get_table_name_for( self::FORMS_TABLE_NAME ),
			array(
				'id' => (int) $form_id
			),
			array(
				'%d'
			)
		);

		$wpdb->delete(
			self::get_table_name_for( self::FIELDS_TABLE_NAME ),
			array(
				'form_id' => (int) $form_id
			),
			array(
				'%d'
			)
		);

        $wpdb->delete(
            self::get_table_name_for( self::ENTRIES_TABLE_NAME ),
            array(
                'form_id' => (int) $form_id
            ),
            array(
                '%d'
            )
        );

        $wpdb->delete(
            self::get_table_name_for( self::ENTRIES_META_TABLE_NAME ),
            array(
                'form_id' => (int) $form_id
            ),
            array(
                '%d'
            )
        );

		return;
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	public static function new_fields_filter( $field ) {
		return (bool) array_key_exists( 'cid', $field );
	}

	/**
	 * @param $form_data
	 *
	 * @return array
	 */
	public static function update_form( $form_data ) {
		global $wpdb;

		$form_id = (int) $form_data['id'];

		$creation_timestamp = current_time( 'mysql' );

		// update form record
		$wpdb->update(
			self::get_table_name_for( self::FORMS_TABLE_NAME ),
			array(
			    'name' => $form_data['name'],
			    'description' => $form_data['description'],
				'modified_at' => $creation_timestamp,
				'config'      => json_encode( $form_data['config'] )
			),
			array( 'id' => $form_id ),
			array(
				'%s',
				'%s',
				'%s',
				'%s'
			),
			array(
				'%d'
			)
		);

		// update form fields
		$all_fields = $form_data["fields"];
		$new_fields = array();
		$old_fields = array();

		// sort to old and new fields
		foreach ( $all_fields as $index => $field ) {
			$field["field_order"] = $index;

			if ( array_key_exists( 'cid', $field ) ) {
				array_push( $new_fields, $field );
			} else {
				array_push( $old_fields, $field );
			}
		}

		// delete removed fields
		$current_fields_count = count( $old_fields );

		$fields_table_name = self::get_table_name_for( self::FIELDS_TABLE_NAME );

		if ( $current_fields_count ) {

			$prepare_args           = array();
			$in_clause_placeholders = '';

			array_push( $prepare_args, $form_id );

			foreach ( $old_fields as $old_field ) {
				array_push( $prepare_args, $old_field['id'] );
				$in_clause_placeholders .= '%d,';
			}

			$in_clause_placeholders = trim( $in_clause_placeholders, ',' );

			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM $fields_table_name " .
					"WHERE form_id = %d " .
					"AND id NOT IN(" . $in_clause_placeholders . ")",
					$prepare_args
				)
			);
		} else { // all fields deleted case
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM $fields_table_name " .
					"WHERE form_id = %d",
					$form_id
				)
			);
		}

		$cid_to_id_map = array();

		// add new fields
		foreach ( $new_fields as $field ) {
			$field_options = array();

			if ( is_array( $field['options'] ) ) {
				$field_options = array_map( array( 'KCF_DbModel', 'field_options_save_map' ), $field['options'] );
			}

			$wpdb->insert(
				self::get_table_name_for( self::FIELDS_TABLE_NAME ),
				array(
					'name'        => $field['name'],
					'field_order' => $field['field_order'],
					'created_at'  => $creation_timestamp,
					'modified_at' => $creation_timestamp,
					'options'     => json_encode( $field_options ),
					'type'        => (int) $field['type_id'],
					'form_id'     => $form_id,
				),
				array(
					'%s',
					'%d',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d'
				)
			);

			$cid_to_id_map[]= array("cid" => (int) $field['cid'], "id" => $wpdb->insert_id);
		}

		// update old fields
		foreach ( $old_fields as $field ) {
			$field_options = array();

			if ( is_array( $field['options'] ) ) {
				$field_options = array_map( array( 'KCF_DbModel', 'field_options_save_map' ), $field['options'] );
			}

			$wpdb->update(
				self::get_table_name_for( self::FIELDS_TABLE_NAME ),
				array(
					'name'        => $field['name'],
					'field_order' => $field['field_order'],
					'modified_at' => $creation_timestamp,
					'options'     => json_encode( $field_options ),
					'type'        => (int) $field['type_id'],
					'form_id'     => $form_id,
				),
				array( 'id' => $field['id'] ),
				array(
					'%s',
					'%d',
					'%s',
					'%s',
					'%d',
					'%d'
				),
				array(
					'%d'
				)
			);
		}

		return array( "new" => $new_fields, "old" => $old_fields, "cidMap" => $cid_to_id_map );
	}

	public static function field_options_save_map( $option ) {
		return array( $option['id'] => $option['value'] );
	}

	public static function save_entry( $form_id, $form_data ) {
		$form_id   = intval( $form_id );
		$fields    = $form_data['values'];
		$field_ids = array();
		$fields_table_name = self::get_table_name_for( self::FIELDS_TABLE_NAME );

		global $wpdb;

		// get fields options
		foreach ( $fields as $field => $data ) {
			array_push( $field_ids, intval( $data['id'] ) );
		}

		$prepare_args           = array();
		$in_clause_placeholders = '';

		foreach ( $field_ids as $field_id ) {
			array_push( $prepare_args, $field_id );
			$in_clause_placeholders .= '%d,';
		}

		$in_clause_placeholders = trim( $in_clause_placeholders, ',' );

		$all_fields = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, type, options FROM $fields_table_name " .
				"WHERE id IN(" . $in_clause_placeholders . ")",
				$prepare_args
			)
		);

		$fields_info = array();

		foreach ( $all_fields as $field ) {
			$fields_info[ $field->id ] = $field;
		}

		$creation_timestamp = current_time( 'mysql' );

		$info = array();

		$info['ip'] = $_SERVER['REMOTE_ADDR'];
		$info['referrer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

		$wpdb->insert(
			self::get_table_name_for( self::ENTRIES_TABLE_NAME ),
			array(
				'created_at'  => $creation_timestamp,
				'modified_at' => $creation_timestamp,
				'form_id'     => $form_id,
				'info'        => json_encode( $info )
			),
			array(
				'%s',
				'%s',
				'%d',
				'%s'
			)
		);

		$entry_id = $wpdb->insert_id;

		foreach ( $fields as $field => $data ) {
			$prepared_value = is_string( $data["value"] ) ? $data["value"] : json_encode( $data["value"] );

			$field_info    = $fields_info[ $data["id"] ];
			$field_options = $field_info->options ? json_decode( $field_info->options, true ) : array();
			$field_label   = '';

			foreach ( $field_options as $option ) {
				if ( $option && array_key_exists("label", $option ) ) {
					$field_label = $option["label"];
				}
			}

			$wpdb->insert(
				self::get_table_name_for( self::ENTRIES_META_TABLE_NAME ),
				array(
					'form_id'     => $form_id,
					'entry_id'    => $entry_id,
					'field_id'    => intval( $data["id"] ),
					'field_value' => $prepared_value,
					'type'        => intval( $field_info->type ),
					'label'       => $field_label,
				),
				array(
					'%d',
					'%d',
					'%d',
					'%s',
					'%d',
					'%s'
				)
			);
		}
	}

	public static function set_form_flag( $form_id, $field, $value ) {
		$allowed_fields = array( 'is_active' );

		if ( ! in_array( $field, $allowed_fields ) ) {
			return;
		}

		self::update_flag_for_form( $form_id, $field, $value );
	}

	public static function update_flag_for_form( $form_id, $field, $value ) {
		global $wpdb;

		$prepare_args           = array();
		$in_clause_placeholders = '';

		$in_clause_placeholders = trim( $in_clause_placeholders, ',' );

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE " . self::get_table_name_for( self::FORMS_TABLE_NAME ) . " " .
				"SET " . $field . " = " . intval( $value ) . " " .
				"WHERE id = %d",
				array( intval( $form_id ) )
			)
		);
	}

	public static function set_entries_flag( $entries_ids, $field, $value ) {
		$allowed_fields = array( 'is_starred', 'is_read' );

		if ( ! in_array( $field, $allowed_fields ) ) {
			return;
		}

		self::update_flag_for_entries_with_ids( $entries_ids, $field, $value );
	}

	public static function update_flag_for_entries_with_ids( $entries_ids, $field, $value ) {
		global $wpdb;

		$prepare_args           = array();
		$in_clause_placeholders = '';

		foreach ( $entries_ids as $entry_id ) {
			array_push( $prepare_args, $entry_id );
			$in_clause_placeholders .= '%d,';
		}

		$in_clause_placeholders = trim( $in_clause_placeholders, ',' );

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE " . self::get_table_name_for( self::ENTRIES_TABLE_NAME ) . " " .
				"SET " . $field . " = " . intval( $value ) . " " .
				"WHERE id IN(" . $in_clause_placeholders . ")",
				$prepare_args
			)
		);
	}

	public static function delete_entries_with_ids( $entries_ids ) {
		global $wpdb;

		$prepare_args           = array();
		$in_clause_placeholders = '';
		$entries_table_name = self::get_table_name_for( self::ENTRIES_TABLE_NAME );
		$entries_meta_table_name = self::get_table_name_for( self::ENTRIES_META_TABLE_NAME );

		foreach ( $entries_ids as $entry_id ) {
			array_push( $prepare_args, $entry_id );
			$in_clause_placeholders .= '%d,';
		}

		$in_clause_placeholders = trim( $in_clause_placeholders, ',' );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $entries_table_name " .
				"WHERE id IN(" . $in_clause_placeholders . ")",
				$prepare_args
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $entries_meta_table_name " .
				"WHERE entry_id IN(" . $in_clause_placeholders . ")",
				$prepare_args
			)
		);
	}
}