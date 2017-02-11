<?php

class WPBakeryShortCode_Tana_Carousel_Full extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'post_type' => 'post',
            'categories' => '',
            'count' => '8',
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
        if( !empty($excludes) ){
            $args['post__not_in'] = explode(',', $excludes);
        }

        // Varialbe declares
        $result = '';

        // Query posts loop
        $posts_query = new WP_Query($args);
        while ( $posts_query->have_posts() ) {
            $posts_query->the_post();
            global $post;

            $thumb = '';
            if( has_post_thumbnail() ){
                $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                $thumb = !empty($thumb) ? $thumb[0] : '';
            }

            $video = Tana_Tpl::get_post_video_url();
            $video = empty($video) ? '' : sprintf('<a href="%s" class="fn-play video-player"><i class="fa fa-play"></i></a>', $video);

            // Builing posts markup
            $result .= sprintf( '<div class="swiper-slide">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="fn-item" style="background-image:url(%s);">
                                                <img src="%s/images/16x7.png" class="full-size" alt="'.esc_attr__('Sizer', 'tana').'">
                                                <div class="fn-entry">
                                                    %s<h4>%s</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>',
                                esc_url($thumb), get_template_directory_uri(), $video, get_the_title() );

        }// end while

        // Reset query
        wp_reset_postdata();

        // result
        $result = sprintf('<div class="fn-fullslide">
                                <div class="swiper-container">
                                    <div class="swiper-wrapper">%s</div>
                                </div>
                                <div class="fn-arrows">
                                    <div class="container">
                                        <a href="javascript:;" class="fn-arrow-prev"><i class="fa fa-angle-left"></i></a>
                                        <a href="javascript:;" class="fn-arrow-next"><i class="fa fa-angle-right"></i></a>
                                    </div>
                                </div>
                            </div>', $result );

        // return result
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
    "name" => esc_html__('Full Carousel Blog', 'tana'),
    "description" => esc_html__("Fullwidth in container", 'tana'),
    "base" => 'tana_carousel_full',
    "icon" => "tana-vc-icon tana-vc-icon04",
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
            "type" => 'textfield',
            "param_name" => "categories",
            "heading" => esc_html__("Categories", 'tana'),
            "description" => esc_html__("Specify category SLUG (not name) or leave blank to display items from all categories. Ex: news,image.", 'tana'),
            "value" => '',
            "holder" => 'div'
        ),

        array(
            "type" => "textfield",
            "param_name" => "count",
            "heading" => esc_html__("Posts Limit", 'tana'),
            "value" => "8"
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
            "description" => esc_html__("If you wish text to white. If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'tana'),
        )
    )
));