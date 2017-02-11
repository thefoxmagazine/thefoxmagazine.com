<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

function chiliforms_put_form($form_id_string) {
	if (!$form_id_string) {
		return;
	}

	$form_renderer = new KCF_FormModel(KCF_DbModel::get_form_by_id($form_id_string));

	$form_renderer->render();
}

function kcf_put_form_shortcode( $atts ) {
	ob_start();

	chiliforms_put_form($atts['id']);

	return ob_get_clean();
}
add_shortcode( 'chiliforms', 'kcf_put_form_shortcode' );