<?php

class WPBakeryShortCode_Tt_News_Slider extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'count' => '6',
            'post_type' => 'post',
            'categories' => '',
            'photostyle' => '0',
            'hoverstyle' => '0',
            'title' => '1',
            'autoplay' => '0',
            'autoplay_seconds' => '5',
            'excerpt' => '1',
            'meta' => '1',
            'excludes' => '',
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
        $slides = '';


        // Gathering slider posts as slides
        $posts_query = new WP_Query($args);
        if($posts_query->have_posts()) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                global $post;

                // Unique posts
                $tana_exclude_posts[] = $post->ID;

                // Element detail markups
                $thumbnail = $thumb = $image_url = "";
                if(has_post_thumbnail(get_the_ID())) {
                    $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'tana-slider-thumbnail');
                    $image_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'large');
                    $thumb = $thumb_src[0];
                    $image_url = $image_src[0];
                }
                $thumbnail = "<div class='image' data-src='".$thumb."'></div>";
                $titlemarkup = $title == '1' ? "<h4 class='animate-element' data-anim='fadeInUp'><a href='".get_permalink()."'>".get_the_title()."</a></h4>" : "";
                $titlemarkup_noanim = $title == '1' ? "<h4><a href='".get_permalink()."'>".get_the_title()."</a></h4>" : "";
                if($hoverstyle == '1') {
                    $thumbnail = '';
                    $titlemarkup_noanim = $title == '1' ? "<h4>".get_the_title()."</h4>" : "";
                }
                $metamarkup = $meta == '1' ? "<div class='meta animate-element' data-anim='fadeInUp'><span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span><span class='date'>".get_the_date()."</span></div>" : "";
                $metamarkup_noanim = $meta == '1' ? "<div class='meta'><span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span><span class='date'>".get_the_date()."</span></div>" : "";
                $excerptmarkup = $excerpt == '1' ? "<p class='animate-element' data-anim='fadeInUp'>".get_the_excerpt()."</p>" : "";
                $thumbnailmarkup = "<div class='thumb-meta'>$metamarkup_noanim$titlemarkup_noanim</div>";
                if($photostyle == '1') {$thumbnailmarkup = "$metamarkup_noanim $titlemarkup_noanim";}

                // Building a slide
                $slides .= "<div class='ms-slide' data-delay='".abs($autoplay_seconds)."'>
                                <div class='slide-pattern'></div>
                                <img src='".get_template_directory_uri()."/vendors/masterslider/style/blank.gif' data-src='".esc_attr($image_url)."' alt='".esc_attr__('Image', 'tana')."'/>
                                <div class='ms-thumb post hover-zoom'>
                                    $thumbnail
                                    $thumbnailmarkup
                                </div>
                                
                                <div class='ms-layer box' data-delay='0' data-effect='bottom(45)' data-duration='300' data-ease='easeInOut'>
    
                                    $metamarkup
                                    $titlemarkup
                                    $excerptmarkup

                                </div>

                            </div>";                    
            } // end while
        } else {
            $description = '';
            if(Tana_Std::get_mod('unique_posts') == '1'){ $description = esc_attr__('Consider to turn off Unique posts option.', 'tana');}
            else { $description =  esc_attr__('Please make sure you are added enough posts into your selected category.', 'tana'); }
            return esc_attr__('No posts found!', 'tana') . ' ' . $description;
        }

        $tana_exclude_posts = array_unique($tana_exclude_posts);

        // Reset query
        wp_reset_postdata();

        // Extra class
        $extra_class = esc_attr($extra_class);

        // Alternate style class or Hover style
        $photoclass = $photostyle == '1' ? ' photo-news-slider' : '';
        $photoclass = $hoverstyle == '1' ?  ' news-slider-hover' : $photoclass;

        // Auto play
        $auto_play_attr = $autoplay == '1' ? ' data-autoplay="true"' : '';


        if($slides != '') {
            $result = "<div class='news-slider$photoclass news-block mv5 mvt0 $extra_class'>

                    <div class='master-slider ms-skin-default' id='masterslider".uniqid()."'$auto_play_attr>
        
                        $slides

                    </div>
                    <!-- end of masterslider -->

                </div>
                <!-- end .news-slider -->";
        } else {
            $result = '<h3>No slider posts found!</h3>';
        }


        // Final result
        return $result;

    }

}


$post_types = array();
$data_post_types = Tana_Std::get_post_types();
foreach ($data_post_types as $key => $value) {
    $post_types[$value] = $key;
}


// Element options
vc_map( array(
    "name" => esc_html__('News slider', 'tana'),
    "description" => esc_html__("News slideshow", 'tana'),
    "base" => 'tt_news_slider',
    "icon" => "tana-vc-icon tana-vc-icon08",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(

        array(
            "type" => "textfield",
            "param_name" => "count",
            "heading" => esc_html__("Posts count", 'tana'),
            "value" => "6"
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
            "value" => '',
            "holder" => "div"
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "photostyle",
            "heading" => esc_html__("Another style used in Picture slider", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0",
            "description" => esc_html__("Slight a different variation. Selected text on thumbnails and meta difference.", 'tana'),
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "hoverstyle",
            "heading" => esc_html__("Hover style", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0",
            "description" => esc_html__("Slider thumbnail and everything hover the images.", 'tana'),
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "title",
            "heading" => esc_html__("Title", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
        ),
        array(
            'type' => 'checkbox',
            "param_name" => "excerpt",
            "heading" => esc_html__("Excerpt", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
        ),
        array(
            'type' => 'checkbox',
            "param_name" => "meta",
            "heading" => esc_html__("Meta", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
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