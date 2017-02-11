<?php
/*
    ==================================
    Tana Wordpress Theme Configuration
    ==================================
*/

// Theme Setup
if ( ! function_exists( 'tana_theme_setup' ) ) :
    function tana_theme_setup() {

        // load translate file
        load_theme_textdomain( 'tana', get_template_directory() . '/languages' );

        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        // Let WordPress manage the document title.
        add_theme_support( 'title-tag' );

        // Support Theme Custom Logo
        add_theme_support( 'custom-logo' );

        // Enable support for Post Thumbnails on posts and pages.
        add_theme_support( 'post-thumbnails' );
        set_post_thumbnail_size( 848, 500, true );

        // Set Image sizes
        add_image_size( 'tana-post-grid-b', 306, 312, true );
        add_image_size( 'tana-rightnow', 165, 100, true );
        add_image_size( 'tana-blog-grid', 400, 240, true );
        add_image_size( 'tana-thumbnail', 80, 80, true );
        add_image_size( 'tana-slider-thumbnail', 269, 177, true );
        
        add_image_size( 'tana-blog-square', 600, 600, true );
        add_image_size( 'tana-blog-vertical', 600, 688, true );
        
        add_image_size( 'tana-list-masonry-small', 600, 448, true );
        add_image_size( 'tana-list-masonry-big', 800, 386, true );

        // This theme uses wp_nav_menu() in two locations.
        register_nav_menus( array(
            'primary' => esc_html__('Primary Menu', 'tana'),
            'footer_menu' => esc_html__('Footer Menu', 'tana')
        ) );

        // Switch default core markup for search form, comment form, and comments to output valid HTML5.
        add_theme_support( 'html5', array(
            'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
        ) );
        
        add_theme_support( 'post-formats', array(
            'quote', 'image', 'gallery', 'audio', 'video', 'link'
        ) );


        // Indicate widget sidebars can use selective refresh in the Customizer.
        add_theme_support( 'customize-selective-refresh-widgets' );
    }
endif;
add_action( 'after_setup_theme', 'tana_theme_setup' );



// default content width
if ( ! isset( $content_width ) ) $content_width = 940;


$tana_exclude_posts = array();

// Register widget area.
function tana_theme_widgets_init() {
    
    // define sidebars
    $theme_sidebars = array(
        'sidebar'=> esc_html__('Post Sidebar Area', 'tana'),
        'sidebar-page'=> esc_html__('Page Sidebar Area', 'tana'),
        'sidebar-push-menu'=> esc_html__('Push Sidebar Area', 'tana')
    );

    if( class_exists('WooCommerce') ){
        $theme_sidebars['sidebar-woo'] = esc_html__('Woo Sidebar Area', 'tana');
    }
    
    foreach ($theme_sidebars as $id => $sidebar) {
        if( !empty($id) ){
            if( $id=='sidebar-portfolio' && !class_exists('TT_Portfolio_PT') )
                continue;
            
            register_sidebar(array(
                'name' => $sidebar,
                'id' => $id,
                'description' => esc_html__('Add widgets here to appear in your sidebar.', 'tana'),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h5 class="widget-title"><span>',
                'after_title'   => '</span></h5>'
            ));                
        }
    }


    // Footer widget areas
    for($i=1; $i<=6 ; $i++ ) {
        register_sidebar(
            array(
                'name'          => esc_html__('Footer Column', 'tana') . ' ' .$i,
                'id'            => 'footer'.$i,
                'description'   => esc_html__('Add widgets here to appear in your footer column', 'tana') . ' ' .$i,
                'before_widget' => '<div id="%1$s" class="footer_widget widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h5 class="widget-title">',
                'after_title'   => '</h5>'
            )
        );
    }


    $custom_sidebars = Tana_Std::get_mod('custom_sidebars');
    if( !empty($custom_sidebars) ){
        $ex_sidebars = explode(',', $custom_sidebars);
        foreach ($ex_sidebars as $sid) {
            $sidebar_id = trim($sid);

            register_sidebar(
                array(
                    'id'            => 'csb_' . $sidebar_id,
                    'name'          => esc_html__('Custom Sidebar', 'tana') . sprintf('(%s)', $sidebar_id),
                    'description'   => esc_html__('Add widgets here to appear in your custom sidebar', 'tana') . sprintf('(%s)', $sidebar_id),
                    'before_widget' => '<div id="%1$s" class="custom_sidebar_widget widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h5 class="widget-title">',
                    'after_title'   => '</h5>'
                )
            );
        }
    }
}

add_action( 'widgets_init', 'tana_theme_widgets_init' );


// Site Favicon
if( !function_exists('wp_site_icon') ):
    // Print Favicon
    add_action('wp_head', 'tana_print_favicon');
    function tana_print_favicon(){
        if(Tana_Std::get_mod('favicon') != ''){
            echo '<link rel="shortcut icon" type="image/x-icon" href="'.Tana_Std::get_mod('favicon').'"/>';
        }
    }
