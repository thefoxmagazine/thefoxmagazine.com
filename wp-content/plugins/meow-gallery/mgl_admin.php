<?php

include "common/meow_admin.php";

class Meow_Gallery_Admin extends Meow_Admin {

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
		add_submenu_page( 'meowapps-main-menu', 'Gallery', 'Gallery', 'manage_options',
			'mgl_settings-menu', array( $this, 'admin_settings' ) );

			// SUBMENU > Settings > Settings
			add_settings_section( 'mgl_settings', null, null, 'mgl_settings-menu' );
			add_settings_field( 'mgl_layout', "Layout",
				array( $this, 'admin_layout_callback' ),
				'mgl_settings-menu', 'mgl_settings' );
			add_settings_field( 'mgl_default_size', "Default Size",
				array( $this, 'admin_default_size_callback' ),
				'mgl_settings-menu', 'mgl_settings' );

			$layout = get_option( 'mgl_layout', 'masonry' );

			// SUBMENU > Settings > Settings
			if ( $layout == 'masonry' ) {
				add_settings_section( 'mgl_masonry', null, null, 'mgl_settings_masonry-menu' );
				add_settings_field( 'mgl_masonry_columns', "Columns",
					array( $this, 'admin_masonry_columns_callback' ),
					'mgl_settings_masonry-menu', 'mgl_masonry',
					array( "Number of columns" ) );
				add_settings_field( 'mgl_masonry_gutter', "Gutter",
					array( $this, 'admin_masonry_gutter_callback' ),
					'mgl_settings_masonry-menu', 'mgl_masonry' );
				register_setting( 'mgl_settings', 'mgl_masonry_columns' );
				register_setting( 'mgl_settings', 'mgl_masonry_gutter' );
			}
			// SUBMENU > Settings > Settings
			else if ( $layout == 'justified' ) {
				add_settings_section( 'mgl_justified', null, null, 'mgl_settings_justified-menu' );
				add_settings_field( 'mgl_justified_gutter', "Gutter",
					array( $this, 'admin_justified_gutter_callback' ),
					'mgl_settings_justified-menu', 'mgl_justified' );
				register_setting( 'mgl_settings', 'mgl_justified_gutter' );
			}

		// SETTINGS
		register_setting( 'mgl_settings', 'mgl_layout' );
		register_setting( 'mgl_settings', 'mgl_default_size' );
	}

	function admin_settings() {
		?>
		<div class="wrap">
			<?php echo $this->display_title( "Meow Gallery" , "By Jordy Meow & Thomas Kim");  ?>
			<p>This gallery plugin is designed for photographers, by photographers. If you have ideas or feature requests, don't hesitate to contact us.</p>
			<div class="section group">
				<div class="meow-box col span_2_of_2">
					<h3>How to use</h3>
					<div class="inside">
						<?php echo _e( "The Meow Gallery simply re-uses the standard WP Gallery and enhances it. You can create a gallery in a post or in a page by using the <b>Add Media</b> button and then the <b>Create Gallery</b> button.
						While the default options should be fine, you can modify them below.", 'meow-gallery' ) ?>
					</div>
				</div>
			</div>
			<div class="section group">
				<form method="post" action="options.php">

					<div class="meow-box col span_1_of_2">
						<h3>Display</h3>
						<div class="inside">
							<?php settings_fields( 'mgl_settings' ); ?>
					    <?php do_settings_sections( 'mgl_settings-menu' ); ?>
					    <?php submit_button(); ?>
						</div>
					</div>

					<?php if ( get_option( 'mgl_layout', 'masonry' ) == 'masonry' ): ?>
					<div class="meow-box col span_1_of_2">
						<h3>Masonry</h3>
						<div class="inside">
							<?php do_settings_sections( 'mgl_settings_masonry-menu' ); ?>
					    <?php submit_button(); ?>
						</div>
					</div>
					<?php endif; ?>

					<?php if ( get_option( 'mgl_layout', 'masonry' ) == 'justified' ): ?>
					<div class="meow-box col span_1_of_2">
						<h3>Justified</h3>
						<div class="inside">
							<?php do_settings_sections( 'mgl_settings_justified-menu' ); ?>
					    <?php submit_button(); ?>
						</div>
					</div>
					<?php endif; ?>

				</form>
			</div>
		</div>
		<?php
	}

	/*
		OPTIONS CALLBACKS
	*/

	function admin_layout_callback( $args ) {
		$layouts = array(
			'masonry' => array( 'name' => 'Masonry', 'desc' => "Display your photos using Masonry." ),
			'justified' => array( 'name' => 'Justified', 'desc' => "Justified by row." ),
		);
		$html = '';
		foreach ( $layouts as $key => $arg )
			$html .= '<input type="radio" class="radio" id="mgl_layout" name="mgl_layout" value="' . $key . '"' .
				checked( $key, get_option( 'mgl_layout', 'masonry' ), false ) . ' > '  .
				( empty( $arg ) ? 'None' : $arg['name'] ) .
				( empty( $arg ) ? '' : '<br/><small>' . $arg['desc'] . '</small>' ) .
				'<br /><br />';
		echo $html;
	}

	function admin_default_size_callback( $args ) {
		$layouts = array(
			'thumbnail' => array( 'name' => 'Thumbnail', 'desc' => "" ),
			'medium' => array( 'name' => 'Medium', 'desc' => "" ),
			'large' => array( 'name' => 'Large', 'desc' => "" ),
			'full' => array( 'name' => 'Full', 'desc' => "" )
		);
		$html = '';
		foreach ( $layouts as $key => $arg )
			$html .= '<input type="radio" class="radio" id="mgl_default_size" name="mgl_default_size" value="' . $key . '"' .
				checked( $key, get_option( 'mgl_default_size', 'thumbnail' ), false ) . ' > '  .
				( empty( $arg ) ? 'None' : $arg['name'] ) .
				'<br />';
		echo $html;
	}

	function admin_masonry_columns_callback( $args ) {
    $value = get_option( 'mgl_masonry_columns', 3 );
    $html = '<input type="text" style="width: 260px;" id="mgl_masonry_columns" name="mgl_masonry_columns" value="' . $value . '" />';
    $html .= '<br /><span class="description">Number of columns (usually between 2 and 5).</label>';
    echo $html;
  }

	function admin_masonry_gutter_callback( $args ) {
    $value = get_option( 'mgl_masonry_gutter', 10 );
    $html = '<input type="text" style="width: 260px;" id="mgl_masonry_gutter" name="mgl_masonry_gutter" value="' . $value . '" />';
    $html .= '<br /><span class="description">Spacing in pixels between the photos.</label>';
    echo $html;
  }

	function admin_justified_gutter_callback( $args ) {
    $value = get_option( 'mgl_justified_gutter', 10 );
    $html = '<input type="text" style="width: 260px;" id="mgl_justified_gutter" name="mgl_justified_gutter" value="' . $value . '" />';
    $html .= '<br /><span class="description">Spacing in pixels between the photos.</label>';
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
