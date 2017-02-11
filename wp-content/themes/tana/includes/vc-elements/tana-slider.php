<?php

class WPBakeryShortCode_Tana_Post_Slider extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'type' => 'half_image',
            'post_type' => 'post',
            'categories' => '',
            'count' => '6',
            'bg_color' => '#fff',
            'bg_color1' => '#ff5c73',
            'bg_color2' => '#012bff',
            'bg_color_scale' => '500%',
            'text_color' => 'black',
            'bg_pattern' => '1',
            'arrow_middle' => '1',
            'arrow_bottom' => '1',
            'autoplay' => '0',
            'autoplay_seconds' => '5',
            'padding_top' => '260',
            'padding_bottom' => '260',
            'excludes' => '',
            'read_more' => esc_html__('read more', 'tana'),
            'extra_class' => ''
        ), $atts));

        $padding_top = abs($padding_top);
        $padding_bottom = abs($padding_bottom);
        $padding = sprintf('padding-top:%spx;padding-bottom:%spx;', $padding_top,$padding_bottom);

        // Initial query sets
        $args = array(
                    'post_type' => $post_type,
                    'posts_per_page' => abs($count),
                    'ignore_sticky_posts' => true
                );


        // Include categories
        $tax_name = Tana_Std::get_taxonomy_post_type($post_type);
        if( !empty($categories) ){
            $categories = str_replace(' ', '', $categories);
            $cats = explode( ',', $categories );
            $args['tax_query'] = array(
                'relation' => 'OR',
                array(
                    'taxonomy' => $tax_name,
                    'field' => 'slug',
                    'terms' => $cats
                )
            );
        }

        // Exclude posts
        if( !empty($excludes) ){
            $args['post__not_in'] = explode(',', $excludes);
        }

        // Auto play
        $autoplay_seconds = $autoplay == '1' ? $autoplay_seconds : '0';

        // Varialbe declares
        $result = '';

        // Query posts loop
        $posts_query = new WP_Query($args);
        while ( $posts_query->have_posts() ) {
            $posts_query->the_post();
            global $post;

            $thumb_src = '';
            $excerpt = Tana_Tpl::clear_urls(wp_trim_words( wp_strip_all_tags(do_shortcode(get_the_content())), 15 ));
            if( has_post_thumbnail() ){
                $img_src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                $thumb_src = !empty($img_src) ? $img_src[0] : '';
            }

            $post_cats = wp_get_post_terms( get_the_ID(), $tax_name );
            $last_cat = array('name'=>'', 'link'=>'', 'desc'=>'');
            if( !empty($post_cats) ){
                foreach( $post_cats as $category ){
                    $desc = category_description($category->term_id);
                    $desc = str_replace('<p>', '', $desc);
                    $desc = str_replace('</p>', '', $desc);
                    $desc = substr($desc, 0, 13);
                    $last_cat = array(
                            'name' => $category->name,
                            'link' => get_term_link($category),
                            'desc' => $desc
                        );
                }
            }

            if( $type=='full_image' ){
                // Builing posts markup
                $result .= sprintf( '<div class="swiper-slide">
                                        <div class="fs-item" style="background-color:%s;" data-views="%s" data-comments="%s">
                                            <div class="fs-entry-bg" style="background-image:url(%s);"></div>
                                            <div class="fn-slide-content">
                                                <div class="fs-entry-item" style="%s">
                                                    <h6 class="fs-animate-text"><a href="%s">%s</a></h6>
                                                    <h3 class="fs-animate-text">%s</h3>
                                                    <p class="fs-animate-text">%s</p>
                                                    <a href="%s" class="read-more fs-animate-text">%s</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>',
                                    $bg_color, Tana_Tpl::getPostViews(get_the_id()), get_comments_number(), esc_url($thumb_src), $padding,
                                    esc_url($last_cat['link']), $last_cat['name'], get_the_title(), $excerpt, get_permalink(), $read_more );
                
            }
            else if( $type=='half_no_excerpt' ){
                // Builing posts markup
                $video = Tana_Tpl::get_post_video_url();
                $video = !empty($video) ? sprintf('<a href="%s" class="play-button video-player">
                                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" xml:space="preserve">
                                                            <path d="M16,0C7.2,0,0,7.2,0,16c0,8.8,7.2,16,16,16c8.8,0,16-7.2,16-16C32,7.2,24.8,0,16,0z M16,30.9C7.8,30.9,1.1,24.2,1.1,16C1.1,7.8,7.8,1.1,16,1.1c8.2,0,14.9,6.7,14.9,14.9C30.9,24.2,24.2,30.9,16,30.9z"/>
                                                            <path d="M22.2,15.9l-8.7-5.9c-0.1-0.1-0.2-0.1-0.3,0c-0.1,0.1-0.2,0.2-0.2,0.3v11.8c0,0.1,0.1,0.2,0.2,0.3c0,0,0.1,0,0.1,0c0.1,0,0.1,0,0.2-0.1l8.7-5.9c0.1-0.1,0.1-0.1,0.1-0.2C22.4,16.1,22.3,16,22.2,15.9z"/>
                                                        </svg>
                                                    </a>', $video) : '';

                $result .= sprintf( '<div class="swiper-slide">
                                        <div class="fs-item" style="background-color:%s; %s">
                                            <div class="fs-entry-bg" style="background-image:url(%s);">
                                                %s
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="fs-entry-item">
                                                            <h4 class="fs-title fs-animate-text">%s</h4>
                                                            <h3 class="fs-animate-text">%s</h3>
                                                            <a href="%s" class="read-more fs-animate-text">%s</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>',
                                    $bg_color, $padding, esc_url($thumb_src), $video, $last_cat['name'], get_the_title(), get_permalink(), $read_more );
            }
            else if( $type=='product_slider' ){
                // Builing posts markup
                $price = '<span class="price">$0.00</span>';
                $read_more_link = sprintf('<a href="%s" class="read-more fs-animate-text">%s</a>', get_permalink(), $read_more);
                $add2cart = '';
                if( function_exists('woocommerce_template_loop_price') && $post_type=='product' ){
                    ob_start();
                    woocommerce_template_loop_price();
                    $price = ob_get_contents();
                    ob_end_clean();

                    ob_start();
                    woocommerce_template_loop_add_to_cart();
                    $add2cart = ob_get_contents();
                    ob_end_clean();
                    $add2cart = preg_replace('/ class="/', ' class="fs-animate-text ', $add2cart, 1);
                    
                }

                $post_cat = $last_cat['name'];
                $cat_exps = explode(' ', $post_cat);
                if( count($cat_exps)>1 ){
                    $post_cat = preg_replace('/ /', '</span><span>', $post_cat, 1);
                    $post_cat = sprintf('<span>%s</span>', $post_cat);
                }
                else{
                    $post_cat = sprintf('<span>%s</span>', $post_cat);
                }

                $result .= sprintf( '<div class="swiper-slide">
                                        <div class="fs-item" style="background-color:%s; %s">
                                            <div class="fs-entry-bg" style="background-image:url(%s);">
                                                <a href="%s" class="fs-entry-category">%s</a>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="fs-entry-item">
                                                            <h4 class="fs-title fs-animate-text">%s<span>%s</span></h4>
                                                            <h3 class="fs-animate-text">%s</h3>
                                                            <div class="fs-entry-links">%s%s</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>',
                                    $bg_color, $padding, esc_url($thumb_src), $last_cat['link'], $post_cat,
                                    esc_html__('Starting at', 'tana'), $price, get_the_title(), $read_more_link, $add2cart );
            }
            else{
                // Builing posts markup
                $result .= sprintf( '<div class="swiper-slide">
                                        <div class="fs-item blog-slide-item" style="background-color:%s; %s">
                                            <div class="fs-entry-bg" style="background-image:url(%s);"></div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="fs-entry-item">
                                                            <h4 class="fs-title fs-animate-text" data-label="%s">%s</h4>
                                                            <h3 class="fs-animate-text">%s</h3>
                                                            <p class="fs-animate-text">%s</p>
                                                            <a href="%s" class="read-more fs-animate-text">%s</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>',
                                    $bg_color, $padding, esc_url($thumb_src), $last_cat['desc'], $last_cat['name'], get_the_title(), $excerpt, get_permalink(), $read_more );
            }

        }// end while

        // Reset query
        wp_reset_postdata();

        $extras = '';

        if( $arrow_middle=='1' && in_array($type, array('half_image', 'half_no_excerpt')) ){
            $extras .= '<div class="fs-arrows">
                            <a href="javascript:;" class="fs-arrow-prev"><i class="fa fa-angle-left"></i> '.esc_attr__('Prev', 'tana').'</a>
                            <a href="javascript:;" class="fs-arrow-next">'.esc_attr__('Next', 'tana').' <i class="fa fa-angle-right"></i></a>
                        </div>';
        }
        if( $arrow_bottom=='1' || $type=='full_image' ){
            $extras .= '<div class="fs-arrows arrows-bottom">
                            <a href="javascript:;" class="fs-arrow-prev"><i class="fa fa-angle-left"></i></a>
                            <a href="javascript:;" class="fs-arrow-next"><i class="fa fa-angle-right"></i></a>
                        </div>';
        }

        if( $type=='full_image' ){
            $socials = Tana_Tpl::get_social_links(false);
            $extras .= sprintf('<div class="fs-pagination"></div>
                                <div class="fn-socials">%s</div>
                                <div class="fn-bottom">
                                    <span>
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 12 12" xml:space="preserve">
                                            <path d="M8.6,0c-1,0-2,0.5-2.6,1.3C5.4,0.5,4.4,0,3.4,0C1.5,0,0,1.6,0,3.5c0,1.5,0.9,3.3,2.6,5.3c1.3,1.5,2.8,2.7,3.2,3L6,12l0.2-0.2c0.4-0.3,1.8-1.5,3.2-3c1.7-2,2.6-3.7,2.6-5.3C12,1.6,10.5,0,8.6,0z M8.8,8.3C7.7,9.5,6.6,10.5,6,11c-0.6-0.5-1.7-1.5-2.8-2.7C1.6,6.5,0.8,4.8,0.8,3.5C0.8,2,2,0.8,3.4,0.8c0.9,0,1.8,0.5,2.3,1.4L6,2.9l0.3-0.7c0.5-0.9,1.3-1.4,2.3-1.4c1.4,0,2.6,1.2,2.6,2.7C11.2,4.8,10.4,6.5,8.8,8.3z"/>
                                        </svg>
                                        <span class="fn-meta-views">0</span>
                                    </span>
                                    <span><span class="fn-meta-comments">0</span> %s</span>
                                </div>', $socials, esc_html__('Comments', 'tana'));
        }

        $uniq_class = 'fns-'.uniqid();
        $classes = '';
        $classes = $type=='full_image' ? "fn-slide $uniq_class" : $classes;
        $classes = $type=='half_no_excerpt' ? 'ms-slide' : $classes;
        $classes = $type=='product_slider' ? 'ms-slide for-products' : $classes;

        $result = sprintf('<div class="section-full tana-slider fs-slide %s">
                                <div class="swiper-container" data-autoplay="%s">
                                    <div class="swiper-wrapper">%s</div>
                                </div>
                                %s
                            </div>',
                            $classes, abs($autoplay_seconds)*1000, $result, $extras );

        if( $type=='full_image' ){
            $result .= "<style type='text/css'>
                        .$uniq_class.fs-slide.fn-slide .fs-item .fn-slide-content::before{
                            background: $bg_color1;
                            background: -moz-linear-gradient(-45deg, $bg_color1 0%, $bg_color2 100%);
                            background: -webkit-linear-gradient(-45deg, $bg_color1 0%,$bg_color2 100%);
                            background: linear-gradient(135deg, $bg_color1 0%, $bg_color2 100%);
                            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='$bg_color1', endColorstr='$bg_color2',GradientType=1 );
                            width: $bg_color_scale;
                            height: $bg_color_scale;
                        }
                        </style>";
        }

        // return result
        return $result;

    }

}

