<?php

class WPBakeryShortCode_tana_carousel_slider extends WPBakeryShortCode {
    protected function content( $atts, $content = null){

        // Initial argument sets
        extract(shortcode_atts(array(
            'count' => '6',
            'columns' => '4',
            'ratio' => '2x3',
            'posttypes' => 'post',
            'categories' => '',
            'autoplay' => '0',
            'autoplay_seconds' => '5',
            'excludes' => '',
            'extra_class' => ''
        ), $atts));

        // Initial query sets
        $args = array(
                    'post_type' => 'post',
                    'posts_per_page' => abs($count),
                    'ignore_sticky_posts' => true
                );

        // Include categories
        if(!empty($categories) && ($posttypes == 'post' || $posttypes== '')){
            $args['category_name'] = str_replace(' ', '', $categories);
        }

        // Custom post types
        $pt = array();
        if ( '' !== $posttypes && $posttypes !== 'post' ) {
            $posttypes = explode( ',', $posttypes );
            foreach ( $posttypes as $post_type ) {
                array_push( $pt, $post_type );
            }
            $args['post_type'] = $pt[0];

            if ($categories != '' && $categories != '0') {
                $args['tax_query'] = array();
                $categories = explode( ',', $categories );
                foreach($categories as $cat) {
                    $taxonomies = get_object_taxonomies($pt[0]);
                    $args['tax_query'] = array_merge($args['tax_query'],array(
                        'relation' => 'OR',
                        array(
                            'taxonomy' => $taxonomies[0],
                            'field' => 'slug',
                            'terms' => $cat
                            )
                        ));
                }
            }
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


        // Variable descares
        $posts = '';
        $index = 0;


        // Query posts loop
        $posts_query = new WP_Query($args);
        if($posts_query->have_posts()) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                global $post;

                // Unique posts
                $tana_exclude_posts[] = $post->ID;

                // Reset markups
                $thumb = $catname = '';
                if(has_post_thumbnail(get_the_ID())) {
                    $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
                    $thumb = $thumb_src[0];
                }
                
                // Building posts markup
                $posts .= sprintf('<div class="swiper-slide">
                                    <div class="travel-item-boxed %6$s">
                                        <div class="entry-img">
                                            <a href="%4$s" class="image entry-link" data-src="%3$s">
                                                <img src="%7$s" class="img-spacer" alt="%1$s"/>
                                            </a>
                                        </div>
                                        <span class="price label">%5$s</span>
                                        <div class="entry-info">
                                            <h3><a href="%4$s">%1$s</a></h3>
                                            <div class="desc">%2$s</div>
                                        </div>
                                    </div>
                                </div>', 
                                get_the_title(),                        // 1
                                get_the_excerpt(),                      // 2
                                $thumb,                                 // 3
                                get_the_permalink(),                    // 4
                                esc_html(TT::getmeta('label')),         // 5
                                TT::getmeta('color'),                   // 6
                                get_template_directory_uri().'/images/'.$ratio.'.png' // 7
                            );

            } // end while
        } else {
            $description = '';
            if(Tana_Std::get_mod('unique_posts') == '1'){ $description = esc_attr__('Consider to turn off Unique posts option.', 'tana');}
            else { $description =  esc_attr__('Please make sure you are added enough posts into your selected category.', 'tana'); }
            return esc_attr__('No posts found!', 'tana') . ' ' . $description;
        }

        $tana_exclude_posts = array_unique($tana_exclude_posts);

        // reset query
        wp_reset_postdata();

        // Extra class
        $extra_class = esc_attr($extra_class);

        // Auto play
        $auto_play_attr = $autoplay == '1' ? $autoplay_seconds : '0';
        $auto_play_attr = abs($auto_play_attr)*1000;

        // Final result
        return "<div class='carousel-travel' data-columns='$columns' data-space='0' data-autoplay='$auto_play_attr'>
                    <div class='swiper-container'>
                        <div class='swiper-wrapper'>

                            $posts

                        </div>
                    </div>

                    <div class='swiper-button-prev'></div>
                    <div class='swiper-button-next'></div>

                </div>
                <!-- /.carousel-travel -->";

    }

}

// Element options
vc_map( array(
    "name" => esc_html__('Carousel Slider', 'tana'),
    "description" => esc_html__("Column slideshow", 'tana'),
    "base" => 'tana_carousel_slider',
    "icon" => "tana-vc-icon tana-vc-icon16",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(

        array(
            "type" => "textfield",
            "param_name" => "count",
            "heading" => esc_html__("Posts Limit", 'tana'),
            "value" => "6",
            "holder" => "div"
        ),
        array(
            "type" => "textfield",
            "param_name" => "columns",
            "heading" => esc_html__("View columns", 'tana'),
            "value" => "4"
        ),
        array(
            "type" => "dropdown",
            "param_name" => "ratio",
            "heading" => esc_html__("Image ratio", 'tana'),
            "value" => array(
                "1x1 (tile)" => "1x1",
                "1x2" => "1x2",
                "2x1" => "2x1",
                "1x3" => "1x3",
                "3x1" => "3x1",
                "2x3 (default)" => "2x3",
                "3x2" => "3x2",
                "3x4" => "3x4",
                "4x3" => "4x3",
                "3x5" => "3x5",
                "5x3" => "5x3",
                "8x5" => "8x5",
                "8x7" => "8x7",
                "8x15" => "8x15",
                "16x7" => "16x7",
            ),
            "std" => "2x3",
        ),

        array(
            "type" => "posttypes",
            "param_name" => "posttypes",
            "heading" => esc_html__("Post type", 'tana'),
            "value" => "Select one desired post type. Please do not select if your posts are regular posts.",
            "description" => esc_html__("Select only one type.", 'tana'),
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
            "type" => "textfield",
            "param_name" => "extra_class",
            "heading" => esc_html__("Extra Class", 'tana'),
            "value" => "",
            "description" => esc_html__("If you wish text to white, you should add class \"text-light\". If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'tana'),
        )
    )
));