<?php

include "common/meow_admin.php";

class Meow_Lightbox_Admin extends Meow_Admin {

	public function __construct() {
		parent::__construct();
		add_action( 'admin_menu', array( $this, 'app_menu' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	function admin_notices() {

	}

	function common_url( $file ) {
		return trailingslashit( plugin_dir_url( __FILE__ ) ) . 'common/' . $file;
	}

	function app_menu() {

		// SUBMENU > Settings
		add_submenu_page( 'meowapps-main-menu', 'Lightbox', 'Lightbox', 'manage_options',
			'mwl_settings-menu', array( $this, 'admin_settings' ) );

			// SUBMENU > Settings > Settings
			add_settings_section( 'mwl_settings', null, null, 'mwl_settings-menu' );
			add_settings_field( 'mwl_layout', "Layout",
				array( $this, 'admin_layout_callback' ),
				'mwl_settings-menu', 'mwl_settings' );
			add_settings_section( 'mwl_settings', null, null, 'mwl_settings-menu' );
			add_settings_field( 'mwl_theme', "Theme",
				array( $this, 'admin_theme_callback' ),
				'mwl_settings-menu', 'mwl_settings' );

			// SUBMENU > Settings > Settings
			add_settings_section( 'mwl_advanced', null, null, 'mwl_settings-menu-advanced' );
			add_settings_field( 'mwl_selector', "Selector",
				array( $this, 'admin_selector_callback' ),
				'mwl_settings-menu-advanced', 'mwl_advanced' );

		// SETTINGS
		register_setting( 'mwl_settings', 'mwl_layout' );
		register_setting( 'mwl_settings', 'mwl_theme' );
		register_setting( 'mwl_settings-advanced', 'mwl_selector' );
	}

	function admin_settings() {
		?>
		<div class="wrap meow-admin">
		<?php echo $this->display_title( "Meow Lightbox" , "By Jordy Meow & Thomas Kim");  ?>
		<p>This lightbox will display your photography beautifully.</p>

		<div class="meow-row">

			<div class="meow-box meow-col meow-span_1_of_2">
				<form method="post" action="options.php">
						<h3><span class="dashicons dashicons-admin-tools"></span> DISPLAY</h3>
						<div class="inside">
							<?php settings_fields( 'mwl_settings' ); ?>
							<?php do_settings_sections( 'mwl_settings-menu' ); ?>
							<?php submit_button(); ?>
						</div>
				</form>
			</div>

			<div class="meow-box meow-col meow-span_1_of_2">
				<form method="post" action="options.php">
					<h3><span class="dashicons dashicons-admin-tools"></span> ADVANCED</h3>
					<div class="inside">
						<?php settings_fields( 'mwl_settings-advanced' ); ?>
						<?php do_settings_sections( 'mwl_settings-menu-advanced' ); ?>
						<?php submit_button(); ?>
					</div>
				</form>
			</div>

		</div>


		</div>
		<?php
	}

	/*
		OPTIONS CALLBACKS
	*/

	function admin_layout_callback( $args ) {
		$layouts = array(
			'photography' => array( 'name' => 'Photography (default)', 'desc' => "Display your photo on the left and its EXIF information on the right." ),
			'standard' => array( 'name' => 'Standard', 'desc' => "Full-screen, similar as most lightboxes." ),
			'minimal' => array( 'name' => 'Minimal', 'desc' => "Very light and simple." ),
		);
		$html = '';
		foreach ( $layouts as $key => $arg )
			$html .= '<input type="radio" class="radio" id="mwl_layout" name="mwl_layout" value="' . $key . '"' .
				checked( $key, get_option( 'mwl_layout', 'photography' ), false ) . ' > '  .
				( empty( $arg ) ? 'None' : $arg['name'] ) .
				( empty( $arg ) ? '' : '<br/><small>' . $arg['desc'] . '</small>' ) .
				'<br /><br />';
		echo $html;
	}

	function admin_theme_callback( $args ) {
		$themes = array(
			'dark' => array( 'name' => 'Dark (default)', 'desc' => "" ),
			'light' => array( 'name' => 'Light', 'desc' => "" ),
		);
		$html = '';
		foreach ( $themes as $key => $arg )
			$html .= '<input type="radio" class="radio" id="mwl_theme" name="mwl_theme" value="' . $key . '"' .
				checked( $key, get_option( 'mwl_theme', 'dark' ), false ) . ' > '  .
				( empty( $arg ) ? 'None' : $arg['name'] ) .
				( empty( $arg ) ? '' : '<br/><small>' . $arg['desc'] . '</small>' );
		echo $html;
	}

	function admin_selector_callback( $args ) {
    $value = get_option( 'mwl_selector', '.entry-content' );
    $html = '<input type="text" id="mwl_selector" name="mwl_selector" value="' . $value . '" />';
    $html .= '<br /><span class="description">This selector will be used to apply the lightbox to the images.</label>';
    echo $html;
  }

	// function admin_takendate_callback( $args ) {
	// 	$html = '<input type="checkbox" id="wplr_use_taken_date" name="wplr_use_taken_date" value="1" ' .
	// 		checked( 1, get_option( 'wplr_use_taken_date' ), false ) . '/>';
	// 	$html .= '<label for="wplr_use_taken_date"> '  . $args[0] . '</label><br>';
	// 	$html .= '<span class="description">The date of the Media will not be the time when it was added to the Media Library but the the time when the photo was taken. ' . ( function_exists( 'exif_read_data' ) ? "EXIF functions are enabled on your server." : "EXIF functions are <b>NOT</b> enabled on your server." ) . '</span>';
	// 	echo $html;
	// }

}

?>
