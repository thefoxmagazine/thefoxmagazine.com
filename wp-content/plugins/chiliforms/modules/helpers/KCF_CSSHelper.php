<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */ 

class KCF_CSSHelper {
	
	private $id;
	/**
	 *
	 * array(
			array(
	 *          "selectors" => array(".some-rule", ".another-rule"),
	 *          "rules" => array("background-size: red", "margin: 0 auto")
	 *      )
	 * )
	 * @var array
	 */
	private $rules = array();
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function add($selectors, $rules) {
		$this->push_rule(array(
			"selectors" => $this->parse_css_rules($selectors),
			"rules" => $this->parse_css_rules($rules)
		));
	}

	public function render_styles() {
		echo $this->get_styles();
	}

	public function get_styles() {
		ob_start();

		foreach($this->rules as $rule):
			$this->render_ruleset($rule);
		endforeach;

		$css = ob_get_clean();

		return $css;
	}

	public function dump_styles() {
		var_dump($this->rules);
	}

	private function render_ruleset($rule_set) {
		echo $this->get_selectors_string($rule_set) . '{' . $this->get_rules_string($rule_set)  . '} ';
	}

	private function get_selectors_string($rule_set) {
		return implode(', ', array_map(array($this, 'prefixed_selector'), $rule_set["selectors"]));
	}

	private function get_rules_string($rule_set) {
		return implode(';', array_filter($rule_set["rules"], function($rule) {
			return trim($rule) !== "";
		})) . ';';
	}
	
	private function push_rule($rule_set) {
		if (isset($rule_set["selectors"]) && isset($rule_set["rules"])) {
			array_push($this->rules, $rule_set);
		}
	}

	private function parse_css_rules($rules) {
		if (is_string($rules)) {
			return array($rules);
		} else if (is_array($rules) && count($rules)) {
			return $rules;
		} else {
			return null;
		}
	}
	
	private function prefixed_selector($selector) {
		return '#' . $this->id . ' ' . $selector;
	}
}