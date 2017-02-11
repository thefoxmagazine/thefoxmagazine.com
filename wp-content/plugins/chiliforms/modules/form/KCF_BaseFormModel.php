<?php

/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */
abstract class KCF_BaseFormModel {

	private $form;
	private $form_settings;
	private $form_style_settings;
	private $theme;
	
	const RECAPTCHA_VERIFY_ENDPOINT = "https://www.google.com/recaptcha/api.js";

	public function __construct($form) {
		$this->form = $form;
		$this->form_settings = $this->form->config["settings"];
		$this->form_style_settings = $this->form->config["style"];
		$this->theme = $this->get_style_setting("form_theme");
	}

	public function render() {

	    if (!$this->form) {
	        ?><p><?php _e("This form was deleted", "chiliforms"); ?></p><?php

            return;
	    }

	    if (!$this->form->active) {
	        ?><p><?php _e("This form is currently inactive", "chiliforms"); ?></p><?php

	        return;
	    }
		
		?>
		<div class="<?php echo esc_attr($this->get_form_container_classes()); ?>">
			<?php
				// custom form CSS styles
				$this->dynamic_style();
			?>
			<form id="<?php echo esc_attr( $this->get_form_id_attr() ); ?>"
			      class="<?php echo esc_attr($this->get_form_classes()); ?>"
			      data-form-id="<?php echo esc_attr( $this->form->id ); ?>"
			      style="<?php echo esc_attr($this->get_form_inline_style());?>"
			      novalidate><?php

				// header
				$this->render_header();

				foreach ( $this->form->fields as $field ) {
					$this->render_field( $field );
				}

				// reCAPTCHA
				$this->render_captcha();

				// footer
				$this->render_footer();
	            ?>
			</form>
		</div>
	<?php
	}

	/**
	 * Renders form header
	 */
	protected function render_header() {
		$form_name_display = $this->check_bool_setting("form_name_display");
		$form_description_display = $this->check_bool_setting("form_description_display");

		if ( $form_name_display || $form_description_display ): ?>
			<div class="cf-form-header">
				<?php
				if ( $form_name_display ):
					?><h3 class="cf-form-name"><?php echo esc_html( $this->form->name ); ?></h3><?php
				endif;

				if ( $form_description_display ):
					?>
					<div class="cf-form-description"><?php echo esc_html( $this->form->description ); ?></div><?php
				endif; ?>
			</div>
		<?php
		endif;
	}

	/**
	 * Renders form captcha, if needed
	 */
	protected function render_captcha() {
		if(KCF_Options::option("recaptcha_public_key") && $this->check_bool_setting("form_recaptcha_on")):

			$script_url = self::RECAPTCHA_VERIFY_ENDPOINT . "?onload=KCFRecaptchaCallback&render=explicit";

			if ( KCF_Options::option( "recaptcha_language" ) ):
				$script_url .= "&hl=" . KCF_Options::option( "recaptcha_language" );
			endif;

			?>
			<script type="text/javascript">
				var KCFRecaptchaCallback = function() {
					grecaptcha.render("<?php echo esc_js("cf_" . $this->form->id . "_recaptcha"); ?>", {
						"sitekey" : "<?php echo esc_js(KCF_Options::option("recaptcha_public_key")); ?>"
					});
				};
			</script>
			<script src="<?php echo esc_attr($script_url); ?>" async defer></script>
			<div class="cf-recaptcha-container">
				<div id="<?php echo esc_attr("cf_" . $this->form->id . "_recaptcha")?>"></div>
			</div>
		<?php endif;
	}

	/**
	 * Renders form footer
	 */
	protected function render_footer() {
		$submit_label = "Submit";

		if ($this->check_setting("submit_label") ) {
			$submit_label = $this->get_setting("submit_label");
		}
		?>
		<div class="<?php echo esc_attr( $this->get_form_footer_classes() ) ?>">
			<?php if ( $this->theme === "html5" ): ?>
				<button class="cf-submit">
					<?php echo esc_html( $submit_label ); ?>
				</button>
			<?php else: ?>
				<a href="#" class="cf-submit">
					<?php echo esc_html( $submit_label ); ?>
				</a>
			<?php endif; ?>
			<div class="cf-form-messages"></div>
		</div>
	<?php
	}

	/**
	 * Gets CSS classes for form container
	 * @return string
	 */
	protected function get_form_container_classes() {
		$container_classes = array("cf-form-holder");

		array_push($container_classes, "cf-align-" . $this->get_style_setting("form_align"));

		return $this->prepare_classes($container_classes);
	}

