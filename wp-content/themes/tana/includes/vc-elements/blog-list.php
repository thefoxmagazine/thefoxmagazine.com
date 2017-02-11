<?php

class WPBakeryShortCode_Tt_Blog_List extends WPBakeryShortCode {
    protected function content( $atts, $content = null){

        // Initial argument sets
        extract(shortcode_atts(array(
            'style' => 'Big first and next thumblist',
            'nextonright' => 'default',
            'count' => '4',
            'ratio' => '5x3',
            'post_type' => 'post',
            'categories' => '',
            'title' => '1',
            'excerpt' => '1',
            'meta' => '1',
            'link' => '0',
            'linktext' => esc_attr__('Continue to the category', 'tana'),
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

        // Varialbe declares
        $result = $resultfirstpost = $resultnextposts = '';
        $index = 1;


        // Query posts loop
        $posts_query = new WP_Query($args);
        if($posts_query->have_posts()) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                global $post;

                $the_post = ''; // Temproary resust

                // Unique posts
                $tana_exclude_posts[] = $post->ID;

                // Post thumbnail and markups
                $thumbnail = Tana_Tpl::get_post_image('tana-blog-grid', $ratio, $playbtn.' '.$playpermalink);
                
                $titlemarkup = $title == '1' ? "<h4><a href='".get_permalink()."'>".get_the_title()."</a></h4>" : "";
                $metamarkup = $meta == '1' ? "<div class='meta'><span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span><span class='date'>".get_the_date()."</span></div>" : "";
                $excerptmarkup = $excerpt == '1' ? "<p>".get_the_excerpt()."</p>" : "";

                if( ($index == 1 && $style == 'Big first and next thumblist') || ($index == 1 && $style == 'Big first and next list') ) {
                    $the_post = "<div class='post first hover-dark'>
                                $thumbnail
                                $metamarkup
                                $titlemarkup
                                $excerptmarkup
                            </div>";
                }
                else if( ($style == 'Thumb list') || ($style == 'Big first and next thumblist') || ( $index == 1 && $style == 'Thumb first and next list') ){
                    $thumb = '';
                    if(has_post_thumbnail(get_the_ID())) {
                        $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'tana-thumbnail');
                        $thumb = !empty($thumb_src) ? $thumb_src[0] : '';
                    }
                    $the_post = "<div class='post hover-light clear-left'>
                                    <a href='".get_permalink()."'>
                                        <div class='image image-thumb' data-src='".esc_attr($thumb)."'></div>
                                    </a>
                                    $titlemarkup
                                    $excerptmarkup
                                    $metamarkup
                                </div>";
                }
                else {
                    $the_post = "<div class='post hover-light clear-left'>
                                    $titlemarkup
                                    $excerptmarkup
                                    $metamarkup
                                </div>";
                }

                $resultnextposts .= $the_post;

                // Column split for next posts are on right option
                if( $index == 1 ) {
                    $resultfirstpost = $the_post;
                    $resultnextposts = '';
                }

                $index++;

            } // end while
        } else {
            $description = '';
            if(Tana_Std::get_mod('unique_posts') == '1'){ $description = esc_attr__('Consider to turn off Unique posts option.', 'tana');}
            else { $description =  esc_attr__('Please make sure you are added enough posts into your selected category.', 'tana'); }
            return esc_attr__('No posts found!', 'tana') . ' ' . $description;
        }


        // Column split for next posts are on right option
        if( $nextonright !== 'default' ) {
            $pullright = $nextonright == 'left' ? 'pull-right' : '';
            $result = "<div class='row'>
                    <div class='col-xs-12 col-sm-6 $pullright'>$resultfirstpost</div>
                    <div class='col-xs-12 col-sm-6'>$resultnextposts</div>
                </div><!-- .row -->";
        } else {
            $result = $resultfirstpost.$resultnextposts;
        }

        // Global exclude posts
        $tana_exclude_posts = array_unique($tana_exclude_posts);

        // Reset query
        wp_reset_postdata();

        // Extra class
        $extra_class = esc_attr($extra_class);

        // Read more link
        $morelink = '';
        if( $categories != '' && $link == '1' ) {
            $cats = explode(',', $categories);
            $the_cat = get_category_by_slug($cats[0]);
            if( $the_cat ) {
                $the_link = get_term_link($the_cat);
                $morelink = "<a href='".$the_link."' class='category-more'>$linktext <img src='".get_template_directory_uri()."/images/arrow-right.png' alt='".esc_attr__('Arrow','tana')."'></a>";
            }
        }

        $result = "<div class='blog-list $extra_class'>
            <div class='category-block articles'>
                $result
                $morelink
            </div>
        </div><!-- end .blog-list -->";


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
    "name" => esc_html__('Blog List', 'tana'),
    "description" => esc_html__("List blog posts", 'tana'),
    "base" => 'tt_blog_list',
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
                "Style 4: Thumbnail list" => "Thumb list",
                "Style 5: List" => "List"
            ),
            "std" => esc_html__("Big first and next thumblist", 'tana'),
            "holder" => "div"
        ),
        array(
            'type' => 'dropdown',
            "param_name" => "nextonright",
            "heading" => esc_html__("Next posts position", 'tana'),
            "std" => "default",
            "value" => array(
                esc_html__("Big top and others on bottom (default)", 'tana') => "default",
                esc_html__("Big on left and others on right", 'tana') => "right",
                esc_html__("Big on right and others on left", 'tana') => "left",
            ),
            "description" => esc_html__("Recommended on Style 1 and Style 2 layouts.", 'tana'),
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
            "value" => "4"
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
            "param_name" => "meta",
            "heading" => esc_html__("Meta", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
        ),
        array(
            'type' => 'checkbox',
            "param_name" => "link",
            "heading" => esc_html__("Continue link", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0",
            "description" => esc_html__("Do not forget to specify a category on the above field. If you specified multiple slugs, the anchor will link with first one.", 'tana'),
        ),
        array(
            'type' => 'textfield',
            "param_name" => "linktext",
            "heading" => esc_html__("Link text", 'tana'),
            'value' => esc_html__( 'Continue to the category', 'tana' ),
            "dependency" => Array("element" => "link", "value" => array("1"))
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