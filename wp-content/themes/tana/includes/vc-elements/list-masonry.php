<?php

class WPBakeryShortCode_Tana_List_Masonry extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'post_type' => 'post',
            'categories' => '',
            'count' => '8',
            'excludes' => '',
            'enable_heading' => '1',
            'filter_style' => 'normal', // normal | bordered | big-title
            'title' => 'Blog',
            'big_text' => 'Archive',
            'size_large' => '1',
            'hide_meta' => '0',
            'col' => '1',
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
        $post_categories = array();
        $item_class = 'col-sm-12';
        if( $col=='4' ){
            $item_class = 'col-sm-6 col-md-4 col-lg-3';
        }
        else if( $col=='3' ){
            $item_class = 'col-sm-6 col-md-4';
        }
        else if( $col=='2' ){
            $item_class = 'col-sm-6';
        }

        // Query posts loop
        $posts_query = new WP_Query($args);
        while ( $posts_query->have_posts() ) {
            $posts_query->the_post();
            global $post;

            $thumb = '';
            if( has_post_thumbnail() ){
                $size = $col=='1' ? 'tana-list-masonry-big' : 'tana-list-masonry-small';
                $thumb = wp_get_attachment_image(get_post_thumbnail_id(), $size);
            }

            $cats = array('name'=>'', 'link'=>'');
            $current_item_class = $item_class;
            $post_cats = wp_get_post_terms( get_the_ID(), $tax_name );
            if( !empty($post_cats) ){
                foreach( $post_cats as $category ){
                    $current_item_class .= " ftr-$category->slug";
                    $cats = array(
                            'name' => $category->name,
                            'link' => get_term_link($category)
                        );

                    $post_categories[$category->slug] = $cats;
                }
            }

            $author = get_the_author_meta('display_name', $post->post_author);
            $author_link = get_author_posts_url($post->post_author);

            if( $hide_meta == '0') {
                // Builing posts markup
                $result .= sprintf( '<div class="masonry-item %1$s">
                                        <div class="fs-grid-item %2$s">
                                            <a href="%3$s" class="fs-entry-image">%4$s</a>
                                            <div class="fs-entry-meta">
                                                <span><a href="%5$s">%6$s</a></span>
                                                <span><a href="%3$s">%7$s</a></span>
                                                <span><a href="%8$s">%9$s</a></span>
                                            </div>
                                            <h3><a href="%3$s">%10$s</a></h3>
                                            <p class="read-more"><a href="%3$s">%11$s</a></p>
                                        </div>
                                    </div>',
                                    $current_item_class, ($size_large=='1' ? 'fs-large' : ''), get_permalink(), $thumb, $cats['link'], $cats['name'],
                                    get_the_date(), $author_link, $author, get_the_title(), $read_more );
            } else {
                // Builing posts markup
                $result .= sprintf( '<div class="masonry-item %1$s">
                                        <div class="fs-grid-item %2$s">
                                            <a href="%3$s" class="fs-entry-image">%4$s</a>
                                            <h3><a href="%3$s">%10$s</a></h3>
                                            <p class="read-more"><a href="%3$s">%11$s</a></p>
                                        </div>
                                    </div>',
                                    $current_item_class, ($size_large=='1' ? 'fs-large' : ''), get_permalink(), $thumb, $cats['link'], $cats['name'],
                                    get_the_date(), $author_link, $author, get_the_title(), $read_more );
            }


        }// end while

        // Reset query
        wp_reset_postdata();

        $filters = '';
        if( $enable_heading=='1' ){
            $filter_html = '';

            if( !empty($categories) ){
                $categories = str_replace(' ', '', $categories);
                $exp_cats = explode(',', $categories);
                if( !empty($exp_cats) && !empty($post_categories) ){
                    foreach ($post_categories as $key => $value) {
                        if( !in_array($key, $exp_cats) ){
                            unset($post_categories[$key]);
                        }
                    }
                }
            }

            if( !empty($post_categories) ){
                foreach ($post_categories as $key => $value) {
                    $filter_html .= sprintf('<li><a href="javascript:;" data-filter=".ftr-%s">%s</a></li>', $key, $value['name']);
                }
            }
            $filters = sprintf( '<div class="fs-post-filter %s">
                                    <div class="col-sm-12">
                                        <h4 data-title="%s">%s</h4>
                                        <ul>
                                            <li class="active"><a href="javascript:;" data-filter="*">%s</a></li>
                                            %s
                                        </ul>
                                    </div>
                                </div>',
                                ($filter_style!='normal' ? esc_attr($filter_style) : ''), esc_attr($big_text), $title, esc_html__('All', 'tana'), $filter_html );
        }

        $col_class = '.col-sm-12';
        $col_class = $col=='4' ? '.col-lg-3' : $col_class;
        $col_class = $col=='3' ? '.col-md-4' : $col_class;
        $col_class = $col=='2' ? '.col-sm-6' : $col_class;
        // result
        $result = sprintf('<div class="masonry-layout row" data-col-width="%s">
                                <div class="fs-grid-posts">
                                    %s
                                    <div class="fs-grid-viewport">
                                        <div class="row">%s</div>
                                    </div>
                                </div>
                            </div>',
                            $col_class, $filters, $result );

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
    "name" => esc_html__('List Masonry', 'tana'),
    "description" => esc_html__("Blog list masonry", 'tana'),
    "base" => 'tana_list_masonry',
    "icon" => "tana-vc-icon tana-vc-icon19",
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
            "type" => "dropdown",
            "param_name" => "filter_style",
            "heading" => esc_html__("Filter Style", 'tana'),
            "value" => array(
                "Normal" => "normal",
                "With border" => "bordered",
                "With big Text" => "big-title"
            ),
            "std" => "normal",
            "dependency" => Array("element" => "enable_heading", "value" => array('1'))
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
            "param_name" => "big_text",
            "heading" => esc_html__("Big Text", 'tana'),
            "value" => 'Archive',
            "dependency" => Array("element" => "filter_style", "value" => array('big-title'))
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "size_large",
            "heading" => esc_html__("Item Larse Style", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
        ),
        array(
            'type' => 'checkbox',
            "param_name" => "hide_meta",
            "heading" => esc_html__("Hide meta list", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0",
            "desc" => esc_html__("To remove post category, author and date", 'tana'),
        ),

        array(
            "type" => "dropdown",
            "param_name" => "col",
            "heading" => esc_html__("Columns", 'tana'),
            "value" => array(
                "1 Column" => "1",
                "2 Columns" => "2",
                "3 Columns" => "3",
                "4 Columns" => "4"
            ),
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