	/**
	 * Gets dynamic CSS for this form, prefixed by form ID
	 */
	protected function dynamic_style() {
		// styles values
		$main = $this->get_style_setting("form_main_color");
		$submit_bg = $this->get_style_setting("form_submit_bg");
		$submit_color = $this->get_style_setting("form_submit_color");
		$submit_width = $this->get_style_setting("form_submit_width");
		$validation_success = $this->get_style_setting("form_validation_success_color");
		$validation_error = $this->get_style_setting("form_validation_error_color");
		$focus = $this->get_style_setting("form_focused_field_color");

		$css = new KCF_CSSHelper("cf_form_" . $this->form->id);

		// submit
		$css->add(".cf-submit", array(
			"background: $submit_bg",
			"color: $submit_color",
			"box-shadow: " . ($this->theme === "lite" ? "0 3px 0 0 " . $this->shadeColor($submit_bg, -20) : null),
			"width: " . ($submit_width !== "auto" ? "$submit_width%" : null)
		));

		// validation error
		$css->add(array(
			".cf-validation-error input[type=\"text\"]",
			".cf-validation-error textarea",
			".cf-validation-error select",
			".cf-validation-error .cf-wrapped-select__control",
		), array(
			"border-color: $validation_error"
		));

		$css->add(array(
			".cf-validation-error .cf-label-text",
			".cf-field-validation-message"
		), array(
			"color: $validation_error"
		));

		// validation success
		$css->add(array(
			".cf-validation-success input[type=\"text\"]",
			".cf-validation-success textarea",
			".cf-validation-success select",
			".cf-validation-success .cf-wrapped-select__control",
		), array(
			"border-color: $validation_success"
		));

		$css->add(array(
			".cf-validation-success .cf-label-text"
		), array(
			"color: $validation_success"
		));

		// focus
		$css->add(array(
			"input[type=\"text\"]:focus",
			"textarea:focus"
		), array(
			"border-color: $focus"
		));

		// radios and checkboxes
		$css->add(".cf-input-option-display:after", array(
			"background: $main"
		));

		?><style><?php $css->render_styles(); ?></style>
	<?php
	}

	/**
	 * Gets form id attribute
	 * @return string
	 */
	private function get_form_id_attr() {
		return "cf_form_" . $this->form->id;
	}

	/**
	 * Gets form css classes
	 * @return string
	 */
	private function get_form_classes() {
		$form_classes = array("cf-form");

		array_push($form_classes, $this->get_setting("form_custom_css_class"));

		$form_width_type = $this->get_style_setting("form_width_type");

		array_push($form_classes, "cf-form-width-" . $form_width_type);
		array_push($form_classes, "cf-form-theme-" . $this->theme);
		array_push($form_classes, "cf-align-" . $this->get_style_setting("form_content_align"));

		if ($form_width_type === "percent") {
			array_push($form_classes, "cf-width-" . $this->get_style_setting("form_width_percent"));
		}

		return $this->prepare_classes($form_classes);
	}
	
	/**
	 * Gets additional inline form styles form form element
	 * @return string
	 */
	protected function get_form_inline_style() {
		$inline_style = "";
		
		$form_width_type = $this->get_style_setting("form_width_type");

		if ($form_width_type === "px") {
			$inline_style = "width: " . $this->get_style_setting("form_width_px") . "px";
		}

		return $inline_style;
	}

	/**
	 * Gets classes for form footer
	 * @return string
	 */
	protected function get_form_footer_classes() {
		$footer_classes = array("cf-form-footer");

		array_push($footer_classes, "cf-align-" . $this->get_style_setting("form_footer_align"));

		return $this->prepare_classes($footer_classes);
	}

	/**
	 * Removes empty classes and converts them to string
	 * @param $classes
	 *
	 * @return string
	 */
	private function prepare_classes($classes) {
		return $this->get_classes_string($this->get_filtered_classes($classes));
	}

	/**
	 * Removes empty classes from classes array
	 * @param $classes
	 *
	 * @return array
	 */
	private function get_filtered_classes($classes) {
		return array_filter($classes, function($entry) {
			return $entry && trim($entry) !== "";
		});
	}

	/**
	 * Converts classes array to string
	 * @param $classes
	 *
	 * @return string
	 */
	private function get_classes_string($classes) {
		return join(" ", $classes);
	}

