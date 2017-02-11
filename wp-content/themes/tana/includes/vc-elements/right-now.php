<?php

class WPBakeryShortCode_Tt_Right_Now extends WPBakeryShortCode {
    protected function content( $atts, $content = null){
        
        // Initial argument sets
        extract(shortcode_atts(array(
            'count' => '10',
            'post_type' => 'post',
            'categories' => '',
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

        // Variable declares
        $result = '';
        $index = 1;


        // Query posts loop
        $posts_query = new WP_Query($args);
        if($posts_query->have_posts()) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                global $post;

                // Unique posts
                $tana_exclude_posts[] = $post->ID;

                $class = $thumb = '';
                if(has_post_thumbnail(get_the_ID())) {
                    $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'tana-rightnow');
                    $thumb = $thumb_src[0];
                    $thumb = '<a href="'.get_permalink().'"><div class="image" data-src="'.$thumb.'">
                        <img src="'.get_template_directory_uri().'/images/5x3.png" alt="'.esc_attr__('Proportion', 'tana').'"/>
                    </div></a>';
                } else {
                    $class = ' no-image';
                }

                $result .= '<div class="col-xs-6 col-sm-12">
                    <div class="post hover-light'.$class.'">
                        <span class="fancy-number">'.$index.'</span>
                        '.$thumb.'
                        <h4><a href="'.get_permalink().'">'.get_the_title().'</a></h4>
                    </div>
                </div>';
                $index++;
                    
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

        
        // Final result
        return '<div class="category-block articles trending row '.esc_attr($extra_class).'">
                    '.$result.'
                </div>';

    }

}


$post_types = array();
$data_post_types = Tana_Std::get_post_types();
foreach ($data_post_types as $key => $value) {
    $post_types[$value] = $key;
}


// Element options
vc_map( array(
    "name" => esc_html__('Right now posts', 'tana'),
    "description" => esc_html__("Quick scroll side posts with fancy numbers", 'tana'),
    "base" => 'tt_right_now',
    "icon" => "tana-vc-icon tana-vc-icon11",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(

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
            "description" => esc_html__("Specify category SLUG (multiple slugs wich comman, not name or ID) or leave blank to display items from all categories. Ex: news,image", 'tana'),
            "value" => "",
            "holder" => "div"
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