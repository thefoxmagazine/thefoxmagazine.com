<?php

class WPBakeryShortCode_Tt_Movie_Slider_Alt extends WPBakeryShortCode {
    protected function content( $atts, $content = null){

        // Initial argument sets
        extract(shortcode_atts(array(
            'count' => '12',
            'post_type' => 'post',
            'categories' => '',
            'autoplay' => '0',
            'autoplay_seconds' => '5',
            'excludes' => '',
            'layout' => 'with-chart',
            'text_color' => 'white',
            'skin' => 'ms-skin-light',
            'extra_class' => ''
        ), $atts));


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
        global $tana_exclude_posts;
        if( !empty($tana_exclude_posts) && Tana_Std::get_mod('unique_posts') == '1' ){
            $args['post__not_in'] = $tana_exclude_posts;
        }
        if( !empty($excludes) ){
            $printed = isset($args['post__not_in']) ? $args['post__not_in'] : array();
            $args['post__not_in'] = array_merge(explode(",", $excludes), $printed);
        }


        // Variable declares
        $posts = '';
        $index = 0;

        $main_slides = '';
        $thumb_slides = '';

        // Query posts loop
        $posts_query = new WP_Query($args);
        if($posts_query->have_posts()) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                global $post;

                // Unique posts
                $tana_exclude_posts[] = $post->ID;

                $img_src_full = '';
                $img_src_thumb = '';

                // Reset markups
                $imagemarkup = $postclass = '';
                if(has_post_thumbnail(get_the_ID())) {
                    $bg_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
                    $thumb_src = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'tana-slider-thumbnail');
                    $img_src_full = !empty($bg_src) ? $bg_src[0] : '';
                    $img_src_thumb = !empty($thumb_src) ? $thumb_src[0] : '';
                }
                
                // title and excerpt
                $titlemarkup = sprintf(
                    '<h3 class="animate-element">
                        <a href="%s">%s</a>
                    </h3>',
                    get_permalink(), get_the_title()
                );
                $excerptmarkup = sprintf( '<div class="excerpt animate-element">%s</div>', get_the_excerpt() );
                
                // label
                $labelmarkup = '';
                $label = TT::getmeta('label');
                if( !empty($label) ){
                    $labelmarkup = "<a href='".get_permalink()."' class='label animate-element'>".esc_html($label)."</a>";
                }

                // watch trailer
                $trailermarkup = '';
                $meta_trailer = TT::getmeta('trailer');
                if( !empty($meta_trailer) ){
                    $url = TT::getmeta('trailer');
                    $playerclass = '';

                    if( preg_match('/youtu\.be/i', $url) || preg_match('/youtube\.com\/watch/i', $url) || preg_match('/vimeo\.com/i', $url) ){
                        $playerclass = ' video-player';
                    }

                    $trailermarkup = sprintf(
                        '<a class="icon-link animate-element%s" data-anim="fadeInUp" href="%s">
                            %s <img src="%s/images/entertainment/icon-play.png" alt="%s">
                        </a>',
                        esc_attr($playerclass), esc_url($url), esc_html__('Watch Trailer', 'tana'),
                        get_template_directory_uri(), esc_html__('Icon', 'tana')
                    );
                }

                // read more
                $linkmarkup = sprintf(
                    '<a class="icon-link animate-element" data-anim="fadeInUp" href="%s">
                        %s <img src="%s/images/entertainment/icon-magnifier.png" alt="%s">
                    </a>',
                    get_permalink(), esc_html__('Read more', 'tana'), get_template_directory_uri(), esc_attr__('Icon', 'tana')
                );

                $read_more = sprintf( '<div class="read-more animate-element">%s%s</div>' , $trailermarkup, $linkmarkup );

                // rate
                $starsmarkup = '';
                $rate_value = abs(TT::getmeta('customer_rate'));
                for ($k=0; $k < 5; $k++) {
                    $starsmarkup .= sprintf( '<i class="fa fa-star%s"></i>', ($rate_value > $k ? ' color-1' : '') );
                }
                $starsmarkup = sprintf( '<div class="rate animate-element">%s</div>', $starsmarkup );


                // circle
                $number = abs(TT::getmeta('imdb_rate'));
                $chartmarkup = sprintf(
                    '<div class="circle-chart" data-circle-width="7" data-percent="%d" data-text="%d <small>IMDB</small>"></div>',
                    $number*10, $number
                );

                // meta author, date
                $metathumbmarkup = sprintf(
                    '<div class="meta animate-element">
                        <span class="author">%s</span>
                        <span class="date">%s</span> %s
                    </div>',
                    get_the_author_meta( 'display_name', $post->post_author ), get_the_date(), ($layout=='with-chart' ? $chartmarkup : '')
                );

                // build item content
                $item_content = sprintf( '%s%s%s', $titlemarkup, $metathumbmarkup, $excerptmarkup );

                if( $layout=='with-chart' ){
                    $item_content = sprintf( '%s%s%s%s%s', $starsmarkup, $titlemarkup, $metathumbmarkup, $excerptmarkup, $read_more );
                }
                else if( $layout=='product' ){
                    $price = '<span class="price">$0.00</span>';
                    $read_more_link = sprintf( '<a href="%s" class="post-link">%s</a>', get_permalink(), esc_html__('Discover Collection', 'tana') );
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

                        ob_start();
                        woocommerce_show_product_loop_sale_flash();
                        $labelmarkup = ob_get_contents();
                        ob_end_clean();

                        if( !empty($labelmarkup) && strpos($labelmarkup, '<span')!==false ){
                            $labelmarkup = sprintf( '<a href="%s" class="label animate-element">%s</a>', get_permalink(), $labelmarkup );
                        }
                    }

