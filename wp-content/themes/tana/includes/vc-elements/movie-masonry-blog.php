<?php

class WPBakeryShortCode_Tt_Movie_Masonry extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'count' => '12',
            'post_type' => 'post',
            'categories' => '',
            'carousel' => '0',
            'perslide' => '7',
            'excludes' => '',
            'animated_block' => '0',
            'extra_class' => ''
        ), $atts));

        // Initial query sets
        $args = array(
                    'post_type' => 'post',
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
        $tag_open = false;


        // Query posts loop
        $posts_query = new WP_Query($args);
        if($posts_query->have_posts()) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                global $post;

                // Unique posts
                $tana_exclude_posts[] = $post->ID;

                $postsize = TT::getmeta('post_size');
                $postsize = !empty($postsize) ? $postsize : '1';

                // Image markup
                $ratiomarkup = '';
                $ratio_img = sprintf(
                    '<img src="%1$s/images/8x5.png" alt="%2$s" class="grid-size">',
                    get_template_directory_uri(), esc_attr__('Image', 'tana')
                );
                switch ($postsize) {
                    case '1':
                        $ratiomarkup = str_repeat($ratio_img, 2);
                        break;
                    case '3':
                        $ratiomarkup = str_repeat($ratio_img, 4);
                        break;
                    case '4':
                        $ratiomarkup = str_repeat($ratio_img, 3);
                        break;
                    default:
                        $ratiomarkup = $ratio_img;
                        break;
                }

                $imagemarkup = '';
                if(has_post_thumbnail(get_the_ID())) {
                    $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
                    $thumb = $thumb_src[0];
                    $imagemarkup = sprintf( '<div class="image post-size%s" data-src="%s">%s</div>', esc_attr($postsize), esc_url($thumb), $ratiomarkup );
                }

                // Reset values
                $contentmarkup = $metamarkup = $labelmarkup = $postclass = $titleclass = $contentclass = '';

                if(TT::getmeta('movie_author')) {
                    $metamarkup = "<div class='meta mb0'>
                                        <span class='author'>".esc_html(TT::getmeta('movie_author'))."</span>
                                    </div>";
                }
                if(TT::getmeta('color_light')) {
                    $postclass .= ' text-light';
                }
                if(TT::getmeta('label')) {
                    $postclass .= ' '.TT::getmeta('color');
                    $labelmarkup = "<a href='".get_permalink()."' class='label'>".esc_html(TT::getmeta('label'))."</a>";
                }
                if(TT::getmeta('title_bigger')) {
                    $titleclass = " class='font36'";
                }

                // Post layout variations
                $sizeclass = 'col-xs-12 col-sm-4 col-md-3';

                if( $postsize == '1' ){
                    $sizeclass = 'col-xs-12 col-sm-4 col-md-3';
                    $labelmarkup .= $labelmarkup != '' ? '<br>' : '';
                    $contentmarkup = "$labelmarkup
                                    $metamarkup
                                    <h4$titleclass><a href='".get_permalink()."'>".get_the_title()."</a></h4>";
                }
                else if( $postsize == '3' ){
                    $sizeclass = 'col-xs-12 col-sm-4 col-md-6';
                    $contentmarkup = "$labelmarkup
                                    <h4$titleclass><a href='".get_permalink()."'>".get_the_title()."</a></h4>
                                    $metamarkup";
                }
                else if( $postsize == '4' ){
                    $sizeclass = 'col-xs-12 col-sm-4 col-md-3';
                    $contentmarkup = "$labelmarkup
                                    <h4$titleclass><a href='".get_permalink()."'>".get_the_title()."</a></h4>
                                    $metamarkup";
                }
                else if( $postsize == '2' || $postsize == '5' ){
                    $sizeclass = 'col-xs-12 col-sm-4 col-md-3';
                    $postclass .= ' half-height';
                    $labelmarkup = $labelmarkup != '' ? '<br>' : '';
                    $contentmarkup = "$metamarkup <h4$titleclass><a href='".get_permalink()."'>".get_the_title()."</a></h4>";
                }

                // Text styling
                $textstyle = TT::getmeta('title_style');
                if( $textstyle == '1' ) {
                    $contentclass = '';
                }elseif( $textstyle == '2' ) {
                    $contentclass = ' post-content-bottom';
                }elseif( $textstyle == '3' ) {
                    $contentclass = ' meta-bottom';
                }


                if($carousel == '1' && $index % abs($perslide) == 0) {
                    $posts .= "<div class='swiper-slide'>
                        <div class='masonry-layout row' data-col-width='.col-md-3'>";
                    $tag_open = true;
                }

                // Building posts list
                $posts .= "<div class='$sizeclass masonry-item ab-item'>
                                <div class='post$postclass'>
                                    $imagemarkup
                                    <div class='post-content$contentclass'>

                                        $contentmarkup

                                    </div>
                                </div>
                            </div>
                            <!-- /.col -->";

                if($carousel == '1' && $index % abs($perslide) == abs($perslide)-1) {
                    $posts .= "</div><!-- /.masonry-layout -->
                        </div><!-- /.swiper-slide -->";
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

        
        // Swiper tag closure
        if($carousel == '1' && $tag_open) {
            $posts .= "</div><!-- /.masonry-layout -->
                </div><!-- /.swiper-slide -->";
        }

        $tana_exclude_posts = array_unique($tana_exclude_posts);

        // Reset query
        wp_reset_postdata();

        // Extra class
        $extra_class .= $animated_block=='1' ? ' animated-blocks' : '';
        $extra_class = esc_attr($extra_class);


        if($carousel != '1') {
            $result = "<div class='en-block en-carousel-block'>
                            <div class='masonry-layout row $extra_class' data-col-width='.col-md-3'>  
                                $posts
                            </div>
                        </div>";

        }
        else {
            $result = "<div class='article-carousel en-block en-carousel-block'>
                            <div class='swiper-container carousel-container'>
                                <div class='swiper-wrapper $extra_class'>
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
    "name" => esc_html__('Movie Masonry Blog', 'tana'),
    "description" => esc_html__("Posts in different sizes", 'tana'),
    "base" => 'tt_movie_masonry',
    "icon" => "tana-vc-icon tana-vc-icon09",
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
            "value" => "12"
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
            "param_name" => "perslide",
            "heading" => esc_html__("Posts per slide", 'tana'),
            "value" => "7",
            "description" => esc_html__('Please set enough number on "Posts Limit" field for slides.', 'tana'),
            "dependency" => Array("element" => "carousel", "value" => "1")
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