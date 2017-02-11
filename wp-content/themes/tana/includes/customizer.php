<?php

if (!function_exists('tt_customizer_options')):

    function tt_customizer_options() {
        $template_uri = get_template_directory_uri();

        $pages = array();
        $all_pages = get_pages();
        foreach ($all_pages as $page) {
            $pages[$page->ID] = $page->post_title;
        }


        $content_options = array(
            array(
                'id' => 'content_human_time',
                'label' => esc_html__('Content Date - Human Readablity', 'tana'),
                'default' => '0',
                'type' => 'switch'
            ),

            array(
                'id' => 'unique_posts',
                'label' => esc_html__('Show Unique Posts', 'tana'),
                'desc' => esc_html__('Please turn this ON, if you do not want to show duplicate posts.', 'tana'),
                'default' => '0',
                'type' => 'switch',
            ),
            array(
                'id' => 'disable_featured_media',
                'label' => esc_html__('Remove featured image & media', 'tana'),
                'desc' => esc_html__('Disable featured image & format media at top of single post and prevent post format data duplication.', 'tana'),
                'default' => '0',
                'type' => 'switch',
            ),

            array(
                'id' => 'content-font-size',
                'label' => esc_html__('Content Font Size', 'tana'),
                'default' => getLessValue('content-font-size'),
                'type' => 'pixel'
            ),
            array(
                'id' => 'content-line-height',
                'label' => esc_html__('Content Text Line Height', 'tana'),
                'default' => getLessValue('content-line-height'),
                'type' => 'pixel'
            ),
            array(
                'id' => 'content-letter-space',
                'label' => esc_html__('Content Text Letter Space', 'tana'),
                'default' => getLessValue('content-letter-space'),
                'type' => 'pixel'
            ),

            array(
                'id' => 'post_single_view',
                'label' => esc_html__('Post single view', 'tana'),
                'default' => 'right',
                'type' => 'select',
                'choices' => array(
                    'center' => esc_html__('Center (no sidebar)', 'tana'),
                    'right' => esc_html__('Right sidebar', 'tana'),
                    'left' => esc_html__('Left sidebar', 'tana'),
                    'music' => esc_html__('Music Single (no sidebar)', 'tana')
                )
            ),
            array(
                'id' => 'meta_disable',
                'label' => esc_html__('Disable meta details globally', 'tana'),
                'desc' => esc_html__('Remove author name and date list site entirely.', 'tana'),
                'default' => '0',
                'type' => 'switch',
            ),
            array(
                'id' => 'meta_inline',
                'label' => esc_html__('Horizontal meta details', 'tana'),
                'desc' => esc_html__('Meta details inline next to Post Title if you need more room on main content section.', 'tana'),
                'default' => '0',
                'type' => 'switch',
            ),
            array(
                'id' => 'archive_style',
                'label' => esc_html__('Category Page Layout', 'tana'),
                'default' => 'regular',
                'type' => 'select',
                'choices' => array(
                    'regular' => 'Regular (default)',
                    '2 columns' => '2 columns',
                    '3 columns' => '3 columns',
                    '4 columns' => '4 columns',
                    '6 columns' => '6 columns',
                    '2 columns nosidebar' => '2 columns no sidebar',
                    '3 columns nosidebar' => '3 columns no sidebar',
                    '4 columns nosidebar' => '4 columns no sidebar',
                    '6 columns nosidebar' => '6 columns no sidebar',
                ),
                'desc' => esc_html__('Selection performs archive & tags page as well.', 'tana'),
            )
        );


        if( class_exists('WooCommerce') ){
            $content_options[] = array(
                'id' => 'page_option_woo_title',
                'type' => 'sub_title',
                'label' => esc_html__('WooCommerce', 'tana'),
                'default' => ''
            );
            $content_options[] = array(
                'id' => 'woo_columns',
                'label' => esc_html__('Woo Columns', 'tana'),
                'default' => 'default',
                'type' => 'select',
                'choices' => array(
                    'default' => esc_html__('Default', 'tana'),
                    '2' => esc_html__('2 Columns', 'tana'),
                    '3' => esc_html__('3 Columns', 'tana'),
                    '4' => esc_html__('4 Columns', 'tana')
                )
            );
            $content_options[] = array(
                'id' => 'woo_sidebars',
                'label' => esc_html__('Woo Sidebar', 'tana'),
                'default' => 'full',
                'type' => 'select',
                'choices' => array(
                    'full' => esc_html__('No Sidebar', 'tana'),
                    'left' => esc_html__('Left Sidebar', 'tana'),
                    'right' => esc_html__('Right Sidebar', 'tana')
                )
            );
        }


        $option = array(
            // General
            array(
                'type' => 'section',
                'id' => 'generals',
                'label' => esc_html__('General & Colors', 'tana'),
                'desc' => '',
                'controls' => array(


                    array(
                        'type' => 'color',
                        'id' => 'brand-color',
                        'label' => esc_html__('Brand Color', 'tana'),
                        'default' => getLessValue('brand-color')
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'color-title',
                        'label' => esc_html__('Title color', 'tana'),
                        'default' => getLessValue('color-title')
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'color-text',
                        'label' => esc_html__('Text color', 'tana'),
                        'default' => getLessValue('color-text')
                    ),

                    array(
                        'type' => 'color',
                        'id' => 'color-second',
                        'label' => esc_html__('Text second color', 'tana'),
                        'default' => getLessValue('color-second')
                    ),

                    array(
                        'type' => 'color',
                        'id' => 'content-bg-color',
                        'label' => esc_html__('Content Background Color', 'tana'),
                        'default' => getLessValue('content-bg-color')
                    ),

                    array(
                        'id' => 'boxed-layout',
                        'label' => esc_html__('Boxed layout', 'tana'),
                        'default' => '0',
                        'type' => 'switch',
                        'desc' => 'Content area is 1440px. Please do not use VC fullwidth stretch rows to prevent overflow.'
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'body-background',
                        'label' => esc_html__('Outer Background Color', 'tana'),
                        'default' => getLessValue('body-background'),
                        'desc' => 'Please turn on above Boxed layout to execute this option.'
                    ),
                    array(
                        'id' => 'body_bg_image',
                        'type' => 'bg_image',
                        'label' => esc_html__('Outer Background Image', 'tana'),
                        'default' => '',
                        'desc' => 'Please turn on above Boxed layout to execute this option.'
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'page-cover-bg',
                        'label' => esc_html__('Page Title Background', 'tana'),
                        'default' => getLessValue('page-cover-bg')
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'page-cover-color',
                        'label' => esc_html__('Page Title Text Color', 'tana'),
                        'default' => getLessValue('page-cover-color')
                    ),

                )
            ),// end General


            // Fonts
            array(
                'type' => 'section',
                'id' => 'font',
                'label' => esc_html__('Font', 'tana'),
                'desc' => '',
                'controls' => array(
                    array(
                        'type' => 'font',
                        'id' => 'font-title',
                        'label' => esc_html__('Title Font', 'tana'),
                        'default' => getLessValue('font-title')
                    ),
                    array(
                        'type' => 'font',
                        'id' => 'font-text',
                        'label' => esc_html__('Text Font', 'tana'),
                        'default' => getLessValue('font-text')
                    ),

                    array(
                        'type' => 'font',
                        'id' => 'footer-font-title',
                        'label' => esc_html__('Footer Title Font', 'tana'),
                        'default' => getLessValue('footer-font-title')
                    ),
                    array(
                        'type' => 'font',
                        'id' => 'footer-font-text',
                        'label' => esc_html__('Footer Text Font', 'tana'),
                        'default' => getLessValue('footer-font-text')
                    )

                )
            ),// end Fonts


            // Branding & Logo
            array(
                'type' => 'section',
                'id' => 'section_header_style',
                'label' => esc_html__('Header Styles', 'tana'),
                'desc' => '',
                'controls' => array(

                    array(
                        'type' => 'image',
                        'id' => 'logo',
                        'label' => esc_html__('Logo Image', 'tana'),
                        'default' => ''
                    ),
                    array(
                        'type' => 'image',
                        'id' => 'favicon',
                        'label' => esc_html__('Favicon', 'tana'),
                        'default' => $template_uri . "/images/favicon.png"
                    ),


                    // Header Options Section
                    array(
                        'id' => 'header_option_section',
                        'type' => 'sub_title',
                        'label' => esc_html__('Header Options', 'tana'),
                        'default' => ''
                    ),

                    array(
                        'id' => 'header_layout',
                        'label' => esc_html__('Header Layout', 'tana'),
                        'default' => 'standard',
                        'type' => 'select',
                        'choices' => array(
                            'standard'      => esc_html__('Standard', 'tana'),
                            'menu-left'     => esc_html__('Menu Left', 'tana'),
                            'menu-center'   => esc_html__('Menu Center', 'tana'),
                            'menu-right'    => esc_html__('Menu Right', 'tana'),
                            'menu-burger'   => esc_html__('Only Burger Menu', 'tana'),
                            'menu-minimal'    => esc_html__('Minimal', 'tana'),
                            'menu-shop'    => esc_html__('Logo with Burger Menu', 'tana')
                        )
                    ),


                    array(
                        'id' => 'header_sticky',
                        'label' => esc_html__('Sticky Header', 'tana'),
                        'default' => '0',
                        'type' => 'select',
                        'choices' => array(
                            '0' => esc_html__('None', 'tana'),
                            '1' => esc_html__('Appear when scroll up', 'tana'),
                            '2' => esc_html__('Permanent at top', 'tana')
                        )
                    ),


                    // background color
                    array(
                        'type' => 'color',
                        'id' => 'header-bg',
                        'label' => esc_html__('Header background color', 'tana'),
                        'default' => getLessValue('header-bg'),
                        'dependency' => array('element'=>'header_layout', 'value'=>'menu-left')
                    ),

                    array(
                        'id' => 'header-alpha',
                        'label' => esc_html__('Header Opacity', 'tana'),
                        'desc' => esc_html__('It works when header transparent on page', 'tana'),
                        'default' => getLessValue('header-alpha'),
                        'type' => 'select',
                        'choices' => array(
                            '0%' => '0%',
                            '10%' => '10%',
                            '20%' => '20%',
                            '30%' => '30%',
                            '40%' => '40%',
                            '50%' => '50%',
                            '60%' => '60%',
                            '70%' => '70%',
                            '80%' => '80%',
                            '90%' => '90%',
                            '100%' => '100%'
                        )
                    ),

                    array(
                        'type' => 'pixel',
                        'id' => 'header-height',
                        'label' => esc_html__('Header Height', 'tana'),
                        'default' => getLessValue('header-height')
                    ),


                    // element on header
                    array(
                        'id' => 'header_search_mode',
                        'label' => esc_html__('Search on Header', 'tana'),
                        'desc' => esc_html__('Only for Menu Left Layout', 'tana'),
                        'default' => '0',
                        'type' => 'switch'
                    ),
                    array(
                        'id' => 'header_info_el',
                        'label' => esc_html__('Weather/Date on Header', 'tana'),
                        'desc' => esc_html__('Only for Burger Menu Layout', 'tana'),
                        'default' => '0',
                        'type' => 'switch'
                    ),

                    array(
                        'type' => 'color',
                        'id' => 'logo-color',
                        'label' => esc_html__('Logo Text Color', 'tana'),
                        'default' => getLessValue('logo-color')
                    ),

                    array(
                        'type' => 'font',
                        'id' => 'logo-font',
                        'label' => esc_html__('Logo Text font', 'tana'),
                        'default' => getLessValue('logo-font')
                    ),

                    array(
                        'id' => 'logo-width',
                        'label' => esc_html__('Logo Width', 'tana'),
                        'default' => getLessValue('logo-width'),
                        'type' => 'pixel'
                    ),


                    // Menu Options Section
                    array(
                        'id' => 'menu_option_section',
                        'type' => 'sub_title',
                        'label' => esc_html__('Menu Options', 'tana'),
                        'default' => ''
                    ),

                    array(
                        'type' => 'font',
                        'id' => 'menu-font',
                        'label' => esc_html__('Menu font', 'tana'),
                        'default' => getLessValue('menu-font')
                    ),

                    array(
                        'type' => 'pixel',
                        'id' => 'menu-font-size',
                        'label' => esc_html__('Menu Text Size', 'tana'),
                        'default' => getLessValue('menu-font-size')
                    ),

                    array(
                        'type' => 'pixel',
                        'id' => 'menu-space',
                        'label' => esc_html__('Menu Items Space', 'tana'),
                        'default' => getLessValue('menu-space')
                    ),

                    array(
                        'type' => 'color',
                        'id' => 'menu-color',
                        'label' => esc_html__('Menu Text color', 'tana'),
                        'default' => getLessValue('menu-color')
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'menu-bg',
                        'label' => esc_html__('Menu background', 'tana'),
                        'default' => getLessValue('menu-bg')
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'menu-color-sub',
                        'label' => esc_html__('Sub menu color', 'tana'),
                        'default' => getLessValue('menu-color-sub')
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'menu-bg-sub',
                        'label' => esc_html__('Sub menu background', 'tana'),
                        'default' => getLessValue('menu-bg-sub')
                    ),


                    // weather information
                    array(
                        'id' => 'weather_option_section',
                        'type' => 'sub_title',
                        'label' => esc_html__('Weather Options', 'tana'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'weather_title',
                        'label' => esc_html__('Weather Title', 'tana'),
                        'default' => esc_html__('Current Location', 'tana'),
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'weather_city',
                        'label' => esc_html__('Weather City Name', 'tana'),
                        'default' => 'ulaanbaatar',
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'weather_apikey',
                        'label' => 'openweathermap.org - API KEY',
                        'default' => 'b4bf55bd81602401bba560478c0c9c06',
                        'type' => 'input'
                    ),


                    // Page title section
                    array(
                        'id' => 'header_option_bg',
                        'type' => 'sub_title',
                        'label' => esc_html__('Page Title Style', 'tana'),
                        'default' => ''
                    ),


                    array(
                        'id' => 'page_cover_title',
                        'label' => esc_html__('Sub Title', 'tana'),
                        'default' => esc_html__('Read The Story', 'tana'),
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'page_cover_desc',
                        'label' => esc_html__('Page Title Description', 'tana'),
                        'default' => esc_html__('Blog', 'tana'),
                        'type' => 'textarea'
                    ),

                    // Post Title
                    array(
                        'id' => 'post_title_txt',
                        'type' => 'sub_title',
                        'label' => esc_html__('Page Title Style', 'tana'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'post_cover_title',
                        'label' => esc_html__('Post Fancy Title', 'tana'),
                        'default' => esc_html__('Article', 'tana'),
                        'type' => 'input'
                    ),

                )
            ),// end Branding



            // Push Menu Options
            array(
                'type' => 'section',
                'id' => 'push_menu_options',
                'label' => esc_html__('Push Sidebar Options', 'tana'),
                'controls' => array(
                    array(
                        'type' => 'color',
                        'id' => 'pm-color',
                        'label' => esc_html__('Text Color', 'tana'),
                        'default' => getLessValue('pm-color')
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'pm-bg',
                        'label' => esc_html__('Background Color', 'tana'),
                        'default' => getLessValue('pm-bg')
                    ),
                    array(
                        'id' => 'pm_overlay',
                        'label' => esc_html__('Overlay Color', 'tana'),
                        'default' => 'light',
                        'type' => 'select',
                        'choices' => array(
                            'light' => 'Light',
                            'dark' => 'Dark'
                        )
                    ),
                    array(
                        'id' => 'pm_bg_dots',
                        'label' => esc_html__('Background Pattern', 'tana'),
                        'default' => '0',
                        'type' => 'switch'
                    ),
                    array(
                        'type' => 'pixel',
                        'id' => 'pm-width',
                        'label' => esc_html__('Sidebar Width', 'tana'),
                        'default' => getLessValue('pm-width')
                    ),
                    array(
                        'id' => 'pm_close',
                        'label' => esc_html__('Close Button', 'tana'),
                        'default' => '1',
                        'type' => 'switch'
                    ),
                    array(
                        'id' => 'pm_home_link',
                        'label' => esc_html__('Home Button', 'tana'),
                        'default' => '0',
                        'type' => 'switch'
                    ),

                ),
            ), //end Push Menu Options


            // Ticker options
            array(
                'type' => 'section',
                'id' => 'ticker_options',
                'label' => esc_html__('Ticker Options', 'tana'),
                'controls' => array(
                    array(
                        'id' => 'ticker_enable',
                        'label' => esc_html__('Ticker Enable/Disable', 'tana'),
                        'default' => '1',
                        'type' => 'switch'
                    ),
                    array(
                        'id' => 'ticker_single_enable',
                        'label' => esc_html__('Enable/Disable in Single', 'tana'),
                        'default' => '0',
                        'type' => 'switch',
                        'desc' => esc_html__('This is a sub option. Depends with the above option.', 'tana'),
                    ),
                    array(
                        'id' => 'ticker_title',
                        'label' => esc_html__('Title', 'tana'),
                        'desc' => esc_html__('Category Slug', 'tana'),
                        'default' => esc_html__('Last Rumor', 'tana'),
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'ticker_cat_slug',
                        'label' => esc_html__('Post Category Slug', 'tana'),
                        'desc' => esc_html__('Category Slug', 'tana'),
                        'default' => 'uncategorized',
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'ticker_count',
                        'label' => esc_html__('Post Count', 'tana'),
                        'default' => '5',
                        'type' => 'select',
                        'choices' => array(
                            '3' => '3', '4' => '4',
                            '5' => '5', '6' => '6',
                            '7' => '7', '8' => '8',
                            '9' => '9', '10' => '10',
                            '11' => '11', '12' => '12',
                            '13' => '13', '14' => '14',
                            '15' => '15', '16' => '16',
                            '17' => '17', '18' => '18',
                            '19' => '19', '20' => '20'
                        )
                    )
                ),
            ), //end Ticker options


            // Content options
            array(
                'type' => 'section',
                'id' => 'page_content',
                'label' => esc_html__('Content Options', 'tana'),
                'controls' => $content_options
            ), //end Content options


            // Social options
            array(
                'type' => 'section',
                'id' => 'social_content',
                'label' => esc_html__('Social Links', 'tana'),
                'controls' => array(
                    array(
                        'id' => 'social_fb',
                        'label' => 'Facebook',
                        'desc' => 'http://facebook.com/example',
                        'default' => '#',
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'social_tw',
                        'label' => 'Twitter',
                        'desc' => 'http://twitter.com/example',
                        'default' => '#',
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'social_gp',
                        'label' => 'Google Plus',
                        'desc' => 'http://plus.google.com/example',
                        'default' => '#',
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'social_vm',
                        'label' => 'Vimeo',
                        'desc' => 'http://www.vimeo.com/example',
                        'default' => '',
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'social_yt',
                        'label' => 'Youtube',
                        'desc' => 'http://www.youtube.com/example',
                        'default' => '#',
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'social_in',
                        'label' => 'Instagram',
                        'desc' => 'http://www.instagram.com/example',
                        'default' => '',
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'social_ln',
                        'label' => 'Linkedin',
                        'desc' => 'http://www.linkedin.com/example',
                        'default' => '',
                        'type' => 'input'
                    ),
                    array(
                        'id' => 'social_header',
                        'label' => esc_html__('Social links at header', 'tana'),
                        'desc' => esc_html__('For News header style', 'tana'),
                        'default' => '1',
                        'type' => 'switch'
                    ),
                    array(
                        'id' => 'social_footer',
                        'label' => esc_html__('Social links at footer top', 'tana'),
                        'desc' => esc_html__('Next to footer logo', 'tana'),
                        'default' => '0',
                        'type' => 'switch'
                    ),
                    array(
                        'id' => 'social_subfooter',
                        'label' => esc_html__('Social links at sub footer', 'tana'),
                        'default' => '1',
                        'type' => 'switch'
                    )
                ),
            ), //end Social options


            // Footer
            array(
                'type' => 'section',
                'id' => 'section_footer',
                'label' => esc_html__('Footer', 'tana'),
                'controls' => array(

                    array(
                        'id' => 'footer_disable',
                        'label' => esc_html__('Footer Disable Entirely', 'tana'),
                        'default' => '0',
                        'type' => 'switch'
                    ),

                    array(
                        'id' => 'footer_light',
                        'label' => esc_html__('Footer Light Style', 'tana'),
                        'default' => '0',
                        'type' => 'switch'
                    ),
                    array(
                        'id' => 'footer_ancient',
                        'label' => esc_html__('Footer Ancient Color Style', 'tana'),
                        'default' => '0',
                        'type' => 'switch',
                        'desc' => esc_html__('Brand color on list and Subscribe etc', 'tana'),
                    ),
                    array(
                        'id' => 'footer_fixed',
                        'label' => esc_html__('Footer Fixed at behind', 'tana'),
                        'default' => '0',
                        'type' => 'switch'
                    ),
                    array(
                        'id' => 'footer_bg_image',
                        'type' => 'bg_image',
                        'label' => esc_html__('Footer Background Image', 'tana'),
                        'default' => ''
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'footer-bg',
                        'label' => esc_html__('Footer Background', 'tana'),
                        'default' => getLessValue('footer-bg')
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'footer-color',
                        'label' => esc_html__('Footer Text Color', 'tana'),
                        'default' => getLessValue('footer-color')
                    ),
                    array(
                        'type' => 'color',
                        'id' => 'sub-footer-color',
                        'label' => esc_html__('Sub Footer Text Color', 'tana'),
                        'default' => getLessValue('sub-footer-color')
                    ),

                    // Footer top
                    array(
                        'id' => 'footer_title_top',
                        'type' => 'sub_title',
                        'label' => esc_html__('Footer Top Section', 'tana'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'footer_top_enable',
                        'label' => esc_html__('Enable Footer Top', 'tana'),
                        'default' => '1',
                        'type' => 'switch'
                    ),
                    array(
                        'id' => 'footer_logo',
                        'label' => esc_html__('Footer Logo Image', 'tana'),
                        'default' => get_template_directory_uri()."/images/logo.svg",
                        'type' => 'image'
                    ),
                    array(
                        'id' => 'footer_top_subscribe',
                        'label' => esc_html__('Enable Subscribe form', 'tana'),
                        'default' => '1',
                        'type' => 'switch'
                    ),

                    // Footer main
                    array(
                        'id' => 'footer_title_main',
                        'type' => 'sub_title',
                        'label' => esc_html__('Footer Main Section', 'tana'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'footer_style',
                        'label' => esc_html__('Footer Columns', 'tana'),
                        'default' => '4',
                        'type' => 'select',
                        'choices' => array(
                            '1' => 'Full',
                            '2' => '2 columns',
                            '3' => '3 columns',
                            '31' => '3 columns, 1/3 + 1/4 + 5/12',
                            '4' => '4 columns',
                            '41' => '4 columns, 1/6 + 1/4 + 1/3 + 1/4',
                            '42' => '4 columns, 1/3 + 1/6 + 1/4 + 1/4',
                            '5' => '5 columns, 1/6 + 1/6 + 1/6 + 1/4 + 1/4',
                            '51' => '5 columns, 1/6 + 1/6 + 1/4 + 1/4 + 1/6',
                            '52' => '5 columns, 1/6 + 1/4 + 1/4 + 1/6 + 1/6',
                            '6' => '6 columns'
                        )
                    ),
                    array(
                        'id' => 'footer_lastcol_right',
                        'label' => esc_html__('Last column right align', 'tana'),
                        'default' => '0',
                        'type' => 'switch'
                    ),

                    array(
                        'id' => 'footer-title-size',
                        'label' => esc_html__('Footer Title Font Size', 'tana'),
                        'default' => getLessValue('footer-title-size'),
                        'type' => 'pixel'
                    ),

                    // Sub Footer
                    array(
                        'id' => 'sub_footer_title',
                        'type' => 'sub_title',
                        'label' => esc_html__('Sub Footer Options', 'tana'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'sub_footer',
                        'label' => esc_html__('Enable Sub Footer', 'tana'),
                        'default' => '1',
                        'type' => 'switch'
                    ),
                    array(
                        'id' => 'sub_footer_mini',
                        'label' => esc_html__('Mini Style', 'tana'),
                        'default' => '0',
                        'type' => 'switch',
                        'desc' => esc_html__('No border, no social, no meta, less padding etc.', 'tana')
                    ),
                    array(
                        'id' => 'copyright_content',
                        'label' => esc_html__('CopyRight Content', 'tana'),
                        'default' => esc_html__('Copyright 2016 &copy; Themeton | All Rights Reserved.', 'tana'),
                        'desc' => '',
                        'type' => 'textarea'
                    ),
                    array(
                        'id' => 'sub_footer_locationmeta',
                        'label' => esc_html__('Location meta', 'tana'),
                        'default' => '0',
                        'desc' => esc_html__('Weather temprature and Date. Set details on Header tab.', 'tana'),
                        'type' => 'switch'
                    )


                ),
            ), // end Footer



            // Extras
            array(
                'id' => 'panel_extra',
                'label' => esc_html__('Extras', 'tana'),
                'desc' => esc_html__('Export Import and Custom CSS.', 'tana'),
                'sections' => array(
                    // Settings
                    array(
                        'type' => 'section',
                        'id' => 'section_settings',
                        'label' => esc_html__('Settings', 'tana'),
                        'desc' => '',
                        'controls' => array(
                            array(
                                'id' => 'transport_mode',
                                'label' => esc_html__('Customizer Transport', 'tana'),
                                'desc' => esc_html__('Transport setting for customizer event when you change customizer element value.', 'tana'),
                                'default' => 'refresh',
                                'type' => 'select',
                                'choices' => array(
                                    'refresh' => esc_html__('Refresh when change value', 'tana'),
                                    'postMessage' => esc_html__('Collect changes until save', 'tana')
                                )
                            ),
                            array(
                                'id' => 'preloader_disable',
                                'label' => esc_html__('Pre Loader Disable', 'tana'),
                                'default' => '0',
                                'type' => 'switch',
                            ),
                            array(
                                'id' => 'video_lightbox_disable',
                                'label' => esc_html__('Lightbox Disable Globally', 'tana'),
                                'default' => '0',
                                'type' => 'switch',
                                'desc' => esc_html__('On mega menu, category and elements.', 'tana'),
                            ),
                        )
                    ), // end settings

                    // Custom Widget
                    array(
                        'type' => 'section',
                        'id' => 'section_custom_sidebars',
                        'label' => esc_html__('Custom Sidebars', 'tana'),
                        'desc' => '',
                        'controls' => array(
                            array(
                                'type' => 'textarea',
                                'id' => 'custom_sidebars',
                                'label' => esc_html__('Custom Sidebars', 'tana'),
                                'desc' => esc_html__('Enter sidebar id seperate by "," Example: [sidebar_id1, sidebar_id2]', 'tana'),
                                'default' => ''
                            )
                        )
                    ), // end custom widgets

                    // Backup
                    array(
                        'type' => 'section',
                        'id' => 'section_backup',
                        'label' => esc_html__('Export/Import', 'tana'),
                        'desc' => '',
                        'controls' => array(
                            array(
                                'id' => 'backup_settings',
                                'label' => esc_html__('Export Data', 'tana'),
                                'desc' => esc_html__('Copy to Customizer Data', 'tana'),
                                'default' => '',
                                'type' => 'backup'
                            ),
                            array(
                                'id' => 'import_settings',
                                'label' => esc_html__('Import Data', 'tana'),
                                'desc' => esc_html__('Import Customizer Exported Data', 'tana'),
                                'default' => '',
                                'type' => 'import'
                            )
                        )
                    ), // end backup
                    // Custom
                    array(
                        'type' => 'section',
                        'id' => 'section_custom_css',
                        'label' => esc_html__('Custom CSS', 'tana'),
                        'desc' => '',
                        'controls' => array(
                            array(
                                'id' => 'custom_css',
                                'label' => esc_html__('Custom CSS (general)', 'tana'),
                                'default' => '',
                                'type' => 'textarea'
                            ),
                            array(
                                'id' => 'custom_css_tablet',
                                'label' => esc_html__('Tablet CSS', 'tana'),
                                'desc' => esc_html__('Screen width between 768px and 991px.', 'tana'),
                                'default' => '',
                                'type' => 'textarea'
                            ),
                            array(
                                'id' => 'custom_css_widephone',
                                'label' => esc_html__('Wide Phone CSS', 'tana'),
                                'desc' => esc_html__('Screen width between 481px and 767px. Ex: iPhone landscape.', 'tana'),
                                'default' => '',
                                'type' => 'textarea'
                            ),
                            array(
                                'id' => 'custom_css_phone',
                                'label' => esc_html__('Phone CSS', 'tana'),
                                'desc' => esc_html__('Screen width up to 480px. Ex: iPhone portrait.', 'tana'),
                                'default' => '',
                                'type' => 'textarea'
                            ),
                        )
                    ), // end Custom
                    // ADS
                    array(
                        'type' => 'section',
                        'id' => 'section_custom_ads',
                        'label' => esc_html__('Adsense', 'tana'),
                        'desc' => '',
                        'controls' => array(
                            array(
                                'id' => 'ads_post_top',
                                'label' => esc_html__('Post top', 'tana'),
                                'default' => '',
                                'type' => 'textarea'
                            ),
                            array(
                                'id' => 'ads_post_bottom',
                                'label' => esc_html__('Post bottom', 'tana'),
                                'default' => '',
                                'type' => 'textarea'
                            ),
                            array(
                                'id' => 'ads_page_top',
                                'label' => esc_html__('Page top', 'tana'),
                                'default' => '',
                                'type' => 'textarea'
                            ),
                            array(
                                'id' => 'ads_page_bottom',
                                'label' => esc_html__('Page bottom', 'tana'),
                                'default' => '',
                                'type' => 'textarea'
                            ),
                        )
                    ) // end ADS
                )
            ) // end Extras
        );

        // remove deprecated items
        if( function_exists('get_custom_logo') ){
            for( $i=0; $i<count($option); $i++ ){
                if( isset($option[$i]['id']) && $option[$i]['id']=='section_header_style' ){
                    for( $j=0; $j<count($option[$i]['controls']); $j++ ){
                        if( $option[$i]['controls'][$j]['id']=='logo' || $option[$i]['controls'][$j]['id']=='favicon' ){
                            unset($option[$i]['controls'][$j]);
                        }
                    }
                }
            }
        }

        return $option;
    }

endif;


function tt_theme_customize_setup(){
    // create instance of TT Theme Customizer
    new TT_Theme_Customizer();
}
add_action( 'after_setup_theme', 'tt_theme_customize_setup' );
