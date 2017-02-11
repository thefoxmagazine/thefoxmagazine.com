<?php

class Meow_Gallery_Run {

	public function __construct() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'imagesLoaded', plugins_url( '/js/imagesloaded.min.js', __FILE__ ), array( 'jquery' ), '0.0.1', false );
    wp_enqueue_script( 'justified', plugins_url( '/js/justified.js', __FILE__ ), array('jquery', 'imagesLoaded'), '0.0.1', false );
    wp_enqueue_script( 'masonry', plugins_url( '/js/masonry.min.js', __FILE__ ), array('jquery'), '0.0.1', false );
		wp_enqueue_script( 'mgl-js', plugins_url( '/js/mgl.js', __FILE__ ), array( 'jquery', 'justified', 'masonry', 'imagesLoaded' ), '0.0.1', false );
		wp_localize_script('mgl-js', 'mgl', array(
			//'url_api' => get_site_url() . '/wp-json/mgl/v1/',
			'settings' => array(
				'layout' => get_option( 'mgl_layout', 'masonry' ),
				'masonry' => array(
					'columns' => get_option( 'mgl_masonry_columns', 3 ),
					'gutter' => get_option( 'mgl_masonry_gutter', 10 )
				),
				'justified' => array(
					'gutter' => get_option( 'mgl_justified_gutter', 10 )
				)
			)
		) );
    wp_enqueue_style( 'mgl-css', plugin_dir_url( __FILE__ ) . 'css/mgl.css' );
    //wp_enqueue_style( 'ionicons', '//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' );

		add_shortcode( 'gallery', array( $this, 'gallery_shortcode' ) );
	}

	function gallery_shortcode( $attr ) {
		$post = get_post();
		static $instance = 0;
		$instance++;
		if ( ! empty( $attr['ids'] ) ) {
	    // 'ids' is explicitly ordered, unless you specify otherwise.
	    if ( empty( $attr['orderby'] ) ) {
	            $attr['orderby'] = 'post__in';
	    }
	    $attr['include'] = $attr['ids'];
		}
		$html5 = current_theme_supports( 'html5', 'gallery' );
		$default_size = get_option( 'mgl_default_size', 'thumbnail' );
		$enable_caption = get_option( 'mgl_enable_caption', false );
		$atts = shortcode_atts( array(
		        'order'      => 'ASC',
		        'orderby'    => 'menu_order ID',
		        'id'         => $post ? $post->ID : 0,
		        'itemtag'    => $html5 ? 'figure'     : 'dl',
		        'icontag'    => $html5 ? 'div'        : 'dt',
		        'captiontag' => $html5 ? 'figcaption' : 'dd',
		        'columns'    => 3,
		        'size'       => $default_size,
		        'include'    => '',
		        'exclude'    => '',
		        'link'       => ''
		), $attr, 'gallery' );

		$id = intval( $atts['id'] );

		if ( ! empty( $atts['include'] ) ) {
		        $_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );

		        $attachments = array();
		        foreach ( $_attachments as $key => $val ) {
		                $attachments[$val->ID] = $_attachments[$key];
		        }
		} elseif ( ! empty( $atts['exclude'] ) ) {
		        $attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		} else {
		        $attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		}

		if ( empty( $attachments ) ) {
		        return '';
		}

		if ( is_feed() ) {
		        $output = "\n";
		        foreach ( $attachments as $att_id => $attachment ) {
		                $output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
		        }
		        return $output;
		}

		$itemtag = tag_escape( $atts['itemtag'] );
		$captiontag = tag_escape( $atts['captiontag'] );
		$icontag = tag_escape( $atts['icontag'] );
		$valid_tags = wp_kses_allowed_html( 'post' );
		if ( ! isset( $valid_tags[ $itemtag ] ) ) {
		        $itemtag = 'dl';
		}
		if ( ! isset( $valid_tags[ $captiontag ] ) ) {
		        $captiontag = 'dd';
		}
		if ( ! isset( $valid_tags[ $icontag ] ) ) {
		        $icontag = 'dt';
		}

		$columns = intval( $atts['columns'] );
		$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
		$float = is_rtl() ? 'right' : 'left';
		$selector = "gallery-{$instance}";
		$gallery_style = '';

		$size_class = sanitize_html_class( $atts['size'] );
		$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

		/**
		 * Filters the default gallery shortcode CSS styles.
		 *
		 * @since 2.5.0
		 *
		 * @param string $gallery_style Default CSS styles and opening HTML div container
		 *                              for the gallery shortcode output.
		 */
		$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

		$i = 0;
		foreach ( $attachments as $id => $attachment ) {

	    $attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
	    if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
	            $image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
	    } elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
	            $image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
	    } else {
	            $image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
	    }
	    $image_meta  = wp_get_attachment_metadata( $id );

	    $orientation = '';
	    if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
	            $orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
	    }
	    $output .= "<{$itemtag} class='gallery-item'>";
	    $output .= "
	            <{$icontag} class='gallery-icon {$orientation}'>
	                    $image_output
	            </{$icontag}>";
	    if ( $enable_caption && $captiontag && trim($attachment->post_excerpt) ) {
	            $output .= "
	                    <{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
	                    " . wptexturize($attachment->post_excerpt) . "
	                    </{$captiontag}>";
	    }
	    $output .= "</{$itemtag}>";
	    if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
	            $output .= '<br style="clear: both" />';
	    }
		}

		if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
		        $output .= "
		                <br style='clear: both' />";
		}

		$output .= "
		        </div>\n";

		return $output;
}

}

?>
