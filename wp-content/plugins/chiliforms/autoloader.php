<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

function kcf_autoloader($class_name) {
	$modules = array(
		'app',
		'actions',
		'db',
		'i18n',
		'field',
		'options',
		'helpers',
		'form',
		'pages',
		'pages' . DIRECTORY_SEPARATOR . 'entries',
		'pages' . DIRECTORY_SEPARATOR . 'options',
		'pages' . DIRECTORY_SEPARATOR . 'forms'
	);

	$folders = array();

	foreach ($modules as $module) {
		array_push($folders, KCF_PLUGIN_PATH . "modules" . DIRECTORY_SEPARATOR . $module);
	}

	foreach ($folders as $folder) {
		$full_path = $folder . DIRECTORY_SEPARATOR . "$class_name.php";

		if (file_exists($full_path)) {
			require_once $full_path;
		}
	}
}
spl_autoload_register('kcf_autoloader');