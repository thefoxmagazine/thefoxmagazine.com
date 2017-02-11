<?php

class WPBakeryShortCode_Tana_Boxed_List extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract( shortcode_atts(array(
            'post_type' => 'post',
            'categories' => '',
            'count' => '4',
            'excludes' => '',
            'enable_heading' => '1',
            'title' => 'Blog',
            'big_title' => 'Big Title',
            'link_text' => esc_html__('Continue to the category', 'tana'),
            'link' => '#',
            'read_more' => esc_html__('read the article', 'tana'),
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
        $post_index = 1;
        // Query posts loop
        $posts_query = new WP_Query($args);
        while ( $posts_query->have_posts() ) {
            $posts_query->the_post();
            global $post;

            $thumb = '';
            if( has_post_thumbnail() ){
                $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'tana-blog-square');
                $thumb = !empty($thumb) ? $thumb[0] : '';
            }

            $video_icon = '';
            $video = Tana_Tpl::get_post_video_url();
            if( !empty($video) ){
                $video_icon = sprintf( '<div class="fs-media-play mv3 mvt0"><a href="%s" class="video-player"><i class="fa fa-play"></i></a></div>', $video );
            }

            $row_top = sprintf('<div class="col-sm-6 fs-table-bg" style="background-image:url(%s);">
                                    <a href="%s">
                                        <img src="%s/images/4x3.png" alt="'.esc_attr__('spacer','tana').'">
                                    </a>
                                </div>', esc_url($thumb), get_permalink(), get_template_directory_uri() );
            $row_bottom = '';
            if( $post_index % 2 == 0 ){
                $row_bottom = $row_top;
                $row_top = '';
            }

            $author = get_the_author_meta('display_name', $post->post_author);
            $author_link = get_author_posts_url($post->post_author);

            // Builing posts markup
            $result .= sprintf( '<div class="fs-table-item %s">
                                    <div class="row">
                                        %s
                                        <div class="col-sm-6">
                                            <div class="fs-table-content">
                                                %s
                                                <h4><a href="%s">%s</a></h4>
                                                <p class="read-more"><a href="%s">%s</a></p>
                                            </div>
                                            <div class="fs-table-meta">
                                                <span class="pull-left"><a href="%s">%s</a></span>
                                                <span class="pull-right"><a href="%s">%s</a></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                        %s
                                    </div>
                                </div>',
                                (($post_index % 2)==0 ? 'fs-media-right' : ''), $row_top, $video_icon, get_permalink(), get_the_title(),
                                get_permalink(), $read_more, $author_link, $author, get_permalink(), get_the_date(), $row_bottom );
            
            $post_index++;

        }// end while

        // Reset query
        wp_reset_postdata();

        $extras = '';
        if( $enable_heading=='1' ){
            $extras = sprintf( '<h2 class="block-title title-block mv5" data-title="%s">%s
                                    <a href="%s" class="category-more text-right">%s <img src="%s/images/arrow-right.png" alt="%s"></a>
                                </h2>
                                <div class="border-line mv0"></div>',
                                esc_attr($big_title), $title, esc_url($link), $link_text, get_template_directory_uri(),esc_attr__('Arrow', 'tana') );
        }

        // result
        $result = sprintf( '%s<div class="fs-post-table">%s</div>', $extras, $result );

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
    "name" => esc_html__('Boxed List', 'tana'),
    "description" => esc_html__("Blog boxed list", 'tana'),
    "base" => 'tana_boxed_list',
    "icon" => "tana-vc-icon tana-vc-icon06",
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
            "value" => ''
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
            'type' => 'checkbox',
            "param_name" => "enable_heading",
            "heading" => esc_html__("Enable Title and Filter", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
        ),

        array(
            "type" => 'textfield',
            "param_name" => "title",
            "heading" => esc_html__("Title", 'tana'),
            "value" => 'Blog',
            "holder" => 'div',
            "dependency" => Array("element" => "enable_heading", "value" => array('1'))
        ),

        array(
            "type" => 'textfield',
            "param_name" => "big_title",
            "heading" => esc_html__("Big Title", 'tana'),
            "value" => 'Big Title',
            "dependency" => Array("element" => "enable_heading", "value" => array('1'))
        ),

        array(
            "type" => 'textfield',
            "param_name" => "link_text",
            "heading" => esc_html__("Link Text", 'tana'),
            "value" => esc_html__('Continue to the category', 'tana'),
            "dependency" => Array("element" => "enable_heading", "value" => array('1'))
        ),

        array(
            "type" => 'textfield',
            "param_name" => "link",
            "heading" => esc_html__("Link", 'tana'),
            "value" => '#',
            "dependency" => Array("element" => "enable_heading", "value" => array('1'))
        ),


        array(
            "type" => 'textfield',
            "param_name" => "read_more",
            "heading" => esc_html__("Read more text", 'tana'),
            "value" => esc_html__("read the article", 'tana'),
            "dependency" => Array("element" => "layout", "value" => array('1'))
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