<?php

class Meow_Lightbox_Run {

	public function __construct() {

		$home_url = function_exists( 'pll_home_url' ) ? pll_home_url() : get_home_url();

    wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'mwl-core-js', plugins_url( '/js/mwl-core.js', __FILE__ ),
			array( 'jquery' ), '0.0.8', false );
		wp_enqueue_script( 'mwl-run-js', plugins_url( '/js/mwl-run.js', __FILE__ ),
			array( 'jquery', 'mwl-core-js'), '0.0.8', false );
		wp_localize_script('mwl-run-js', 'mwl', array(
			'plugin_url' => plugin_dir_url(__FILE__),
			'url_api' => $home_url . '/wp-json/mwl/v1/',
			'settings' => array(
				'layout' => get_option( 'mwl_layout', 'photography' ),
				'theme' => get_option( 'mwl_theme', 'dark' ),
				'selector' => get_option( 'mwl_selector', '.entry-content' )
			)
		) );

    wp_enqueue_style( 'mwl-css', plugin_dir_url( __FILE__ ) . 'css/mwl.css', null, '0.0.8', 'screen' );
    wp_enqueue_style( 'ionicons', '//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' );
		add_action( 'rest_api_init', array( $this, 'init_rest' ) );
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'wp_get_attachment_image_attributes' ), 10, 2 );
	}

	function wp_get_attachment_image_attributes( $attr, $attachment ) {
    if ( empty( $attr['mwl-img-id'] ) ) {
			$attr['mwl-img-id'] = $attachment->ID;
		}
    return $attr;
	}

	function init_rest() {
		register_rest_route( 'mwl/v1', '/info/(?P<id>[0-9-]+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'exif_from_id' ),
			'args' => array(
				'in' => array(
				)
			)
		) );
  }

	function nice_shutter_speed( $shutter_speed ) {
		$str = "";
		if ( ( 1 / $shutter_speed ) > 1) {
			$str .= "1/";
			if ( number_format( ( 1 / $shutter_speed ), 1) ==  number_format( ( 1 / $shutter_speed ), 0 ) )
				$str .= number_format( ( 1 / $shutter_speed ), 0, '.', '' ) . '';
			else
				$str .= number_format( ( 1 / $shutter_speed ), 0, '.', '' ) . '';
		}
		else
			$str .= $shutter_speed . ' sec';
		return $str;
	}

	function exif_from_id( $data ) {
		$id = (int)$data['id'];
		$meta = wp_get_attachment_metadata( $id );
		$p = get_post( $id );

		if ( empty( $meta ) || empty( $p ) ) {
			$message = "No meta was found for this ID.";
			if ( !wp_attachment_is_image( $id ) )
				$message = "This attachment does not exist or is not an image.";
			echo json_encode( array(
				'success' => false,
				'message' => $message
			) );
		}
		else {

			if ( !isset( $meta['image_meta']['lens'] ) ) {
				$file = get_attached_file( $id );
				$exif = exif_read_data( $file );
				$meta['image_meta']['lens'] = empty( $exif['UndefinedTag:0xA434'] ) ? "" : $exif['UndefinedTag:0xA434'];
				wp_update_attachment_metadata( $id, $meta );
			}

			$title = $p->post_title;
			$caption =  $p->post_excerpt;
			$description = $p->post_content;
			//$alt_text = get_post_meta( $id, '_wp_attachment_image_alt', true );
			echo json_encode( array(
				'success' => true,
				'file' => wp_get_attachment_url( $id ),
				'file_srcset' => wp_get_attachment_image_srcset( $id ),
				'file_sizes' => wp_get_attachment_image_sizes( $id ),
				'dimension' => array( 'width' => $meta['width'], 'height' => $meta['height'] ),
				'data' => array(
					'id' => (int)$data['id'],
					// 'title' => empty( $meta['image_meta']['title'] ) ? "N/A" : $meta['image_meta']['title'],
					// 'caption' => empty( $meta['image_meta']['caption'] ) ? "N/A" : $meta['image_meta']['caption'],
					'title' => !empty( $p->post_title ) ? $p->post_title : "",
					'caption' => !empty( $caption ) ? $caption : "",
					'description' => !empty( $description ) ? $description : "",
					'copyright' => empty( $meta['image_meta']['copyright'] ) ? "N/A" : $meta['image_meta']['copyright'],
					'camera' => $this->nice_camera( empty( $meta['image_meta']['camera'] ) ? "N/A" : $meta['image_meta']['camera'] ),
					'lens' => $this->nice_lens( empty( $meta['image_meta']['lens'] ) ? "N/A" : $meta['image_meta']['lens'] ),
					'aperture' => empty( $meta['image_meta']['aperture'] ) ? "N/A" : "f/" . $meta['image_meta']['aperture'],
					'focal_length' => empty( $meta['image_meta']['focal_length'] ) ? "N/A" : round( $meta['image_meta']['focal_length'], 0 ) . "mm",
					'iso' => empty( $meta['image_meta']['iso'] ) ? "N/A" : "ISO " . $meta['image_meta']['iso'],
					'shutter_speed' => empty( $meta['image_meta']['shutter_speed'] ) ? "N/A" : $this->nice_shutter_speed( $meta['image_meta']['shutter_speed'] )
				)
			) );
		}
		exit;
	}

	function exif_from_url( $data ) {
	}

	// This function will be improved over time
	function nice_lens( $lens ) {
		if ( empty( $lens ) )
			return $lens;
		$lenses = array(
			"70.0-200.0 mm f/2.8" => "70-200mm f/2.8",
			"----" => ""
		);
		if ( isset( $lenses[$lens] ) )
			return $lenses[$lens];
		else
			return $lens;
	}

	// This function will be improved over time
	function nice_camera( $camera ) {
		if ( empty( $camera ) )
			return $camera;
		$cameras = array(
			"ILCE-7RM2" => "SONY α7R II"
		);
		if ( isset( $cameras[$camera] ) )
			return $cameras[$camera];
		else
			return $camera;
	}

}

?>
