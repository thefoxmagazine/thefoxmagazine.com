<?php

class WPBakeryShortCode_Tana_Media_List extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'post_type' => 'post',
            'categories' => '',
            'count' => '8',
            'excludes' => '',
            'item_style' => 'play_icon', // link_icon
            'col' => '5',
            'text_light' => '0',
            'show_number' => '1',
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
        $numbering = 0;
        $column_class = '';
        $column_class = $col=='1' ? "col-xs-12 col-sm-12 text-bigger" : $column_class;
        $column_class = $col=='2' ? "col-xs-6 col-sm-6" : $column_class;
        $column_class = $col=='3' ? "col-xs-6 col-sm-6 col-md-4" : $column_class;
        $column_class = $col=='4' ? "col-xs-6 col-sm-6 col-md-3" : $column_class;
        $column_class = $col=='6' ? "col-xs-6 col-sm-4 col-md-2" : $column_class;
        $column_class = $col=='5' ? "col-xs-6 col-sm-4 col-md-15" : $column_class;

        $text_light = $text_light=='1' ? 'text-light' : '';

        // Query posts loop
        $posts_query = new WP_Query($args);
        while ( $posts_query->have_posts() ) {
            $posts_query->the_post();
            global $post;

            $numbering++;

            $thumb = '';
            if( has_post_thumbnail() ){
                $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'tana-blog-square');
                $thumb = !empty($thumb) ? $thumb[0] : '';
            }

            $video = Tana_Tpl::get_post_video_url();
            $video = empty($video) ? get_permalink() : $video;

            $author = Tana_Std::getmeta('movie_author');
            $label = $show_number=='1' ? sprintf('<span class="label">#%s</span>', $numbering) : '';

            if( $item_style=='play_icon' ){
                // Builing posts markup
                $result .= sprintf( '<div class="%s">
                                        <div class="post boxoffice-style ms-style %s">
                                            <div class="image" style="background-image: url(%s);">
                                                <a href="%s"><img src="%s/images/1x1.png" alt="'.esc_attr__('image', 'tana').'"></a>
                                                %s
                                                <a href="%s" class="play-button player-popup size-small">
                                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" xml:space="preserve">
                                                        <g>
                                                            <path d="M16,0C7.2,0,0,7.2,0,16c0,8.8,7.2,16,16,16c8.8,0,16-7.2,16-16C32,7.2,24.8,0,16,0z M16,30.9C7.8,30.9,1.1,24.2,1.1,16C1.1,7.8,7.8,1.1,16,1.1c8.2,0,14.9,6.7,14.9,14.9C30.9,24.2,24.2,30.9,16,30.9z"></path>
                                                            <path d="M22.2,15.9l-8.7-5.9c-0.1-0.1-0.2-0.1-0.3,0c-0.1,0.1-0.2,0.2-0.2,0.3v11.8c0,0.1,0.1,0.2,0.2,0.3c0,0,0.1,0,0.1,0c0.1,0,0.1,0,0.2-0.1l8.7-5.9c0.1-0.1,0.1-0.1,0.1-0.2C22.4,16.1,22.3,16,22.2,15.9z"></path>
                                                        </g>
                                                    </svg>
                                                </a>
                                            </div>
                                            <div class="ms-meta">
                                                <h4><a href="%s">%s</a></h4>
                                                <h5><a href="%s">%s</a></h5>
                                                <a href="javascript:;" class="ms-love">
                                                    <svg viewBox="0 0 11 11">
                                                        <path d="M5.5,10.7L4.7,9.9C1.9,7.3,0,5.6,0,3.4c0-1.7,1.3-3.1,3-3.1c0.9,0,1.9,0.4,2.5,1.2C6.1,0.8,7,0.3,8,0.3c1.7,0,3,1.3,3,3.1c0,2.1-1.9,3.9-4.7,6.4L5.5,10.7z"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>',
                                    $column_class, $text_light, esc_url($thumb), get_permalink(), get_template_directory_uri(), $label, esc_url($video),
                                    get_permalink(), get_the_title(), get_permalink(), $author );
            }
            else{
                // Builing posts markup
                $result .= sprintf( '<div class="%s">
                                        <div class="post boxoffice-style ms-style %s">
                                            <div class="image" style="background-image: url(%s);">
                                                <a href="%s"><img src="%s/images/1x1.png" alt="'.esc_attr__('image', 'tana').'"></a>
                                                %s
                                                <a href="%s" class="icon-more"></a>
                                            </div>
                                            <h4><a href="%s">%s</a></h4>
                                            <h5><a href="%s">%s</a></h5>
                                        </div>
                                    </div>',
                                    $column_class, $text_light, esc_url($thumb), get_permalink(), get_template_directory_uri(), $label, get_permalink(),
                                    get_permalink(), get_the_title(), get_permalink(), $author );
            }

        }// end while

        // Reset query
        wp_reset_postdata();

        // result
        $result = sprintf( '<div class="fn-media-list">
                                <div class="row%s">%s</div>
                            </div>',
                            ($col=='5' ? ' row-has-5-columns' : ''), $result );

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
    "name" => esc_html__('Media List', 'tana'),
    "description" => esc_html__("Media List Numbering", 'tana'),
    "base" => 'tana_media_list',
    "icon" => "tana-vc-icon tana-vc-icon17",
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
            "type" => "dropdown",
            "param_name" => "item_style",
            "heading" => esc_html__("Item Style", 'tana'),
            "value" => array(
                "With Play Icon" => "play_icon",
                "With Link Icon" => "link_icon"
            ),
            "std" => "play_icon"
        ),

        array(
            "type" => "dropdown",
            "param_name" => "col",
            "heading" => esc_html__("Columns", 'tana'),
            "value" => array(
                "1 Column" => "1",
                "2 Columns" => "2",
                "3 Columns" => "3",
                "4 Columns" => "4",
                "5 Columns" => "5"
            ),
            "std" => "5"
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "text_light",
            "heading" => esc_html__("Text Light Color", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0"
        ),


        array(
            'type' => 'checkbox',
            "param_name" => "show_number",
            "heading" => esc_html__("Show Numbering", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
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