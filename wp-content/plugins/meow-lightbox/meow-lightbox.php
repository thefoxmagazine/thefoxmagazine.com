<?php
/*
Plugin Name: Meow Lightbox
Plugin URI: http://meowapps.com/meow-lightbox
Description: Lightbox designed by and for photographers.
Version: 0.0.8
Author: Jordy Meow, Thomas KIM
Author URI: http://meowapps.com
Text Domain: meow-lightbox
Domain Path: /languages

Dual licensed under the MIT and GPL licenses:
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html
*/

include "mwl_core.php";

function be_attachment_id_on_images( $attr, $attachment ) {
	if( !strpos( $attr['class'], 'wp-image-' . $attachment->ID ) )
		$attr['class'] .= ' wp-image-' . $attachment->ID;
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'be_attachment_id_on_images', 10, 2 );

new Meow_Lightbox_Core;
