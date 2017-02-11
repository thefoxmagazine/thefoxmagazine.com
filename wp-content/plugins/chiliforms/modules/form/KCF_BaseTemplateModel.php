<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */
abstract class KCF_BaseTemplateModel {

	public function __construct() {

	}

	/**
	 * Gets template for given input type and template
	 * @param $input_type
	 * @param string $template_name
	 *
	 * @return string
	 */
	public static function get_template($input_type, $template_name = 'html5') {
		$templates_map = self::get_templates_map();

		$result = null;
		$template_method_name = '_get_' . $template_name . '_' . $templates_map[$input_type];

		if (method_exists(__CLASS__, $template_method_name)) {
			$result = self::_get_template_string_from_method($template_method_name);
		} else if ($template_name !== 'html5') {
			$template_method_name = '_get_html5_' . $templates_map[$input_type];
			if (method_exists(__CLASS__, $template_method_name)) {
				$result = self::_get_template_string_from_method($template_method_name);
			}
		}

		return $result;
	}

	public static function get_all_templates() {
		$templates = array();
		$template_names = self::get_templates_name_map();
		$templates_map = self::get_templates_map();

		foreach($template_names as $template_name) {
			$templates[$template_name] = array();

			foreach($templates_map as $input_type => $render_method) {
				$templates[$template_name][$input_type] = self::get_template($input_type, $template_name);
			}
		}

		return $templates;
	}

	protected static function get_templates_name_map() {
		return array (
			'html5',
			'wrapped'
		);
	}

	protected static function get_templates_map() {
		return array(
			'single-line-input' => 'input',
			'multi-line-input' => 'textarea',
			'dropdown' => 'select',
			'checkbox' => 'checkbox',
			'radio' => 'radio',
			'email' => 'email',
			'url' => 'url'
		);
	}

	protected static function _get_template_string_from_method($template_method_name) {
		ob_start();
		call_user_func(array(__CLASS__, $template_method_name));
		$result = ob_get_clean();

		return str_replace(array("\n", "\r"), '', preg_replace('/(?<=\n)(\s+)/', '', $result));
	}

	protected static function _get_control_class_name() {
		return 'cf-form-control';
	}

	protected static function _get_html5_input() {
		?><label><span class="cf-label-text">{{LABEL}}{{REQUIRED_SYMBOL}}</span><br/>
			<input type="text" class="<?php echo esc_attr( self::_get_control_class_name() ); ?>"
			       name="{{NAME}}" placeholder="{{PLACEHOLDER}}"/>
		</label><?php
	}

    protected static function _get_wrapped_input() {
        ?><label><span class="cf-label-text">{{LABEL}}{{REQUIRED_SYMBOL}}</span><br/>
            <input type="text" class="<?php echo esc_attr( self::_get_control_class_name() ); ?>"
                   name="{{NAME}}" placeholder="{{PLACEHOLDER}}"/>
        </label><?php
    }

	protected static function _get_html5_textarea() {
		?><label><span class="cf-label-text">{{LABEL}}{{REQUIRED_SYMBOL}}</span><br/>
			<textarea class="<?php echo esc_attr( self::_get_control_class_name() ); ?>"
			          name="{{NAME}}"
			          rows="{{OPTION(rows)}}"
			          placeholder="{{PLACEHOLDER}}"></textarea>
		</label><?php
	}

	protected static function _get_html5_email() {
		self::_get_html5_input();
	}

	protected static function _get_html5_url() {
		self::_get_html5_input();
	}

	protected static function _get_html5_select() {
		?>
		<label><span class="cf-label-text">{{LABEL}}{{REQUIRED_SYMBOL}}</span><br/>
			<select class="<?php echo esc_attr( self::_get_control_class_name() ); ?>"
			        name="{{NAME}}">
				<option value="">{{PLACEHOLDER}}</option>
				{{MAP(options: value, key)}}
				<option value="{{value.optionKey}}">{{value.optionLabel}}</option>
				{{ENDMAP}}
			</select>
		</label>
	<?php
	}

    protected static function _get_wrapped_select() {
        ?>
        <label><span class="cf-label-text">{{LABEL}}{{REQUIRED_SYMBOL}}</span><br/>
            <select class="<?php echo esc_attr( self::_get_control_class_name() ); ?> cf-wrapped-select__original"
                    name="{{NAME}}">
                <option value="">{{PLACEHOLDER}}</option>
                {{MAP(options: value, key)}}
                <option value="{{value.optionKey}}">{{value.optionLabel}}</option>
                {{ENDMAP}}
            </select>

            <div class="cf-wrapped-select">
                <div class="cf-wrapped-select__control">
                    <div class="cf-wrapped-select__current">{{PLACEHOLDER}}</div>
                    <span class="cf-wrapped-select__arrow-box"></span>
                </div>
                <div class="cf-wrapped-select__options">
                    <div data-value="" class="cf-wrapped-select__option">{{PLACEHOLDER}}</div>
                    {{MAP(options: value, key)}}
                        <div data-value="{{value.optionKey}}" class="cf-wrapped-select__option">{{value.optionLabel}}</div>
                    {{ENDMAP}}
                </div>
            </div>
        </label>
    <?php
    }
	
	protected static function _get_html5_checkbox() {
		?>
		<label><span class="cf-label-text">{{LABEL}}{{REQUIRED_SYMBOL}}</span><br/></label>
		{{MAP(options: value, key)}}
			<label class="cf-option-label">
				<input type="checkbox"
				       class="<?php echo esc_attr( self::_get_control_class_name() ); ?>"
				       name="{{value.optionKey}}"/>&nbsp;
				<span class="cf-input-label-wrap">{{value.optionLabel}}</span>
			</label>
		{{ENDMAP}}
		<?php
	}

    protected static function _get_wrapped_checkbox() {
        ?>
        <label><span class="cf-label-text">{{LABEL}}{{REQUIRED_SYMBOL}}</span><br/></label>
        {{MAP(options: value, key)}}
            <label class="cf-option-label">
                <input type="checkbox"
                       class="<?php echo esc_attr( self::_get_control_class_name() ); ?>"
                       name="{{value.optionKey}}"/>&nbsp;
                <span class="cf-input-option-display"></span>
                <span class="cf-input-label-wrap">{{value.optionLabel}}</span>
            </label>
        {{ENDMAP}}
        <?php
    }

	protected static function _get_html5_radio() {
		?>
		<label><span class="cf-label-text">{{LABEL}}{{REQUIRED_SYMBOL}}</span><br/></label>
		{{MAP(options: value, key)}}
		<label class="cf-option-label">
			<input type="radio"
			       class="<?php echo esc_attr( self::_get_control_class_name() ); ?>"
			       name="{{NAME}}"
			       value="{{value.optionKey}}"/>&nbsp;
			<span class="cf-input-label-wrap">{{value.optionLabel}}</span>
		</label>
		{{ENDMAP}}
	<?php
	}

    protected static function _get_wrapped_radio() {
        ?>
        <label><span class="cf-label-text">{{LABEL}}{{REQUIRED_SYMBOL}}</span><br/></label>
        {{MAP(options: value, key)}}
        <label class="cf-option-label">
            <input type="radio"
                   class="<?php echo esc_attr( self::_get_control_class_name() ); ?>"
                   name="{{NAME}}"
                   value="{{value.optionKey}}"/>&nbsp;
            <span class="cf-input-option-display"></span>
            <span class="cf-input-label-wrap">{{value.optionLabel}}</span>
        </label>
        {{ENDMAP}}
    <?php
    }
}