endif;



// google Fonts
if ( ! function_exists( 'tana_theme_fonts_url' ) ) :
    function tana_theme_fonts_url() {
        $fonts_url = '';
        $fonts     = array();
        $subsets   = 'latin,latin-ext';

        if ( $fonts ) {
            $fonts_url = esc_url(add_query_arg( array(
                'family' => implode( '|', $fonts ),
                'subset' => urlencode( $subsets ),
            ), '//fonts.googleapis.com/css' ));
        }

        return $fonts_url;
    }
endif;


// Enqueue Scripts
function tana_enqueue_scripts() {
    // support wp-media-element
    wp_enqueue_script( 'wp-mediaelement' );

    // Add custom fonts, used in the main stylesheet.
    wp_enqueue_style( 'tt-theme-fonts', tana_theme_fonts_url(), array(), null );
    
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Include all static css files
    wp_enqueue_style( 'tana-lib-packages', get_template_directory_uri() . '/css/packages.min.css' );
    wp_enqueue_script('tana-lib-packages', get_template_directory_uri() . '/js/packages.min.js', array('jquery'), false, true );
    
    // Theme style and scripts
    wp_enqueue_style( 'tana-stylesheet', get_stylesheet_uri() );
    wp_enqueue_script('tana-scripts', get_template_directory_uri() . '/js/scripts.min.js', array('jquery', 'wp-mediaelement', 'jquery-ui-slider'), false, true );

}
add_action( 'wp_enqueue_scripts', 'tana_enqueue_scripts' );





// Body Class Filter
add_filter( 'body_class', 'tana_body_class_filter' );
function tana_body_class_filter( $classes ) {
    global $post;

    if(Tana_Std::get_mod('footer_fixed') == '1') { $classes[] = 'fixed-footer'; }
    if(Tana_Std::get_mod('video_lightbox_disable') == '1') { $classes[] = 'disable-video-lightbox'; }
    if(Tana_Std::get_mod('boxed-layout') == '1') { $classes[] = 'boxed-layout'; }

    return $classes;
}




// Custom Excerpt Length
function custom_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );




// Custom Excerpt More Symbol
function custom_excerpt_more( $excerpt ) {
    return ' ...';
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );



/**
 * This code filters the Categories archive widget to include the post count inside the link
 */
add_filter('wp_list_categories', 'cat_count_span');

function cat_count_span($links) {
    $links = str_replace('</a> (', ' <span>', $links);
    $links = str_replace('<span class="count">(', '<span>', $links);
    $links = str_replace(')', '</span></a>', $links);
    return $links;
}



/**
 * This code filters the Archive widget to include the post count inside the link
 */
add_filter('get_archives_link', 'archive_count_span');

function archive_count_span($links) {
    $links = str_replace('</a>&nbsp;(', ' <span>', $links);
    $links = str_replace(')</li>', '</span></a></li>', $links);
    return $links;
}



// Custom Gallery Shortcode
function tana_gallery_shortcode( $output = '', $atts, $instance ) {
    $return = $output;

    // retrieve content of your own gallery function
    $tana_gallery = Tana_Tpl::gallery_slideshow( $atts );
    if( !empty( $tana_gallery ) ) {
        $return = $tana_gallery;
    }

    return $return;
}
add_filter( 'post_gallery', 'tana_gallery_shortcode', 10, 3 );



// Print Main Menu
function tana_primary_menu_filter( $nav_menu, $args ){
    if( isset($args->menu_id) && $args->menu_id=='primary-nav' ){
        global $post;
        $po = $post;
        $page_for_posts = get_option('page_for_posts');
        $is_blog_page = is_home() && get_post_type($post) && !empty($page_for_posts) ? true : false;
        if( (is_page() || $is_blog_page) && $is_blog_page ){
            $po = get_post($page_for_posts);
        }

        if( isset($po->ID) && Tana_Std::getmeta('one_page_menu', $po->ID)=='1' ){
            $content = $po->post_content;
            $pattern = get_shortcode_regex();

            $menu_class = isset($args->menu_class) && !empty($args->menu_class) ? esc_attr($args->menu_class) : '';
            $nav_menu = "<ul class='$menu_class one-page-menu'>";
            if( preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) && array_key_exists( 2, $matches ) && in_array( 'vc_row', $matches[2] ) ){
                foreach ($matches[3] as $attr) {
                    $props = array();
                    $sarray = explode('" ', trim($attr));
                    foreach ($sarray as $val) {
                        $el =explode("=", $val);
                        $s1 = str_replace('"', '', trim($el[0]));
                        $s2 = isset($el[1]) ? str_replace('"', '', trim($el[1])) : '';
                        $props[$s1] = $s2;
                    }

                    if( isset($props['one_page_section'], $props['one_page_label']) && $props['one_page_section']=='yes' && !empty($props['one_page_label']) ){
                        $label = $props['one_page_label'];
                        $slug = isset($props['one_page_slug']) && !empty($props['one_page_slug']) ? $props['one_page_slug'] : Tana_Std::create_slug($props['one_page_label']);

                        $nav_menu .= "<li class='menu-item'><a class='scroll-to-link' href='#".esc_attr($slug)."'>$label</a></li>";
                    }

                }
            }
            $nav_menu .= "</ul>";
        }

    }

    return $nav_menu;
}
add_filter('wp_nav_menu', 'tana_primary_menu_filter', 10, 2);





