<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

class KCF_AjaxActions {
	const NONCE = 'chiliforms_nonce';
	const NONCE_KEY = 'chiliforms_ajax_nonce';

	public function __construct() {}

	public function register() {
		// editor actions
		add_action('wp_ajax_kcf_create_form', array($this, 'ajax_create_form'));
		add_action('wp_ajax_kcf_delete_form', array($this, 'ajax_delete_form'));
		add_action('wp_ajax_kcf_save_form', array($this, 'ajax_save_form'));
		add_action('wp_ajax_kcf_activate_form', array($this, 'ajax_activate_form'));
		add_action('wp_ajax_kcf_deactivate_form', array($this, 'ajax_deactivate_form'));
		add_action('wp_ajax_kcf_mark_entries_as_read', array($this, 'ajax_mark_entries_as_read'));
		add_action('wp_ajax_kcf_mark_entries_as_unread', array($this, 'ajax_mark_entries_as_unread'));
		add_action('wp_ajax_kcf_mark_entries_as_starred', array($this, 'ajax_mark_entries_as_starred'));
		add_action('wp_ajax_kcf_mark_entries_as_unstarred', array($this, 'ajax_mark_entries_as_unstarred'));
		add_action('wp_ajax_kcf_delete_entries', array($this, 'ajax_delete_entries'));
		add_action('wp_ajax_kcf_save_plugin_settings', array($this, 'ajax_save_plugin_settings'));

		// clientside actions
		add_action('wp_ajax_kcf_submit_form', array($this, 'ajax_submit_form'));
		add_action('wp_ajax_nopriv_kcf_submit_form', array($this, 'ajax_submit_form'));
	}

	public static function get_nonce() {
		return self::NONCE;
	}

	public static function get_nonce_key() {
		return self::NONCE_KEY;
	}

	protected function send_security_error() {
		echo json_encode( array(
			'status' => 1,
			'errors' => array(
				'global' => array(
					array(
						'code'          => 4001,
						'error_message' => 'Security error. Sorry, you cannot currently perform this action.',
						'chiliforms'
					)
				)
			)
		) );

		wp_die();
	}

	protected function check_admin_user() {
		if (!current_user_can('administrator')) {
			$this->send_security_error();
		}

		$this->check_user();
	}

	protected function check_user() {
		if (!check_ajax_referer(self::get_nonce(), self::get_nonce_key(), false)) {
			$this->send_security_error();
		}
	}

	public function ajax_create_form() {
		$this->check_admin_user();

		$new_form_id = KCF_DbModel::create_form();

		echo json_encode(array(
			'status' => 0,
			'newFormId' => $new_form_id,
			'forms' => KCF_DbModel::get_all_forms()
		));

		wp_die();
	}

	public function ajax_delete_form() {
		$this->check_admin_user();

		$form_id = intval( $_POST['formId'] );
		$status = 0;

		if (!empty($form_id)) {
			$delete_result = KCF_DbModel::delete_form($form_id);

			if ($delete_result === false) {
				$status = 1;
			}
		} else {
			$status = 1;
		}

		echo json_encode(array(
			'status' => $status,
			'forms' => KCF_DbModel::get_all_forms()
		));

		wp_die();
	}

	public function ajax_save_form() {
		$this->check_admin_user();

		$form_id = intval( $_POST['formId'] );
		$form_data = $_POST['formData'];
		$status = 0;

		echo json_encode(array(
			'formSaveResult' => KCF_DbModel::update_form($form_data),
			'status' => $status,
			'forms' => KCF_DbModel::get_all_forms()
		));

		wp_die();
	}

	public function ajax_activate_form() {
		$this->check_admin_user();

		$form_id = intval( $_POST['formId'] );

		$status = 0;

		KCF_DbModel::set_form_flag($form_id, 'is_active', true);

		echo json_encode(array(
			'result' => $form_id,
			'status' => $status
		));

		wp_die();
	}

