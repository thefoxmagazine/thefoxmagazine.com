<?php



// change default settings for default gallery
add_action( 'after_setup_theme', 'themeton_attachment_display_settings' );
function themeton_attachment_display_settings() {
    update_option( 'image_default_link_type', 'file' );
}


// Print global js variables
add_action('wp_head', 'print_theme_wp_head');
function print_theme_wp_head(){
    $playlist_trans = array(
        'art' => esc_html__('Art', 'tana'),
        'title' => esc_html__('Title', 'tana'),
        'artist' => esc_html__('Artist', 'tana'),
        'time' => esc_html__('Time', 'tana')
    );
    printf('<script>
                var theme_options = { ajax_url: "%s" };
                var themeton_playlist_label = %s;
            </script>', esc_url(admin_url('admin-ajax.php')), json_encode($playlist_trans) );
}


// Print custom styles
add_action('wp_head', 'print_theme_styles', 1024);
function print_theme_styles(){
    global $post;
    
    $custom_css = TT::get_mod('custom_css');
    $custom_css .= TT::get_mod('custom_css_tablet') != '' ?    '@media (min-width: 768px) and (max-width: 985px) { ' . TT::get_mod('custom_css_tablet') . ' }' : '';
    $custom_css .= TT::get_mod('custom_css_widephone') != '' ? '@media (min-width: 481px) and (max-width: 767px) { ' . TT::get_mod('custom_css_widephone') . ' }' : '';
    $custom_css .= TT::get_mod('custom_css_phone') != '' ?     '@media (max-width: 480px) { '                        . TT::get_mod('custom_css_phone') . ' }' : '';
    $custom_css .= TT::get_mod('meta_disable') == '1' ? ' .meta {display:none !important;} ' : '';
    
    $body_bg_style = Tana_Std::get_option_bg_value('body_bg_image');
    $body_bg_style = $body_bg_style != '' ? "body.boxed-layout { $body_bg_style } ":'';

    $custom_styles = '';

    printf("<style type='text/css' id='theme-customize-css'>
                %s %s %s
            </style>", $body_bg_style, $custom_styles, $custom_css);
        
}






/*
                                                                    
 _____ _                 _              _____ _                     
|_   _| |_ ___ _____ ___| |_ ___ ___   |     | |___ ___ ___ ___ ___ 
  | | |   | -_|     | -_|  _| . |   |  |   --| | .'|_ -|_ -| -_|_ -|
  |_| |_|_|___|_|_|_|___|_| |___|_|_|  |_____|_|__,|___|___|___|___|
  
*/
$template_load_files = array(
    '/framework/classes/class.less.php',                    // Less Compiler
    '/framework/classes/class.render.meta.php',             // Meta fields for Posts
    '/framework/classes/class.wp.customize.controls.php',   // WP Customizer
    '/framework/classes/class.wp.customize.php',
    '/framework/functions/global.functions.php',            // Import functions
    '/framework/functions/functions.breadcrumb.php',
    '/framework/classes/class.import.data.php',             // Import Demo Data
    '/includes/widgets/init_widget.php',                    // Import Widgets
    '/includes/customizer.php',                             // Customizer
    '/includes/plugins.php',                                // TGM Plugin Activation
    '/includes/meta.page.php',                              // Quick Load Element for VC
    '/includes/ExtendVCRow.php',
    '/includes/template-tags.php',                          // Import Template tags
    '/includes/mega-menu/index.php',                        // Mega Menu
    '/includes/woo.php'                                     // Woocommerce
);
foreach ($template_load_files as $load_file) {
    if( file_exists(get_template_directory() . $load_file) ){
        require get_template_directory() . $load_file;
    }
}




// Import VC Custom Elements
function themeton_load_vc_elements(){
    $file_dir = get_template_directory() . '/includes/vc-elements/';
    foreach( glob( $file_dir . '*.php' ) as $filename ) {
        $filename = sprintf('/includes/vc-elements/%s', basename($filename));
        require get_template_directory() . $filename;
    }
}
add_action('vc_before_init', 'themeton_load_vc_elements');


?>