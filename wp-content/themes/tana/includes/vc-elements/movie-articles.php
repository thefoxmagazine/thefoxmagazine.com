<?php

class WPBakeryShortCode_Tt_Blog_Movie_Grid extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'columns' => '3 columns',
            'count' => '6',
            'post_type' => 'post',
            'categories' => '',
            'title' => '1',
            'excerpt' => '1',
            'meta' => '1',
            'excludes' => '',
            'animated_block' => '0',
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

        // Column variations
        $column_class = '';
        $column_class = $columns=='1 column' ? "col-xs-12 col-sm-12" : $column_class;
        $column_class = $columns=='2 columns' ? "col-xs-12 col-sm-6" : $column_class;
        $column_class = $columns=='3 columns' ? "col-xs-12 col-sm-6 col-md-4" : $column_class;
        $column_class = $columns=='4 columns' ? "col-xs-12 col-sm-6 col-md-3" : $column_class;
        $column_class = $columns=='6 columns' ? "col-xs-12 col-sm-4 col-md-2" : $column_class;

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

                // Element detail markups
                $titlemarkup = $title == '1' ? "<h4 class='font18'><a href='".get_permalink()."'>".get_the_title()."</a></h4>" : "";
                $metamarkup = $meta == '1' ? "<div class='meta'><span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span><span class='date'>".get_the_date()."</span></div>" : "";
                $excerptmarkup = $excerpt == '1' ? "<p>".get_the_excerpt()."</p>" : "";
                $morelinkmarkup = "<a href='".get_permalink()."' class='category-more'>".esc_html__('Continue to the news', 'tana'). "<img src='".get_template_directory_uri()."/images/arrow-right.png' alt='".esc_attr__('Arrow', 'tana')."'></a>";
                $authormarkup = get_avatar($post->post_author, 54, '', esc_attr__( 'Avatar', 'tana' ), array('class'=>'image-thumb'));
                $postclass = $labelmarkup = '';
                $postclass .= ' '.TT::getmeta('color'); 
                $labelmarkup = "<a href='".get_permalink()."' class='label'>".TT::getmeta('label')."</a>";

                // Post formats
                $mediamarkup = '';
                $format = get_post_format();
                // Image format
                if( $format=='image' ){
                    if(!has_post_thumbnail()){
                        $first_img = '';
                        ob_start();
                        ob_end_clean();
                        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
                        $first_img = $matches[1][0];

                        $mediamarkup ='<div class="image" data-src="'. $first_img .'">
                                <a href="'.get_permalink().'">
                                <img src="'. get_template_directory_uri().'/images/2x1.png" alt="'.get_the_title().'">
                                </a></div>';
                    } else {
                        $mediamarkup = wp_get_attachment_image( get_post_thumbnail_id(), 'medium');
                    }
                }
                // Gallery format
                else if( $format=='gallery' && has_shortcode($post->post_content, 'gallery') ){

                    $galleryObject = get_post_gallery( get_the_ID(), false );
                    $ids = explode(",", isset($galleryObject['ids']) ? $galleryObject['ids'] : "");

                    $gallery_id = uniqid();
                    $gallery = '';
                    $index = 0;
                    if( $ids == "" || count($ids) < 2) {
                        foreach ($galleryObject['src'] as $key => $value) {
                            $gallery .= "<div class='swiper-slide'><img src='$value' alt='".get_the_title()."'/></div>";
                            $gallery .= "<div class='image' data-src='$value'><img src='".get_template_directory_uri()."/images/4x3.png' alt='".get_the_title()."'/></div>";
                            $index++;
                        }
                    } else {
                        foreach ($ids as $gid) {
                            $img = wp_get_attachment_image_src( $gid, 'thumbnail' );
                            $gallery .= "<div class='image' data-src='".$img[0]."'><img src='".get_template_directory_uri()."/images/4x3.png' alt='".get_the_title()."'/></div>";
                            $index++;
                        }
                    }

                    $number = ($index % 3) == 0 ? 3 : (($index % 4) == 0 ? 4 : 2);

                    $mediamarkup = !empty($gallery) ? "<div class='image-grid col-$number'>
                                    $gallery
                                </div>" : $mediamarkup;

                } // end Gallery format markup


                // Builing posts markup
                $result .= "<div class='$column_class ab-item'>

                                <div class='post boxed mb3 $postclass cart-style'>
                                    
                                    <div class='clearfix'>
                                        $authormarkup
                                        $labelmarkup
                                        $metamarkup
                                    </div>
                                    $titlemarkup
                                    $excerptmarkup
                                    $mediamarkup
                                    $morelinkmarkup
                                    
                                </div>
                                <!-- /.post -->

                            </div>
                            <!-- /.col -->";

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
        $extra_class .= $animated_block=='1' ? ' animated-blocks' : '';
        $extra_class = esc_attr($extra_class);


        // Final result
        return "<div class='row none-masonry blog-grid-$columns $extra_class'>$result</div>";

    }

}



$post_types = array();
$data_post_types = Tana_Std::get_post_types();
foreach ($data_post_types as $key => $value) {
    $post_types[$value] = $key;
}


// Element options
vc_map( array(
    "name" => esc_html__('Movie Blog Grid', 'tana'),
    "description" => esc_html__("Posts in columns", 'tana'),
    "base" => 'tt_blog_movie_grid',
    "icon" => "tana-vc-icon tana-vc-icon20",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(
        
        array(
            "type" => "dropdown",
            "param_name" => "columns",
            "heading" => esc_html__("Columns", 'tana'),
            "value" => array(
                "1 Column" => "1 column",
                "2 Columns" => "2 columns",
                "3 Columns" => "3 columns",
                "4 Columns" => "4 columns",
                "6 Columns" => "6 columns"
            ),
            "std" => "3 columns",
            "holder" => "div"
        ),

        array(
            "type" => "textfield",
            "param_name" => "count",
            "heading" => esc_html__("Posts Limit", 'tana'),
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
            "type" => "textfield",
            "param_name" => "excludes",
            "heading" => esc_html__("Exclude posts", 'tana'),
            "value" => "",
            "description" => esc_html__("Please add post IDs separated by comma. Example: 125,1,65.", 'tana'),
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "animated_block",
            "heading" => esc_html__("Visibility with animation", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0"
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