                    $post_cats = wp_get_post_terms( get_the_ID(), 'product_cat' );
                    $last_cat = array('name'=>'', 'link'=>'', 'desc'=>'');
                    if( !empty($post_cats) ){
                        foreach( $post_cats as $category ){
                            $metathumbmarkup = sprintf(
                                '<div class="meta animate-element"><span class="author">%s</span></div>',
                                $category->name
                            );
                        }
                    }

                    $item_content = sprintf(
                        '<div class="product-price animate-element">%s%s</div>
                        %s%s%s
                        <div class="read-more animate-element">%s%s</div>',
                        esc_html__('Starting at', 'tana'), $price, $titlemarkup, $metathumbmarkup, $excerptmarkup, $read_more_link, $add2cart
                    );
                }

                $postclass = sprintf( 'text-%s %s', esc_attr($text_color), esc_attr(TT::getmeta('color')) );

                $main_slides .= sprintf(
                    '<div class="swiper-slide" style="background-image:url(%s);">
                        <div class="ts-entry-item %s">
                            <div class="tse-content">%s</div>%s
                        </div>
                    </div>',
                    esc_url($img_src_full), esc_attr($postclass), $item_content, $labelmarkup
                );

                $thumb_slides .= sprintf(
                    '<div class="swiper-slide" style="background-image:url(%s);">
                        <img src="%s/images/16x9.png" alt="%s">
                    </div>',
                    esc_url($img_src_thumb), get_template_directory_uri(), esc_html__('Image Ratio', 'tana')
                );

            } // end while

        }
        else {
            $description = '';
            if( Tana_Std::get_mod('unique_posts') == '1' ){
                $description = esc_html__('Consider to turn off Unique posts option.', 'tana');
            }
            else {
                $description =  esc_html__('Please make sure you are added enough posts into your selected category.', 'tana');
            }
            return esc_html__('No posts found!', 'tana') . ' ' . $description;
        }

        $tana_exclude_posts = array_unique($tana_exclude_posts);

        // Reset query
        wp_reset_postdata();

        // Extra class
        $extra_class = esc_attr($extra_class);

        // Auto play
        $data_auto_play = $autoplay == '1' ? abs($autoplay_seconds) : 0;

        // Final result
        return sprintf( '<div class="tana-element tana-slider-with-thumb layout-%s %s" data-autoplay="%s">
                            <div class="ts-main-wrap">
                                <div class="swiper-container ts-main-panel">
                                    <div class="swiper-wrapper">%s</div>
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                            <div class="swiper-container ts-thumbs-panel">
                                <div class="swiper-wrapper">%s</div>
                            </div>
                            <div class="swiper-bottom-arrows">
                                <div class="sba-left"></div>
                                <div class="sba-right"></div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>', esc_attr($layout), esc_attr($skin), $data_auto_play, $main_slides, $thumb_slides );
    }

}


$post_types = array();
$data_post_types = Tana_Std::get_post_types();
foreach ($data_post_types as $key => $value) {
    $post_types[$value] = $key;
}


$layouts = array(
    esc_html__('With Chart Cycle', 'tana') => "with-chart",
    esc_html__('No Chart Cycle', 'tana') => "no-chart"
);

if( class_exists('WooCommerce') ){
    $layouts[esc_html__('For Product', 'tana')] = 'product';
}

// Element options
vc_map( array(
    "name" => esc_html__('Movie Slider Alternation', 'tana'),
    "description" => esc_html__("Picture slideshow", 'tana'),
    "base" => 'tt_movie_slider_alt',
    "icon" => "tana-vc-icon tana-vc-icon14",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(

        array(
            "type" => "dropdown",
            "param_name" => "post_type",
            "heading" => esc_html__("Post Type", 'tana'),
            "value" => $post_types,
            "std" => "post",
        ),

        array(
            "type" => "textfield",
            "param_name" => "count",
            "heading" => esc_html__("Posts Limit", 'tana'),
            "value" => "5"
        ),

        array(
            "type" => 'textfield',
            "param_name" => "categories",
            "heading" => esc_html__("Categories", 'tana'),
            "description" => esc_html__("Specify category SLUG (not name or ID) or leave blank to display items from all categories. Ex: news,image.", 'tana'),
            "value" => '',
            "holder" => "div"
        ),
        array(
            'type' => 'checkbox',
            "param_name" => "autoplay",
            "heading" => esc_html__("Autoplay next slides", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0"
        ),
        array(
            'type' => 'textfield',
            "param_name" => "autoplay_seconds",
            "heading" => esc_html__("Autoplay seconds", 'tana'),
            'value' => "5",
            "std" => "5",
            'dependency' => array('element' => 'autoplay', 'value' => '1',),
        ),

        array(
            "type" => "textfield",
            "param_name" => "excludes",
            "heading" => esc_html__("Exclude posts", 'tana'),
            "value" => "",
            "description" => esc_html__("Please add post IDs separated by comma. Example: 125,1,65.", 'tana'),
        ),

        array(
            "type" => "dropdown",
            "param_name" => "layout",
            "heading" => esc_html__("Layout", 'tana'),
            "value" => $layouts,
            "std" => "with-chart"
        ),

        array(
            "type" => "dropdown",
            "param_name" => "text_color",
            "heading" => esc_html__("Text Color", 'tana'),
            "value" => array(
                esc_html__('White', 'tana') => "white",
                esc_html__('Black', 'tana') => "black"
            ),
            "std" => "white"
        ),

        array(
            "type" => "dropdown",
            "param_name" => "skin",
            "heading" => esc_html__("Skin", 'tana'),
            "value" => array(
                esc_html__('Light', 'tana') => "ms-skin-light",
                esc_html__('Dark', 'tana') => "ms-skin-dark"
            ),
            "std" => "ms-skin-light"
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