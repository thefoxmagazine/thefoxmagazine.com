<?php
/*
Plugin Name: Meow Gallery
Plugin URI: http://apps.meow.fr
Description: Gallery system built for photographers.
Version: 0.0.3
Author: Jordy Meow
Author URI: http://apps.meow.fr
Text Domain: meow-gallery
Domain Path: /languages

Dual licensed under the MIT and GPL licenses:
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html

Originally developed for two of my websites:
- Jordy Meow (http://jordymeow.com)
- Haikyo (http://www.haikyo.org)
*/

include "mgl_core.php";

global $wplr;
$wplr = new Meow_Gallery_Core;

if ( is_admin() ) {
	include "mgl_admin.php";
	new Meow_Gallery_Admin;
}

?>