	public function ajax_deactivate_form() {
		$this->check_admin_user();

		$form_id = intval( $_POST['formId'] );

		$status = 0;

		KCF_DbModel::set_form_flag($form_id, 'is_active', false);

		echo json_encode(array(
			'result' => $form_id,
			'status' => $status
		));

		wp_die();
	}

	public function ajax_submit_form() {
		$this->check_user();

		$form_id = intval( $_POST['formId'] );
		$form_data = $_POST['formData'];

		$form = KCF_DbModel::get_form_by_id($form_id);

		if (!$form) {
			wp_die();
		}

		$validation_result = KCF_FieldValidator::validate($form, $form_data);

		if (count($validation_result['global']) > 0 ||
		    count($validation_result['fields']) > 0 ||
		    count($validation_result['recaptcha']) > 0) {
			echo json_encode(array(
				'formId' => $form_id,
				'status' => 1,
				'errors' => $validation_result,
				'formSaveResult' => $form_data,
			));

			wp_die();
		}

		// save entry to DB
		KCF_DbModel::save_entry($form_id, $form_data);

		// proceed with actions
		$form_model = new KCF_FormModel($form);

		if (!$form_model->check_bool_setting('admin_email_disable')) {
			$headers = array();

			$is_html = $form_model->check_bool_setting('admin_email_html');

			if ($is_html) {
				array_push($headers, 'Content-Type: text/html; charset=UTF-8');
			}

			$to = trim($form_model->get_setting("admin_email_to"));
			$from = trim($form_model->get_setting("admin_email_from"));
			$subject = trim($form_model->get_setting("admin_email_subject"));
			$message = trim($form_model->get_setting("admin_email_content"));

			$to = str_replace('[wp_email]', get_bloginfo( 'admin_email' ), $to);

			if ($from) {
				$from = str_replace('[wp_email]', get_bloginfo( 'admin_email' ), $from);
				$from = str_replace('[wp_title]', get_bloginfo( 'name' ), $from);

				$headers[] = 'From: ' . $from;
			}

			$message = str_replace(
				'[form_contents]',
				$this->get_entry_contents_for_email($form_data, $form_model, $is_html),
				$message
			);

			if ($to && $subject && $message) {
				wp_mail(
					$to,
					$subject,
					$message,
					$headers
				);
			}
		}

		echo json_encode(array(
			'formSaveResult' => $form_data,
			'formId' => $form_id,
			'status' => 0
		));

		wp_die();
	}

	protected function get_entry_contents_for_email($data, $form_model, $is_html = false) {
		$print_data = array();

		foreach($data["values"] as $entry_item) {
			$field = $form_model->get_field_by_id((int) $entry_item["id"]);
			$parsed_value = $entry_item["value"] ? $entry_item["value"] : "";

			if ($entry_item["value"] &&
			    is_array($entry_item["value"]) &&
			    sizeof($entry_item["value"])) {

				if ($field->type_slug === 'dropdown' || $field->type_slug === 'radio') {
					$key = $entry_item["value"]["key"];
					$label = $entry_item["value"]["label"];

					$parsed_value = $label . ( $key !== $label ? ' (' . $key . ')' : '');
				} else if ($field->type_slug === 'checkbox') {
					$selected = array_filter($entry_item["value"], function($option) {
						return $option["value"] === "true" || $option["value"] === true;
					});

					$parsed_value = array_reduce($selected, function($result, $option) {
						$key = $option["key"];
						$label = $option["label"];

						return $result . $label . ( $key !== $label ? ' (' . $key . ')' : '') . ", ";
					}, "");

					$parsed_value = trim($parsed_value, ", ");
				}
			}

			array_push($print_data, array(
				"label" => $form_model->get_field_option("label", $field),
				"value" => $parsed_value
			));
		}

		ob_start();

		if ($is_html) {
			$this->print_entry_as_html($print_data);
		} else {
			$this->print_entry_as_text($print_data);
		}

		$contents = ob_get_clean();

		return $contents;
	}