	/**
	 * Render fields depending on type
	 * @param $field
	 */
	protected function render_field( $field ) {
		global $kcf_fieldFactory;

		$type_slug        = $kcf_fieldFactory->get_type_slug( $field->type_id );
		$field_required   = $this->get_required( $field );
		$field_custom_css = $this->get_custom_css( $field );

		?><div
			class="cf-field-wrap cf-field-type-<?php echo esc_attr( $type_slug ); ?> <?php
			echo esc_attr( $field_custom_css ); ?><?php
			echo $field_required ? " " . "cf-required-field" : "";
			echo " " . "cf-width-" . esc_attr($this->get_field_option("field_width_percent", $field));
			?>"
			data-type="<?php echo esc_attr( $type_slug ); ?>"
			data-validation-type="<?php echo esc_attr($this->get_field_option("field_validation_type", $field)); ?>"
			data-id="<?php echo esc_attr( $field->id ); ?>"
			><div class="cf-field-inner-wrap">
		<?php

			if ( $kcf_fieldFactory->is_of_type( $field->type_id, "SINGLE_LINE_INPUT" ) ) {
				$this->render_input( $field, $field_required );
			} else if ( $kcf_fieldFactory->is_of_type( $field->type_id, "MULTI_LINE_INPUT" ) ) {
				$this->render_textarea( $field, $field_required );
			} else if ( $kcf_fieldFactory->is_of_type( $field->type_id, "DROPDOWN" ) ) {
				$this->render_select( $field, $field_required );
			} else if ( $kcf_fieldFactory->is_of_type( $field->type_id, "CHECKBOX" ) ) {
				$this->render_checkbox( $field, $field_required );
			} else if ( $kcf_fieldFactory->is_of_type( $field->type_id, "RADIO" ) ) {
				$this->render_radio( $field, $field_required );
			} else if ( $kcf_fieldFactory->is_of_type( $field->type_id, "EMAIL" ) ) {
				$this->render_email( $field, $field_required );
            } else if ( $kcf_fieldFactory->is_of_type( $field->type_id, "URL" ) ) {
                $this->render_url( $field, $field_required );
            } else {
				echo $field->type_id;
			}

			?><div class="cf-field-validation-message"></div></div></div><?php // no spaces to prevent inline-block bugs
	}
	
	protected function render_input( $field, $field_required ) {
		?>
		<label class="cf-field-label"><span class="cf-label-text"><?php echo esc_html( $this->get_label( $field ) . $this->get_required_symbol( $field_required ) ); ?></span><br/>
			<input type="text" class="cf-form-control"
			       name="<?php echo esc_attr( $this->get_field_name_attr( $field ) ); ?>"
			       placeholder="<?php echo esc_attr( $this->get_placeholder( $field ) ); ?>"/>
		</label>
	<?php
	}

	protected function render_textarea( $field, $field_required ) {
		?>
		<label class="cf-field-label"><span class="cf-label-text"><?php echo esc_html( $this->get_label( $field ) . $this->get_required_symbol( $field_required ) ); ?></span><br/>
			<textarea class="cf-form-control"
			          name="<?php echo esc_attr( $this->get_field_name_attr( $field ) ); ?>"
			          rows="<?php echo esc_attr($this->get_field_option("rows", $field)); ?>"
			          placeholder="<?php echo esc_attr( $this->get_placeholder( $field ) ); ?>"></textarea>
		</label>
	<?php
	}

