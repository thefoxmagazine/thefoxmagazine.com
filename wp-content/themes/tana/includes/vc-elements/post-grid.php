<?php

class WPBakeryShortCode_Tana_Post_Grid extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
        	'title' => '',
            'post_type' => 'post',
			'categories' => '',
			'count' => '8',
			'excludes' => '',
			'enable_filter' => '1',
			'col' => '4',
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
        $item_class = 'col-xs-12 col-sm-6 col-md-4 col-lg-3';
        if( $col=='3' ){
        	$item_class = 'col-xs-12 col-sm-6 col-md-4 col-lg-4';
        }

        // Query posts loop
        $posts_query = new WP_Query($args);
        while ( $posts_query->have_posts() ) {
            $posts_query->the_post();
            global $post;

            $thumb = '';
            if( has_post_thumbnail() ){
                $thumb = wp_get_attachment_image(get_post_thumbnail_id(), 'tana-list-masonry-small');
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

            $custom_link = Tana_Std::getmeta('trailer');
            $custom_link = !empty($custom_link) ? esc_url($custom_link) : get_permalink();
            $badge = Tana_Std::getmeta('label');
            $badge = !empty($badge) ? "<span>$badge</span>" : "";

            // Builing posts markup
            $result .= sprintf('<div class="text-center masonry-item %s">
									<div class="wpf-item">
										<a href="%s">%s</a>
										<h2>%s<a href="%s">%s</a></h2>
									</div>
								</div>',
                                $current_item_class, $custom_link, $thumb, $badge, $custom_link, get_the_title() );

        }// end while

        // Reset query
        wp_reset_postdata();

        $filters = '';
        if( $enable_filter=='1' ){
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
                    $filter_html .= sprintf('<a href="javascript:;" data-filter=".ftr-%s">%s</a>', $key, $value['name']);
                }
            }
            $filters = sprintf( '<div class="row">
									<div class="col-sm-12 text-center">
										<h2>%s</h2>
										<div class="wpf-filter">
											<a href="javascript:;" class="active" data-filter="*">%s</a>
											%s
										</div>
									</div>
								</div>',
								$title, esc_html__('All', 'tana'), $filter_html );
        }

        // result
        $result = sprintf('<div class="welcome-folio %s">
								<div class="wpf-panel">
									%s
									<div class="row pv4 pvb0 wpf-viewport">%s</div>
								</div>
							</div>',
                            esc_attr($extra_class), $filters, $result );

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
    "name" => esc_html__('Post Grid', 'tana'),
    "description" => esc_html__("Tana Post Grid", 'tana'),
    "base" => 'tana_post_grid',
    "icon" => "tana-vc-icon tana-vc-icon19",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(

    	array(
            "type" => 'textfield',
            "param_name" => "title",
            "heading" => esc_html__("Title", 'tana'),
            "value" => esc_html__("Title", 'tana'),
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
            'type' => 'checkbox',
            "param_name" => "enable_filter",
            "heading" => esc_html__("Show Title and Filter", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "1"
        ),


        array(
            "type" => "dropdown",
            "param_name" => "col",
            "heading" => esc_html__("Columns", 'tana'),
            "value" => array(
                "3 Columns" => "3",
                "4 Columns" => "4"
            ),
            "std" => "4"
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