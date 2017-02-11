<?php 
/*
  Plugin Name: Category and Post Tab
  Description: Category and Post Tab
  Author: ikhodal team
  Plugin URI: http://www.ikhodal.com/category-and-post-tab/
  Author URI: http://www.ikhodal.com/category-and-post-tab/
  Version: 1.0
  License: GNU General Public License v2.0
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/ 
  
  
//////////////////////////////////////////////////////
// Defines the constants for use within the plugin. //
////////////////////////////////////////////////////// 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
/**
* Widget/Block Title
*/
define( 'cpt_widget_title', __( 'Category & Post View', 'categoryposttab') );
 
/**
* Default category selection for fist post load in widget
*/
define( 'cpt_category', '0' );

/**
* Number of posts per next loading result
*/
define( 'cpt_number_of_post_display', '2' ); 
 
/**
* Category tab text color 
*/
define( 'cpt_category_tab_text_color', '#000' );

/**
* Post title text color
*/
define( 'cpt_title_text_color', '#000' );

/**
* Category tab background color
*/
define( 'cpt_category_tab_background_color', '#f7f7f7' );

/**
* Widget/block header text color
*/
define( 'cpt_header_text_color', '#fff' );

/**
* Widget/block header text background color
*/
define( 'cpt_header_background_color', '#00bc65' );

/**
* Display post title and text over post image
*/
define( 'cpt_display_title_over_image', 'no' );

/**
* Widget/block width
*/
define( 'cpt_widget_width', '100%' );  

/**
* Hide/Show widget title
*/
define( 'cpt_hide_widget_title', 'no' ); 

/**
* Template for widget/block
*/
define( 'cpt_template', 'pane_style_1' ); 

/**
* Hide/Show post title
*/
define( 'cpt_hide_post_title', 'no' );  

/**
* Security key for block id
*/
define( 'cpt_security_key', 'CPT_#s@R$@ASI#TA(!@@21M3' );
 
/**
*  Assets for tab for category and posts
*/
$cpt_plugins_url = plugins_url( "/assets/", __FILE__ );

define( 'CPT_MEDIA', $cpt_plugins_url ); 

/**
*  Plugin DIR
*/
$cpt_plugin_DIR = plugin_basename( dirname(__FILE__) );

define( 'CPT_Plugin_DIR', $cpt_plugin_DIR ); 
 
/**
 * Include abstract class for common methods
 */
require_once 'include/abstract.php';


///////////////////////////////////////////////////////
// Include files for widget and shortcode management //
///////////////////////////////////////////////////////

/**
 * Admin panel widget configuration
 */ 
require_once 'include/admin.php';

/**
 * Load Category and Post Tab on frontent pages
 */
require_once 'include/categoryposttab.php';  
 