	protected function print_entry_as_text($print_data) {
		foreach($print_data as $row):
			echo esc_html($row["label"]) . ": " . esc_html($row["value"]) . "\r\n";
		endforeach;
	}

	protected function print_entry_as_html( $print_data ) {
		$index = 0;
		$border_color = "#ddd";
		$even_color = "#f8f8f8";
		$odd_color = "#ffffff";
		$head_bg = "#0B73A0";
		$head_color = "#fff";

		?>
		<br/>
		<br/>
		<table style="border-collapse:collapse; text-align:left;" cellpadding="10">
			<tr style="border:1px solid <?php
				echo esc_attr($border_color);
			?>; background-color:<?php
				echo esc_attr($head_bg);
			?>; color: <?php echo esc_attr($head_color);
			?>">
				<th style="border-right:1px solid <?php echo esc_attr($border_color); ?>;">Form field</th>
				<th>Submitted value</th>
			</tr>
			<?php
			foreach ( $print_data as $row ): ?>
				<tr style="border:1px solid <?php echo esc_attr($border_color); ?>; background-color:<?php
					if ($index % 2 === 0) :
						echo esc_attr($even_color);
					else:
						echo esc_attr($odd_color);
					endif;
				?>">
					<td style="border-right:1px solid <?php echo esc_attr($border_color); ?>;"><?php echo esc_html( $row["label"] ); ?></td>
					<td><strong><?php echo esc_html($row["value"]); ?></strong></td>
				</tr>
			<?php
			++$index;
			endforeach;
			?>
		</table>
		<br/>
		<span style="font-style: italic; font-size: 12px; color: #888">Sent via <a href="<?php echo esc_attr(KCF_PROJECT_URL); ?>">ChiliForms</a> v<?php echo esc_html(KCF_VERSION); ?></span>
		<br/>
		<br/>
	<?php
	}

	public function ajax_mark_entries_as_read() {
		$this->check_admin_user();

		$entries_ids = $_POST['entriesIds'];

		$status = 0;

		KCF_DbModel::set_entries_flag($entries_ids, 'is_read', true);

		echo json_encode(array(
			'result' => $entries_ids,
			'status' => $status
		));

		wp_die();
	}

	public function ajax_mark_entries_as_unread() {
		$this->check_admin_user();

		$entries_ids = $_POST['entriesIds'];

		$status = 0;

		KCF_DbModel::set_entries_flag($entries_ids, 'is_read', false);

		echo json_encode(array(
			'result' => $entries_ids,
			'status' => $status
		));

		wp_die();
	}

	public function ajax_mark_entries_as_starred() {
		$this->check_admin_user();

		$entries_ids = $_POST['entriesIds'];

		$status = 0;

		KCF_DbModel::set_entries_flag($entries_ids, 'is_starred', true);

		echo json_encode(array(
			'result' => $entries_ids,
			'status' => $status
		));

		wp_die();
	}

	public function ajax_mark_entries_as_unstarred() {
		$this->check_admin_user();

		$entries_ids = $_POST['entriesIds'];

		$status = 0;

		KCF_DbModel::set_entries_flag($entries_ids, 'is_starred', false);

		echo json_encode(array(
			'result' => $entries_ids,
			'status' => $status
		));

		wp_die();
	}

	public function ajax_delete_entries() {
		$this->check_admin_user();

		$entries_ids = $_POST['entriesIds'];

		$status = 0;

		KCF_DbModel::delete_entries_with_ids($entries_ids);

		echo json_encode(array(
			'result' => $entries_ids,
			'status' => $status
		));

		wp_die();
	}

	public function ajax_save_plugin_settings() {
		$this->check_admin_user();

		$settings = $_POST['settings'];

		$status = 0;

		KCF_Options::save($settings);

		echo json_encode(array(
			'status' => $status
		));

		wp_die();
	}
}