	protected function render_select( $field, $field_required) {
	    if ($this->theme === "html5"):
		?>
		<label class="cf-field-label"><span class="cf-label-text"><?php echo esc_html( $this->get_label( $field ) . $this->get_required_symbol( $field_required ) ); ?></span><br/>
			<select class="cf-form-control"
			        name="<?php echo esc_attr( $this->get_field_name_attr( $field ) ); ?>">
				<option value="" data-label=""><?php echo esc_html( $this->get_placeholder( $field ) ); ?></option>
				<?php
				$options = $this->get_field_option( "options", $field );

                if (!empty($options)):
                    foreach ( $options as $option ):
                        $label = $option["optionLabel"];
                        $key   = trim( $option["optionKey"] );

                        $key = $key ? $key : $label;
                        ?>
                        <option value="<?php echo esc_attr( $key ); ?>" data-label="<?php echo esc_attr( $label ); ?>"><?php echo esc_attr( $label ); ?></option>
                    <?php
                    endforeach;
				endif;
				?>
			</select>
		</label>
	<?php
	else: ?>
	<label class="cf-field-label"><span class="cf-label-text"><?php echo esc_html( $this->get_label( $field ) . $this->get_required_symbol( $field_required ) ); ?></span><br/>
        <select class="cf-form-control cf-wrapped-select__original"
                name="<?php echo esc_attr( $this->get_field_name_attr( $field ) ); ?>">
            <option value="" data-label=""><?php echo esc_html( $this->get_placeholder( $field ) ); ?></option>
            <?php
            $options = $this->get_field_option( "options", $field );

            if (!empty($options)):
                foreach ( $options as $option ):
                    $label = $option["optionLabel"];
                    $key   = trim( $option["optionKey"] );

                    $key = $key ? $key : $label;
                    ?>
                    <option value="<?php echo esc_attr( $key ); ?>" data-label="<?php echo esc_attr( $label ); ?>"><?php echo esc_html( $label ); ?></option>
                <?php
                endforeach;
            endif;
            ?>
        </select>

        <div class="cf-wrapped-select">
            <div class="cf-wrapped-select__control">
                <div class="cf-wrapped-select__current"><?php echo esc_html( $this->get_placeholder( $field ) ); ?></div>
                <span class="cf-wrapped-select__arrow-box"></span>
            </div>
            <div class="cf-wrapped-select__options">
                <div data-value="" class="cf-wrapped-select__option"><?php echo esc_html( $this->get_placeholder( $field ) ); ?></div>
                <?php
                $options = $this->get_field_option( "options", $field );

                if (!empty($options)):
                    foreach ( $options as $option ):
                        $label = $option["optionLabel"];
                        $key   = trim( $option["optionKey"] );

                        $key = $key ? $key : $label;
                        ?>
                        <div data-value="<?php echo esc_attr( $key ); ?>" class="cf-wrapped-select__option"><?php echo esc_html( $label ); ?></div>
                    <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </label>
	<?php endif;
	}

	protected function render_checkbox( $field, $field_required ) {
		?>
		<label class="cf-field-label"><span class="cf-label-text"><?php echo esc_html( $this->get_label( $field ) . $this->get_required_symbol( $field_required ) ); ?></span><br/></label>

		<?php
		if ($this->theme === "html5"):

		$options = $this->get_field_option( "options", $field );

        if (!empty($options)):
            foreach ( $options as $option ):
                $label = $option["optionLabel"];
                $key   = trim( $option["optionKey"] );

                $key = $key ? $key : $label;
                ?>
                <label class="cf-option-label">
                    <input type="checkbox"
                           class="cf-form-control"
                           name="<?php echo esc_attr( $this->get_field_name_attr( $field ) ); ?>"
                           value="<?php echo esc_attr( $key ); ?>"
                           data-label="<?php echo esc_attr( $label ); ?>" />
                    <span class="cf-input-label-wrap"><?php echo esc_html( $label ); ?></span>
                </label>
            <?php
            endforeach;
		endif;

		else:

		$options = $this->get_field_option( "options", $field );

        if (!empty($options)):
            foreach ( $options as $option ):
                $label = $option["optionLabel"];
                $key   = trim( $option["optionKey"] );

                $key = $key ? $key : $label;
                ?>
                <label class="cf-option-label">
                    <input type="checkbox"
                           class="cf-form-control"
                           name="<?php echo esc_attr( $this->get_field_name_attr( $field ) ); ?>"
                           value="<?php echo esc_attr( $key ); ?>"
                           data-label="<?php echo esc_attr( $label ); ?>" />
                    <span class="cf-input-option-display"></span>
                    <span class="cf-input-label-wrap"><?php echo esc_html( $label ); ?></span>
                </label>
            <?php
            endforeach;
        endif;

		endif;
	}

	protected function render_radio( $field, $field_required ) {
		?>
		<label class="cf-field-label"><span class="cf-label-text"><?php echo esc_html( $this->get_label( $field ) . $this->get_required_symbol( $field_required ) ); ?></span><br/></label>

		<?php
		if ($this->theme === "html5"):

		$options = $this->get_field_option( "options", $field );

        if (!empty($options)):
            foreach ( $options as $option ):
                $label = $option["optionLabel"];
                $key   = trim( $option["optionKey"] );

                $key = $key ? $key : $label;
                ?>

                <label class="cf-option-label">
                    <input type="radio"
                           class="cf-form-control"
                           name="<?php echo esc_attr( $this->get_field_name_attr( $field ) ); ?>"
                           value="<?php echo esc_attr( $key ); ?>"
                           data-label="<?php echo esc_attr( $label ); ?>" />
                    <span class="cf-input-label-wrap"><?php echo esc_html( $label ); ?></span>
                </label>
            <?php
            endforeach;
		endif;

        else:

        $options = $this->get_field_option( "options", $field );

        if (!empty($options)):
            foreach ( $options as $option ):
                $label = $option["optionLabel"];
                $key   = trim( $option["optionKey"] );

                $key = $key ? $key : $label;
                ?>

                <label class="cf-option-label">
                    <input type="radio"
                           class="cf-form-control"
                           name="<?php echo esc_attr( $this->get_field_name_attr( $field ) ); ?>"
                           value="<?php echo esc_attr( $key ); ?>"
                           data-label="<?php echo esc_attr( $label ); ?>" />
                    <span class="cf-input-option-display"></span>
                    <span class="cf-input-label-wrap"><?php echo esc_html( $label ); ?></span>
                </label>
            <?php

            endforeach;
        endif;

        endif;
	}

