<?php

class WPBakeryShortCode_Tana_Grid_Carousel extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'title' => 'Grid Carousel Title',
            'post_type' => 'post',
            'categories' => '',
            'count' => '8',
            'excludes' => '',
            'layout' => '1',
            'col' => '4',
            'row' => '1',
            'pager' => '1',
            'arrows' => '1',
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

        // Query posts loop
        $posts_query = new WP_Query($args);
        while ( $posts_query->have_posts() ) {
            $posts_query->the_post();
            global $post;

            $thumb = '';
            if( has_post_thumbnail() ){
                $size = $layout=='2' ? 'tana-blog-vertical' : 'tana-blog-square';
                $thumb = wp_get_attachment_image(get_post_thumbnail_id(), $size);
            }

            $label = Tana_Std::getmeta('label');
            $label = !empty($label) ? sprintf('<span class="fs-label">%s</span>', $label) : '';

            if( $layout=='2' ){
                // Builing posts markup
                $result .= sprintf( '<div class="swiper-slide">
                                        <div class="fs-blog-item boxed-title">
                                            <a href="%1$s">%2$s</a>%3$s
                                            <div class="entry-title">
                                                <h4><a href="%1$s">%4$s</a></h4>
                                                <p class="read-more"><a href="%1$s">%5$s</a></p>
                                            </div>
                                        </div>
                                    </div>',
                                    get_permalink(), $thumb, $label, get_the_title(), $read_more );
            }
            else{
                // Builing posts markup
                $result .= sprintf( '<div class="swiper-slide">
                                        <div class="fs-blog-item">
                                            <a href="%1$s">%2$s</a>
                                            <h4><a href="%1$s">%3$s</a></h4>
                                        </div>
                                    </div>',
                                    get_permalink(), $thumb, get_the_title() );
            }

        }// end while

        // Reset query
        wp_reset_postdata();

        // title
        if( !empty($title) ){
            $title = sprintf('<h3 class="fs-title text-center">%s</h3>', $title);
        }

        // pager
        if( $pager=='1' ){
            $title = sprintf( '%1$s<div class="fs-pager">
                                    <span>
                                        <a href="javascript:;" class="fs-arrow-prev swiper-prev"><img src="%2$s/images/arrow-prev.png" alt="'.esc_attr__('preview','tana').'"></a>
                                        <i class="fs-current-index">1</i> of <i class="fs-current-total">1</i>
                                        <a href="javascript:;" class="fs-arrow-next swiper-next"><img src="%2$s/images/arrow-next.png" alt="'.esc_attr__('preview','tana').'"></a>
                                    </span>
                                </div>', $title, get_template_directory_uri() );
        }

        // arrows
        $extras = '';
        if( $arrows=='1' ){
            $extras .= sprintf('<div class="swiper-button-prev swiper-prev"><i class="fa fa-angle-left"></i> <span>%s</span></div>
                                <div class="swiper-button-next swiper-next"><span>%s</span> <i class="fa fa-angle-right"></i></div>',
                                esc_html__('Prev', 'tana'), esc_html__('Next', 'tana') );
        }

        $responsive = array();
        for( $i=abs($col)-1; $i>0; $i-- ){
            $responsive[] = $i;
        }

        // result
        $result = sprintf('<div class="fs-blog-carousel" data-col="%s" data-row="%s" data-responsive="%s">
                                %s
                                <div class="swiper-container">
                                    <div class="swiper-wrapper">%s</div>
                                </div>
                                %s
                            </div>',
                            abs($col), abs($row), implode(',', $responsive), $title, $result, $extras );

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
    "name" => esc_html__('Grid Carousel', 'tana'),
    "description" => esc_html__("Carousel contains posts", 'tana'),
    "base" => 'tana_grid_carousel',
    "icon" => "tana-vc-icon tana-vc-icon21",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(

        array(
            "type" => 'textfield',
            "param_name" => "title",
            "heading" => esc_html__("Title", 'tana'),
            "value" => 'Grid Carousel Title',
            "holder" => 'div'
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
            "type" => "dropdown",
            "param_name" => "layout",
            "heading" => esc_html__("Item Style", 'tana'),
            "value" => array(
                "Square" => "1",
                "Vertical" => "2"
            ),
            "std" => "1"
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
                "5 Columns" => "5",
                "6 Columns" => "6"
            ),
            "std" => "4"
        ),

        array(
            "type" => "dropdown",
            "param_name" => "row",
            "heading" => esc_html__("Rows", 'tana'),
            "value" => array(
                "1 Row" => "1",
                "2 Rows" => "2",
                "3 Rows" => "3",
                "4 Rows" => "4",
                "5 Rows" => "5",
                "6 Rows" => "6"
            ),
            "std" => "1"
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "pager",
            "heading" => esc_html__("Pager", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "arrows",
            "heading" => esc_html__("Next/Prev Arrows", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
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