<?php

class WPBakeryShortCode_Tt_Movie_Carts extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'columns' => '5 columns',
            'count' => '10',
            'post_type' => 'post',
            'categories' => '',
            'carousel' => '0',
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
        
        // Column variations
        $column_class = '';
        $column_class = $columns=='1 column' ? "col-xs-12 col-sm-12 text-bigger" : $column_class;
        $column_class = $columns=='2 columns' ? "col-xs-6 col-sm-6" : $column_class;
        $column_class = $columns=='3 columns' ? "col-xs-6 col-sm-6 col-md-4" : $column_class;
        $column_class = $columns=='4 columns' ? "col-xs-6 col-sm-6 col-md-3" : $column_class;
        $column_class = $columns=='6 columns' ? "col-xs-6 col-sm-4 col-md-2" : $column_class;
        $column_class = $columns=='5 columns' ? "col-xs-6 col-sm-4 col-md-15" : $column_class;

        $row_class = ' row-has-' . str_replace(' ', '-', $columns);

        // Declare variables
        $posts = '';
        $index = 0;
        $tag_open = false;


        // Query posts loop
        $posts_query = new WP_Query($args);
        if($posts_query->have_posts()) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                global $post;

                // Unique posts
                $tana_exclude_posts[] = $post->ID;

                $thumb = '';
                if(has_post_thumbnail(get_the_ID())) {
                    $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'medium');
                    $thumb = $thumb_src[0];
                }

                // Reset detail values
                $titlemarkup = $metamarkup = $labelmarkup = $starsmarkup = $postclass = $titleclass = $contentclass = $chartmarkup = '';
                
                $colorclass = ' '.TT::getmeta('color');

                if(TT::getmeta('label')) {
                    $postclass .= ' '.TT::getmeta('color'); 
                    $labelmarkup = "<a href='".get_permalink()."' class='label'>".esc_html(TT::getmeta('label'))."</a>";
                }
                if(TT::getmeta('imdb_rate') != '' && TT::getmeta('imdb_rate') != 0) {
                    $number = abs(TT::getmeta('imdb_rate'));
                    $chartmarkup = "<div class='circle-chart' data-circle-width='10' data-percent='".(abs($number)*10)."' data-text='$number'></div>";
                }

                if($carousel == '1' && $index % abs($columns) == 0) {
                    $posts .= "<div class='swiper-slide'>";
                    $tag_open = true;
                }


                $value = TT::getmeta('customer_rate');
                if($value != '') {
                    for ($k=0; $k < 5; $k++) {
                        $starsmarkup .= "<i class='fa fa-star";
                        if(abs($value) > $k) { $starsmarkup .= " color-1"; }
                        $starsmarkup .= "'></i>";
                    }
                    $starsmarkup = "<div class='rate'>$starsmarkup</div>";
                }

                $titlemarkup = "<h4><a href='".get_permalink()."'>".esc_html(get_the_title())."</a></h4>";

                // Building posts list
                $posts .= "<div class='$column_class'>
                                <div class='post boxoffice-style$colorclass'>
                                    <div class='image' data-src='$thumb'>
                                        <a href='".get_permalink()."'><img src='".get_template_directory_uri()."/images/2x3.png' alt='".esc_attr__('Image', 'tana')."'/></a>
                                        $labelmarkup
                                        <div class='entry-hover bigger-meta'>
                                            <div class='meta-holder'>
                                                $chartmarkup
                                                <span class='earnings'>".TT::getmeta('total')."</span>
                                                <span class='views'>".TT::getmeta('last_week')."</span>
                                            </div>
                                        </div>
                                    </div>
                                    $starsmarkup
                                    $titlemarkup
                                </div>
                            </div>";

                if($carousel == '1' && $index % abs($columns) == abs($columns)-1) {
                    $posts .= "</div><!-- /.swiper-slide -->";
                    $tag_open = false;
                }

                $index++;               
            } // end while
        } else {
            $description = '';
            if(Tana_Std::get_mod('unique_posts') == '1'){ $description = esc_attr__('Consider to turn off Unique posts option.', 'tana');}
            else { $description =  esc_attr__('Please make sure you are added enough posts into your selected category.', 'tana'); }
            return esc_attr__('No posts found!', 'tana') . ' ' . $description;
        }

        // Swiper closure
        if($carousel == '1' && $tag_open) {
            $posts .= "</div><!-- /.swiper-slide -->";
        }

        $tana_exclude_posts = array_unique($tana_exclude_posts);

        // Reset query
        wp_reset_postdata();

        // Extra class
        $extra_class = esc_attr($extra_class);

        $result = "<div class='row$row_class'>$posts</div>";

        // Wrapping with carousel container
        if($carousel == '1') {
            $result = "<div class='article-carousel'>
                            <div class='swiper-container carousel-container'>
                                <div class='swiper-wrapper row$row_class'>
                                    $posts
                                </div>
                                <div class='pagination-next-prev bordered'>
                                    <a href='#' class='swiper-button-prev arrow-link' title='".esc_attr__('Prev','tana')."'><img src='".get_template_directory_uri()."/images/arrow-left.png' alt='".esc_attr__('Arrow', 'tana')."'></a>
                                    <a href='#' class='swiper-button-next arrow-link' title='".esc_attr__('Next','tana')."'><img src='".get_template_directory_uri()."/images/arrow-right.png' alt='".esc_attr__('Arrow', 'tana')."'></a>
                                </div>
                            </div>
                        </div>";
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
    "name" => esc_html__('Movie Cart Blog', 'tana'),
    "description" => esc_html__("Cart Posts", 'tana'),
    "base" => 'tt_movie_carts',
    "icon" => "tana-vc-icon tana-vc-icon13",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(

        array(
            "type" => "dropdown",
            "param_name" => "columns",
            "heading" => esc_html__("Columns", 'tana'),
            "value" => array(
                "2 Columns" => "2 columns",
                "3 Columns" => "3 columns",
                "4 Columns" => "4 columns",
                "5 Columns" => "5 columns",
                "6 Columns" => "6 columns"
            ),
            "std" => "5 columns",
            "holder" => "div"
        ),

        array(
            "type" => "textfield",
            "param_name" => "count",
            "heading" => esc_html__("Posts Limit", 'tana'),
            "value" => "10"
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
            "description" => esc_html__("Specify category SLUG (not name or ID) or leave blank to display items from all categories. Ex: news,image.", 'tana'),
            "value" => '',
            "holder" => "div"
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "carousel",
            "heading" => esc_html__("Carousel", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0"
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