// Element options

$tana_slider_types = array(
    "Half Image" => "half_image",
    "Half Image / No Excerpt" => "half_no_excerpt",
    "Full Image" => "full_image"
);

if( class_exists('WooCommerce') ){
    $tana_slider_types[esc_html__('For Products', 'tana')] = 'product_slider';
}


$post_types = array();
$data_post_types = Tana_Std::get_post_types();
foreach ($data_post_types as $key => $value) {
    $post_types[$value] = $key;
}

vc_map( array(
    "name" => esc_html__('Tana Slider', 'tana'),
    "description" => esc_html__("Slider contains posts", 'tana'),
    "base" => 'tana_post_slider',
    "icon" => "tana-vc-icon tana-vc-icon12",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(

        array(
            "type" => "dropdown",
            "param_name" => "type",
            "heading" => esc_html__("Slider Type", 'tana'),
            "value" => $tana_slider_types,
            "std" => "half_image",
            "holder" => 'div'
        ),

        array(
            "type" => "dropdown",
            "param_name" => "post_type",
            "heading" => esc_html__("Post Type", 'tana'),
            "value" => $post_types,
            "std" => "post",
        ),

        array(
            "type" => 'textfield',
            "param_name" => "categories",
            "heading" => esc_html__("Categories", 'tana'),
            "description" => esc_html__("Specify category SLUG (not name) or leave blank to display items from all categories. Ex: news,image.", 'tana'),
            "value" => ''
        ),

        array(
            "type" => "textfield",
            "param_name" => "count",
            "heading" => esc_html__("Posts Limit", 'tana'),
            "value" => "6"
        ),

        array(
            'type' => 'colorpicker',
            "param_name" => "bg_color",
            "heading" => esc_html__("Background Color", 'tana'),
            "value" => '#ffffff',
            "dependency" => Array("element" => "type", "value" => array('half_image', 'half_no_excerpt', 'product_slider'))
        ),

        array(
            'type' => 'colorpicker',
            "param_name" => "bg_color1",
            "heading" => esc_html__('Background Color 1 for Gradient', 'tana'),
            "value" => '#ff5c73',
            "dependency" => Array("element" => "type", "value" => array('full_image'))
        ),

        array(
            'type' => 'colorpicker',
            "param_name" => "bg_color2",
            "heading" => esc_html__('Background Color 2 for Gradient', 'tana'),
            "value" => '#012bff',
            "dependency" => Array("element" => "type", "value" => array('full_image'))
        ),

        array(
            "type" => "dropdown",
            "param_name" => "bg_color_scale",
            "heading" => esc_html__("Background Gradient Scale", 'tana'),
            "value" => array(
                "100%" => "100%",
                "200%" => "200%",
                "300%" => "300%",
                "400%" => "400%",
                "500%" => "500%"
            ),
            "std" => "500%",
            "dependency" => Array("element" => "type", "value" => array('full_image'))
        ),

        array(
            "type" => "dropdown",
            "param_name" => "text_color",
            "heading" => esc_html__("Text Color", 'tana'),
            "value" => array(
                esc_html__('Black', 'tana') => 'black',
                esc_html__('White', 'tana') => 'white'
            ),
            "std" => "default"
        ),

        array(
            'type' => 'checkbox',
            'param_name' => "bg_pattern",
            'heading' => esc_html__("Background Pattern", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            'std' => "1",
            "dependency" => Array("element" => "type", "value" => array('half_no_excerpt'))
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "arrow_middle",
            "heading" => esc_html__("Middle Next/Prev", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1",
            "dependency" => Array("element" => "type", "value" => array('half_image', 'half_no_excerpt'))
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "arrow_bottom",
            "heading" => esc_html__("Bottom Next/Prev", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1",
            "dependency" => Array("element" => "type", "value" => array('half_image', 'half_no_excerpt'))
        ),
        array(
            'type' => 'checkbox',
            "param_name" => "autoplay",
            "heading" => esc_html__("Autoplay", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0",
        ),
        array(
            'type' => 'textfield',
            "param_name" => "autoplay_seconds",
            "heading" => esc_html__("Autoplay", 'tana'),
            'value' => "5",
            "std" => "5",
            "dependency" => Array("element" => "autoplay", "value" => array('1'))
        ),

        array(
            "type" => "textfield",
            "param_name" => "padding_top",
            "heading" => esc_html__("Padding Top", 'tana'),
            "value" => "260",
            "description" => esc_html__("Please enter only numbers. Example: { 250 }", 'tana'),
        ),
        array(
            "type" => "textfield",
            "param_name" => "padding_bottom",
            "heading" => esc_html__("Padding Bottom", 'tana'),
            "value" => "260",
            "description" => esc_html__("Please enter only numbers. Example: { 250 }", 'tana'),
        ),

        array(
            "type" => "textfield",
            "param_name" => "excludes",
            "heading" => esc_html__("Exclude posts", 'tana'),
            "value" => "",
            "description" => esc_html__("Please add post IDs separated by comma. Example: 125,1,65.", 'tana'),
        ),

        array(
            "type" => 'textfield',
            "param_name" => "read_more",
            "heading" => esc_html__("Read more text", 'tana'),
            "value" => esc_html__("read more", 'tana')
        ),

        array(
            "type" => "textfield",
            "param_name" => "extra_class",
            "heading" => esc_html__("Extra Class", 'tana'),
            "value" => "",
            "description" => esc_html__("If you wish text to white. If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'tana'),
        )
    )
));