    protected function render_email( $field, $field_required ) {
        ?>
        <label class="cf-field-label"><span class="cf-label-text"><?php echo esc_html( $this->get_label( $field ) . $this->get_required_symbol( $field_required ) ); ?></span><br/>
            <input type="text" class="cf-form-control"
                   name="<?php echo esc_attr( $this->get_field_name_attr( $field ) ); ?>"
                   placeholder="<?php echo esc_attr( $this->get_placeholder( $field ) ); ?>"/>
        </label>
    <?php
    }

    protected function render_url( $field, $field_required ) {
        ?>
        <label class="cf-field-label"><span class="cf-label-text"><?php echo esc_html( $this->get_label( $field ) . $this->get_required_symbol( $field_required ) ); ?></span><br/>
            <input type="text" class="cf-form-control"
                   name="<?php echo esc_attr( $this->get_field_name_attr( $field ) ); ?>"
                   placeholder="<?php echo esc_attr( $this->get_placeholder( $field ) ); ?>"/>
        </label>
    <?php
    }

	protected function get_field_id_attr( $field ) {
		return "cf_" . $field->id . "_" . $field->name;
	}

	protected function get_field_name_attr( $field ) {
		return "cf_field" . $field->id;
	}

	protected function get_label( $field ) {
		return $this->get_field_option( "label", $field );
	}

	protected function get_placeholder( $field ) {
		return $this->get_field_option( "placeholder", $field );
	}

	protected function get_custom_css( $field ) {
		return $this->get_field_option( "custom-css", $field );
	}

	protected function get_required( $field ) {
		$required = $this->get_field_option( "required", $field );

		return $required === "true" ? true : false;
	}

	protected function get_required_symbol( $field_required ) {
		return $field_required ? "*" : "";
	}

	public function get_field_option( $option_id, $field ) {
		$value = "";

		foreach ( $field->options as $id => $option ) {
			$id = array_keys( $option );
			$id = $id[0];

			if ( $id === $option_id ) {
				$value = $option[ $option_id ];
				break;
			}
		}

		return $value;
	}

	public function get_setting($key) {
	    $value = "";

        if (array_key_exists($key, $this->form_settings)) {
            $value = $this->form_settings[$key];
        }

        return $value;
	}

	public function get_style_setting($key) {
		$value = "";

		if (array_key_exists($key, $this->form_style_settings)) {
			$value = $this->form_style_settings[$key];
		}

		return $value;
	}
	
	public function check_bool_setting($key) {
		return $this->check_bool($key, $this->form_settings);
	}

	public function check_bool_style_setting($key) {
		return $this->check_bool($key, $this->form_style_settings);
	}
	
	private function check_bool($key, $pool) {
		return array_key_exists($key, $pool) &&
		       filter_var($pool[$key], FILTER_VALIDATE_BOOLEAN);
	}

	public function check_setting($key) {
		return $this->check_value($key, $this->form_settings);
	}

	public function check_style_setting($key) {
		return $this->check_value($key, $this->form_style_settings);
	}

	private function check_value($key, $pool) {
		return array_key_exists($key, $pool) && trim($pool[$key]) !== "";
	}

	public function get_field_by_id($id) {
		$requested_field = null;

		foreach($this->form->fields as $field) {
			if ($id === $field->id) {
				$requested_field = $field;
				break;
			}
		}

		return $requested_field;
	}

	private function shadeColor( $color, $percent ) {
		$num = base_convert(substr($color, 1), 16, 10);
		$amt = round(2.55 * $percent);
		$r = ($num >> 16) + $amt;
		$b = ($num >> 8 & 0x00ff) + $amt;
		$g = ($num & 0x0000ff) + $amt;

		return "#".substr(base_convert(0x1000000 + ($r<255?$r<1?0:$r:255)*0x10000 + ($b<255?$b<1?0:$b:255)*0x100 + ($g<255?$g<1?0:$g:255), 10, 16), 1);
	}
}