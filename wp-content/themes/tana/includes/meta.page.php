<?php

class CurrentThemePageMetas extends TTRenderMeta{

    function __construct(){
        add_action( 'admin_init', array($this, 'initial_items') );
        add_action( 'admin_enqueue_scripts', array($this, 'print_admin_scripts') );
        add_action( 'add_meta_boxes', array($this, 'add_custom_meta'), 1 );
        add_action( 'edit_post', array($this, 'save_post'), 99 );

        // support svg logo
        $svg_logo_filter = sprintf('%s_%s', 'upload', 'mimes');
        add_filter( $svg_logo_filter, array($this, 'support_svg_logo') );

        add_action('admin_enqueue_scripts', array($this, 'print_elements_icons'));
    }

    public function print_elements_icons(){
        wp_enqueue_style('tana-admin-vc-element-icons', get_template_directory_uri() . '/css/elements-icons.css' );
    }


    public function support_svg_logo($types){
        $types['svg'] = sprintf('%s/%s+%s', 'image', 'svg', 'xml');
        return $types;
    }
    

    public function initial_items(){
        $this->items = $this->items();
    }

    public function items(){
        global $post;

        define('ADMIN_IMAGES', get_template_directory_uri().'/framework/admin-assets/images/');

        $all_post_types = array();
        $data_post_types = TT::get_post_types();
        foreach ($data_post_types as $key => $value) {
            $all_post_types[] = $key;
        }

        $tmp_arr = array(
            'post' => array(
                'label' => esc_html__('Post Options', 'tana'),
                'post_type' => 'post',
                'items' => array(

                    array(
                        'name' => 'page_layout',
                        'type' => 'thumbs',
                        'label' => esc_html__('Post Layout', 'tana'),
                        'default' => 'customize',
                        'option' => array(
                            'customize' => ADMIN_IMAGES . '1customizer.png',
                            'center' => ADMIN_IMAGES . '3center.png',
                            'right' => ADMIN_IMAGES . '2cr.png',
                            'left' => ADMIN_IMAGES . '2cl.png',
                            'music' => ADMIN_IMAGES . 'music-center.png',
                        ),
                        'desc' => esc_html__('1) How declared on customize options. 2) Centered. 3) Right sidebar.', 'tana')
                    ),
                    array(
                        'type' => 'checkbox',
                        'name' => 'topparallax',
                        'label' => esc_html__('Top parallax with featured image', 'tana'),
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'parallaxheight',
                        'label' => esc_html__('Parallax image area height', 'tana'),
                        'default' => '540',
                        'option' => array(
                            '720' => '720px',
                            '600' => '600px',
                            '540' => '540px (default)',
                            '400' => '400px',
                            '320' => '320px',
                            '200' => '200px',
                        )
                    ),

                )
            ),
            
            'commont_layout' => array(
                'label' => esc_html__('Layout Options', 'tana'),
                'post_type' => $all_post_types,
                'items' => array(

                    array(
                        'name' => 'post_size',
                        'type' => 'thumbs',
                        'label' => esc_html__('Post Size on Masonry Layout', 'tana'),
                        'default' => '1',
                        'option' => array(
                            '1' => ADMIN_IMAGES . 'masonry1.jpg',
                            '2' => ADMIN_IMAGES . 'masonry2.jpg',
                            '3' => ADMIN_IMAGES . 'masonry3.jpg',
                            '4' => ADMIN_IMAGES . 'masonry4.jpg'
                        ),
                        'desc' => wp_kses(__('Select Post Layout on Movie element. <br><strong>Note:</strong> 4 & 5th styles are acceptable for Masonry Alternate element only.', 'tana'), array('br'=>array()) )
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'title_style',
                        'label' => esc_html__('Title & Author text variations', 'tana'),
                        'default' => '1',
                        'option' => array(
                            '1' => 'Top left (default)',
                            '2' => 'Bottom',
                            '3' => 'Above and bottom'
                            ),
                        'desc' => esc_html__('Please set your ancient color values on the Customize panel to modify this.', 'tana')
                    ),

                    array(
                        'type' => 'checkbox',
                        'name' => 'title_bigger',
                        'label' => esc_html__('Title size bigger', 'tana'),
                    ),

                    array(
                        'type' => 'checkbox',
                        'name' => 'color_light',
                        'label' => esc_html__('Title color light', 'tana'),
                        'default' => '',
                        'desc' => esc_html__('If your featured image is darker, you should select this option and make your title as white.', 'tana')
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'label',
                        'label' => esc_html__('Label text (optional)', 'tana'),
                        'default' => '',
                        'desc' => 'Ex: #3 or What\'s hot'
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'color',
                        'label' => esc_html__('Ancient color', 'tana'),
                        'default' => 'color-1',
                        'option' => array(
                            'color-1' => 'Color 1',
                            'color-2' => 'Color 2',
                            'color-3' => 'Color 3',
                            'color-4' => 'Color 4',
                            'color-5' => 'Color 5',
                            'color-6' => 'Color 6',
                            ),
                        'desc' => esc_html__('Please set your custom ancient color values on the Customize panel to modify this.', 'tana')
                    )

                )
            ),

            'post-movie' => array(
                'label' => esc_html__('Movie Post Options', 'tana'),
                'post_type' => 'post',
                'items' => array(

                    array(
                        'type' => 'text',
                        'name' => 'trailer',
                        'label' => esc_html__('Trailer link', 'tana'),
                        'default' => '',
                        'desc' => esc_html__('Any custom url you can allow but Youtube and vimeo url are show on lightbox. Set empty to disable.', 'tana')
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'rating',
                        'label' => esc_html__('Movie rating', 'tana'),
                        'default' => 'G',
                        'option' => array(
                            'G' => esc_html__('G - General Audiences', 'tana'),
                            'PG' => esc_html__('PG - Parental Guidance Suggested', 'tana'),
                            'PG-13' => esc_html__('PG-13 - Parents Strongly Cautioned', 'tana'),
                            'R' => esc_html__('R - Restricted', 'tana'),
                            'NC-17' => esc_html__('NC-17 - Adults Only', 'tana')
                        )
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'imdb_rate',
                        'label' => esc_html__('IMDB rate', 'tana'),
                        'desc' => esc_html__('Please insert number or float value that is lower than 10. 0 or empty value is for removal.', 'tana'),
                        'default' => '8.7'
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'customer_rate',
                        'label' => esc_html__('Customer rate', 'tana'),
                        'desc' => esc_html__('Please insert a number that is up to 5 for star rating.', 'tana'),
                        'default' => '4',
                        'option' => array(
                            '1' => '1 star',
                            '2' => '2 stars',
                            '3' => '3 stars',
                            '4' => '4 stars',
                            '5' => '5 stars',
                        )
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'total',
                        'label' => esc_html__('Total gross', 'tana'),
                        'default' => '$253.4M'
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'last_week',
                        'label' => esc_html__('Last weeks earnings', 'tana'),
                        'default' => '$43.7M'
                    ),

                    array(
                        'type' => 'text',
                        'name' => 'movie_author',
                        'label' => esc_html__('Movie/Music author', 'tana'),
                        'default' => 'Kevin Cook',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'release_date',
                        'label' => esc_html__('Release Date', 'tana'),
                        'default' => 'December 11, 2016',
                    )

                )
            ),
            'page' => array(
                'label' => 'Page Options',
                'post_type' => 'page',
                'items' => array(
                    array(
                        'name' => 'page_layout',
                        'type' => 'thumbs',
                        'label' => esc_html__('Page Layout', 'tana'),
                        'default' => 'full',
                        'option' => array(
                            'full' => ADMIN_IMAGES . '1col.png',
                            'right' => ADMIN_IMAGES . '2cr.png',
                            'left' => ADMIN_IMAGES . '2cl.png'
                        ),
                        'desc' => esc_html__('Select Page Layout (Fullwidth | Right Sidebar | Left Sidebar)', 'tana')
                    ),

                    array(
                        'type' => 'checkbox',
                        'name' => 'one_page_menu',
                        'label' => esc_html__('One page menu (menu by defined sections)', 'tana'),
                        'default' => '0',
                        'desc' => esc_html__('Please edit the Visual Composer rows and set properties that need to be a section of your page. And page menu presents by them when you turned this option On.', 'tana')
                    ),

                    array(
                        'type' => 'checkbox',
                        'name' => 'header_transparent',
                        'label' => esc_html__('Header Transparent', 'tana'),
                        'default' => '0',
                        'desc' => esc_html__('Header: position is absolute and background is transparent.', 'tana')
                    ),

                    array(
                        'type' => 'checkbox',
                        'name' => 'remove_padding',
                        'label' => esc_html__('Remove Padding', 'tana'),
                        'default' => '0'
                    ),

                    array(
                        'type' => 'checkbox',
                        'name' => 'title_show',
                        'label' => esc_html__('Page Title Show', 'tana'),
                        'default' => '1'
                    ),

                )
            )

        );

        return $tmp_arr;
    }
    
}

new CurrentThemePageMetas();