// Primary menu callback function
function tana_primary_callback(){
    echo '<ul class="menu">';
    wp_list_pages( array(
        'sort_column'  => 'menu_order, post_title',
        'title_li' => '') );
    echo '</ul>';
}


// Footer menu callback function
function tana_footer_menu_callback(){
    echo '<ul class="list-inline pull-left">';
        echo '<li class="menu-item"><a href="'.esc_url(home_url('/')).'">'.esc_html__('Home', 'tana').'</a></li>';
        echo '<li class="menu-item"><a href="'.esc_url(home_url('/')).'?post_type=post">'.esc_html__('Archive', 'tana').'</a></li>';
        echo '<li class="menu-item"><a href="'.esc_url(home_url('/')).'?s=">'.esc_html__('Search', 'tana').'</a></li>';
    echo '</ul>';
}



// Read more filter
add_filter( 'the_content_more_link', 'modify_read_more_link' );
function modify_read_more_link() {
    return '<br><br><a href="' . esc_url(get_permalink()) . '" class="button button-fill button-bordered button-small">'.esc_html__('Read More', 'tana').'</a>';
}



/*
                                                                    
 _____ _                 _              _____ _                     
|_   _| |_ ___ _____ ___| |_ ___ ___   |     | |___ ___ ___ ___ ___ 
  | | |   | -_|     | -_|  _| . |   |  |   --| | .'|_ -|_ -| -_|_ -|
  |_| |_|_|___|_|_|_|___|_| |___|_|_|  |_____|_|__,|___|___|___|___|
  
*/
// Themeton Standard Package
require get_template_directory() . '/framework/classes/class.themeton.std.php';

// Theme Class Extends Themeton Class
class Tana_Std extends ThemetonStd { }

// Include current theme customize
require get_template_directory() . '/includes/functions.php';

// Theme Class Extends Template Class
class Tana_Tpl extends TPL { }






function tana_filter_publish_dates( $the_date, $d, $post ) {
    return human_time_diff( strtotime($post->post_date), current_time('timestamp') );
}
if( Tana_Std::get_mod('content_human_time')=='1' ){
    add_action( 'get_the_date', 'tana_filter_publish_dates', 10, 3 );
}





function tana_header_classes_hook( $classes ){
    global $post;
    $classes = !empty($classes) ? $classes : array();

    $header_sticky = Tana_Std::get_mod('header_sticky');
    if( $header_sticky=='1' ){
        $classes[] = 'header-sticky';
    }
    else if( $header_sticky=='2' ){
        $classes[] = 'header-sticky';
        $classes[] = 'sticky-permanent';
    }

    if( !empty($post) && is_page() ){
        $opt = Tana_Std::getmeta('header_transparent');
        if( $opt=='1' ){
            $classes[] = 'header-transparent';
        }
    }

    return $classes;
}
add_filter( 'tana_header_classes', 'tana_header_classes_hook' );





// Get weather information
if( !function_exists('tana_get_weather_info_hook') ):
function tana_get_weather_info_hook(){
    $temp = Tana_Std::get_mod('weather_temp', '0');
    $date = Tana_Std::get_mod('weather_date');
    $city = esc_attr(Tana_Std::get_mod('weather_city', 'ulaanbaatar'));
    $today = date('Y-m-d');
    $apikey = esc_attr(Tana_Std::get_mod('weather_apikey', 'b4bf55bd81602401bba560478c0c9c06'));

    if( $date!=$today || isset($_POST['renew']) ){
        $uri = "http://api.openweathermap.org/data/2.5/weather?q=$city&units=metric&APPID=$apikey";
        $get_remote = wp_remote_get($uri);
        $json_string = array_key_exists('body', $get_remote) ? $get_remote['body'] : '';
        $json_content = json_decode($json_string);
        // temp | pressure | humidity | temp_min | temp_max | sea_level | grnd_level
        $temp = isset($json_content->main->temp) ? $json_content->main->temp_max : 0;

        set_theme_mod('weather_temp', (int)$temp);
        set_theme_mod('weather_date', $today);
        set_theme_mod('weather_last_city', $city);
    }

    echo (int)$temp;
    exit;
}
endif;
add_action('wp_ajax_tana_get_weather_info', 'tana_get_weather_info_hook');
add_action('wp_ajax_nopriv_tana_get_weather_info', 'tana_get_weather_info_hook');

?>