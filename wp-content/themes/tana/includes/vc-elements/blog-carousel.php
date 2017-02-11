<?php

class WPBakeryShortCode_Tt_Blog_Carousel extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'columns' => '3',
            'rows' => '1',
            'count' => '12',
            'post_type' => 'post',
            'categories' => '',
            'thumbnail' => '1',
            'ratio' => '5x3',
            'title' => '1',
            'excerpt' => '1',
            'meta' => '1',
            'pagination' => '0',
            'playbtn' => '',
            'playpermalink' => '',
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


        // Varible declares
        $slides = '';


        // Query posts loop
        $posts_query = new WP_Query($args);
        if($posts_query->have_posts()) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                global $post;

                // Unique posts
                $tana_exclude_posts[] = $post->ID;

                // Detail markups
                $thumb = $thumbnail == '1' ? Tana_Tpl::get_post_image('tana-blog-grid', $ratio, $playbtn.' '.$playpermalink) : "";
                $titlemarkup = $title == '1' ? "<h4><a href='".get_permalink()."'>".get_the_title()."</a></h4>" : "";
                $metamarkup = $meta == '1' ? "<div class='meta'><span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span><span class='date'>".get_the_date()."</span></div>" : "";
                $excerptmarkup = $excerpt == '1' ? "<p>".get_the_excerpt()."</p>" : "";

                $slides .= "<div class='swiper-slide'>
                    <div class='category-block articles'>
                        <div class='post hover-dark'>

                            $thumb
                            $metamarkup
                            $titlemarkup
                            $excerptmarkup

                        </div>
                    </div>
                </div>";
            
            }// end while
        }
        else {
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

        $result = "<div class='m-dimension-carousel news-block $extra_class' data-col='$columns' data-row='$rows'>
            <div class='swiper-container carousel-container'>
                <div class='swiper-wrapper'>

                    $slides

                </div>
                <!-- end .swiper-wrapper -->

                <div class='pagination-next-prev mt3'>
                    <a href='javascript:;' class='swiper-button-prev arrow-link' title='".esc_attr__('Prev','tana')."'><img src='".get_template_directory_uri()."/images/arrow-left.png' alt='".esc_attr__('Arrow', 'tana')."'></a>
                    <a href='javascript:;' class='swiper-button-next arrow-link' title='".esc_attr__('Next','tana')."'><img src='".get_template_directory_uri()."/images/arrow-right.png' alt='".esc_attr__('Arrow', 'tana')."'></a>
                </div>
                
            </div>
            <!-- end .swiper-container -->
        </div>
        <!-- end .m-dimension-carousel -->";

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
    "name" => esc_html__('Blog Carousel', 'tana'),
    "description" => esc_html__("Carousel Slide Blog", 'tana'),
    "base" => 'tt_blog_carousel',
    "icon" => "tana-vc-icon tana-vc-icon01",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(
        
        array(
            "type" => "dropdown",
            "param_name" => "columns",
            "heading" => esc_html__("Columns", 'tana'),
            "value" => array(
                "4 Columns" => "4",
                "3 Columns" => "3",
                "2 Columns" => "2",
                "1 Column" => "1"
            ),
            "std" => "3",
            "holder" => "div"
        ),
        array(
            "type" => "dropdown",
            "param_name" => "rows",
            "heading" => esc_html__("Rows", 'tana'),
            "value" => array(
                "3 Rows" => "3",
                "2 Rows" => "2",
                "1 Row" => "1"
            ),
            "std" => "1"
        ),

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
            "value" => "12"
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
            "param_name" => "thumbnail",
            "heading" => esc_html__("Post thumbnail image", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
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
                "2x3" => "2x3",
                "3x2" => "3x2",
                "3x4" => "3x4",
                "4x3" => "4x3",
                "3x5" => "3x5",
                "5x3 (default)" => "5x3",
                "8x5" => "8x5",
                "8x7" => "8x7",
                "8x15" => "8x15",
                "16x7" => "16x7",
            ),
            "std" => "5x3",
            'dependency' => array('element' => 'thumbnail', 'value' => '1',),
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
            "param_name" => "pagination",
            "heading" => esc_html__("Pagination", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0"
        ),


        array(
            "type" => "dropdown",
            "param_name" => "playbtn",
            "heading" => esc_html__("Play button style", 'tana'),
            "value" => array(
                esc_html__("Center large", 'tana') => "video-player-center video-player-large",
                esc_html__("Center medium", 'tana') => "video-player-center",
                esc_html__("Center small", 'tana') => "video-player-center video-player-small",
                esc_html__("Inner left bottom, large", 'tana') => "video-player-inside video-player-large",
                esc_html__("Inner left bottom, medium", 'tana') => "video-player-inside",
                esc_html__("Inner left bottom, small", 'tana') => "video-player-inside video-player-small",
                esc_html__("Overflow right bottom, large", 'tana') => "video-player-large",
                esc_html__("Overflow right bottom, medium (default)", 'tana') => "default",
                esc_html__("Overflow right bottom, small", 'tana') => "video-player-small",
                esc_html__("- No player icon", 'tana') => "no-player-icon",
            ),
            "std" => "default",
            "description" => esc_html__("Apply the style If your query has video posts.", 'tana'),
        ),
        array(
            'type' => 'checkbox',
            "param_name" => "playpermalink",
            "heading" => esc_html__("Video Player Icon opens Single post instead of lightbox", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => 'playpermalink' ),
            "std" => ""
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