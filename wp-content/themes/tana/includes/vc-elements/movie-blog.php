<?php

class WPBakeryShortCode_Tt_Movie_Post_List extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'style' => 'Big first and next thumblist',
            'count' => '4',
            'ratio' => '5x3',
            'post_type' => 'post',
            'categories' => '',
            'title' => '1',
            'excerpt' => '1',
            'author' => '1',
            'meta' => '1',
            'playbtn' => '',
            'playpermalink' => '',
            'link' => '1',
            'moretext' => 'Continue to the news',
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

        // Result collector variable
        $result = '';

        // Query posts loop
        $posts_query = new WP_Query($args);
        if($posts_query->have_posts()) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                global $post;

                // Unique posts
                $tana_exclude_posts[] = $post->ID;

                // Variables reset
                $metaclass = $linkmarkup = '';

                // Element detail markups
                $label = TT::getmeta('label');
                
                $thumbnail = Tana_Tpl::get_post_image('tana-blog-grid', $ratio, $playbtn.' '.$playpermalink, true, $label);
                if( $author == '1' ) {
                    $authormarkup = get_avatar($post->post_author, 54, '', esc_attr__( 'Avatar', 'tana' ), array('class'=>'image-small'));
                } else {
                    $authormarkup = $label != '' ? "<a href='".get_permalink()."' class='label'>$label</a>" : "";
                    $metaclass = ' small-meta';
                }
                $titlesize = TT::getmeta('title_bigger') == '1' ? " class='font28'" : " class='font18'";
                $titlemarkup = $title == '1' ? "<h4$titlesize><a href='".get_permalink()."'>".get_the_title()."</a></h4>" : "";
                $metamarkup = $meta == '1' ? "<div class='meta inline-meta$metaclass'>$authormarkup<span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span><span class='date'>".get_the_date()."</span></div>" : "";
                if(!empty($thumbnail)){
                    $metamarkup = $meta == '1' ? "<div class='meta inline-meta mb0'><span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span><span class='date'>".get_the_date()."</span></div>" : "";
                }
                $excerptmarkup = $excerpt == '1' ? "<p>".get_the_excerpt()."</p>" : "";
                $color = TT::getmeta('color');
                if( $link == '1' ) {
                    $linkmarkup = "<a href='".get_permalink()."' class='category-more'>".esc_html($moretext)." <img src='".get_template_directory_uri()."/images/arrow-right-red.png' alt='".esc_attr__('Arrow', 'tana')."'></a>";
                }

                if( empty($thumbnail) && $author == '1' ) {
                    $metamarkup .= "<div class='border-bottom2 mb2 $color'></div>";
                }

                // Builing posts markup
                if(empty($thumbnail)){
                    $result .= "<div class='post $color'>

                                $metamarkup
                                $titlemarkup
                                $excerptmarkup
                                $linkmarkup
                                
                            </div>
                            <!-- end .post -->";
                } else {

                    $result .= "<div class='post hover-dark $color'>

                                $thumbnail
                                $metamarkup
                                $titlemarkup
                                $linkmarkup

                            </div>
                            <!-- end .post -->";
                }

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


        // Final result
        return "<div class='category-block en-block'>

                    $result

                </div>
                <!-- .category-block -->";

    }

}


$post_types = array();
$data_post_types = Tana_Std::get_post_types();
foreach ($data_post_types as $key => $value) {
    $post_types[$value] = $key;
}


// Element options
vc_map( array(
    "name" => esc_html__('Movie Blog List', 'tana'),
    "description" => esc_html__("List blog posts", 'tana'),
    "base" => 'tt_movie_post_list',
    "icon" => "tana-vc-icon tana-vc-icon03",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(
        
        array(
            "type" => "dropdown",
            "param_name" => "style",
            "heading" => esc_html__("Layout options", 'tana'),
            "value" => array(
                "Style 1: Big first and thumbnail list (default)" => "Big first and next thumblist",
                "Style 2: Big first and title list" => "Big first and next list",
                "Style 3: Thumbnail first and list" => "Thumb first and next list",
                "Style 4: Thumbnail first" => "Thumb list",
                "Style 5: List" => "List"
            ),
            "std" => esc_html__("Big first and next thumblist", 'tana'),
            "holder" => "div"
        ),

        array(
            "type" => "textfield",
            "param_name" => "count",
            "heading" => esc_html__("Posts Limit", 'tana'),
            "value" => "4"
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
            "heading" => esc_html__("Category slug/slugs", 'tana'),
            "description" => esc_html__("Specify category SLUG (not name) or leave blank to display items from all categories. Ex: news,image.", 'tana'),
            "value" => '',
            "holder" => "div"
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
            "param_name" => "author",
            "heading" => esc_html__("Author image", 'tana'),
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
            'type' => 'checkbox',
            "param_name" => "link",
            "heading" => esc_html__("Read more link", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
        ),

        array(
            "type" => 'textfield',
            "param_name" => "moretext",
            "heading" => esc_html__("Read more text", 'tana'),
            "value" => 'Continue to the news',
            "dependency" => Array("element" => "link", "value" => array("1"))
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