<?php

class WPBakeryShortCode_Tt_Movie_Masonry_Alt extends WPBakeryShortCode {
    protected function content( $atts, $content = null){

        // Initial argument sets
        extract(shortcode_atts(array(
            'count' => '12',
            'post_type' => 'post',
            'categories' => '',
            'excludes' => '',
            'animated_block' => '0',
            'img_position' => 'top',
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

        // Varible declares
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

                // Reset values
                $contentmarkup = $titlemarkup = $metamarkup = $labelmarkup = $postclass = $titleclass = $contentclass = '';

                $postsize = TT::getmeta('post_size');
                $postsize = !empty($postsize) ? $postsize : '1';
                $titlemarkup = "<h4><a href='".get_permalink()."'>".esc_html(get_the_title())."</a></h4>";

                if(TT::getmeta('label')) {
                    $postclass .= ' '.TT::getmeta('color');
                    $labelmarkup = "<a href='".get_permalink()."' class='label'>".esc_html(TT::getmeta('label'))."</a>";
                }

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

                // Image thumbnail
                $imagemarkup = sprintf( '<div class="image post-size%s">%s</div>', $postsize, $ratiomarkup );
                if( has_post_thumbnail(get_the_ID()) ){
                    $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
                    $thumb = $thumb_src[0];

                    $imagemarkup = sprintf( '<div class="image post-size%s" data-src="%s">%s</div>', $postsize, esc_url($thumb), $ratiomarkup );
                    if( $postsize == '3' || $postsize == '4' ) {
                        $imagemarkup = sprintf( '<div class="image post-size%s" data-src="%s">%s%s</div>', $postsize, esc_url($thumb), $labelmarkup, $ratiomarkup );
                    }

                } else {
                    $postclass .= ' no-image';
                }

                $metamarkup = sprintf(
                    '<div class="meta">
                        <span class="author">%s</span>
                        <span class="date">%s</span>
                    </div>',
                    esc_html(TT::getmeta('movie_author')), get_the_date()
                );

                if( $post_type=='product' && class_exists('WooCommerce') ){
                    $price = '';
                    $post_cats = wp_get_post_terms( get_the_ID(), $tax_name );
                    $product_cat = '';
                    if( !empty($post_cats) ){
                        foreach( $post_cats as $category ){
                            $product_cat = $category->name;
                        }
                    }

                    if( in_array($postsize, array('1','3','4')) ){
                        ob_start();
                        woocommerce_template_loop_price();
                        $price = ob_get_contents();
                        ob_end_clean();
                        $price = sprintf('<div class="product-price">%s</div>', $price);
                    }

                    $metamarkup = sprintf(
                        '<div class="meta">
                            <span class="product-cat">%s</span>%s
                        </div>',
                        $product_cat, $price
                    );
                }

                $authormarkup = "<a href='".get_permalink()."' class='author-image'>
                                    ".get_avatar($post->post_author, 80, '', esc_attr__( 'Avatar', 'tana' ), array('class'=>'image image-thumb border-radius'))."
                                </a>";

                if(TT::getmeta('color_light')) {
                    $postclass .= ' text-light';
                }
                if(TT::getmeta('title_bigger')) {
                    $titleclass = " class='font36'";
                }

                $sizeclass = 'col-xs-12 col-sm-4 col-md-3';

                if( $postsize == '1' ){
                    $sizeclass = 'col-xs-12 col-sm-4 col-md-3';
                    $contentmarkup = "$imagemarkup
                                    <div class='entry-hover'>
                                        $labelmarkup
                                        $titlemarkup
                                        $metamarkup
                                    </div>";
                }
                else if( $postsize == '3' ){
                    $sizeclass = 'col-xs-12 col-sm-6 col-md-6';
                    $contentmarkup = "$imagemarkup
                                    <div class='entry-hover bigger-meta'>
                                        <div class='meta-holder'>
                                            $authormarkup
                                            $metamarkup
                                            $titlemarkup
                                        </div>
                                    </div>";
                }
                else if( $postsize == '4' ){
                    $sizeclass = 'col-xs-12 col-sm-4 col-md-3';
                    $chartmarkup = '';
                    if(TT::getmeta('imdb_rate') != 0 && TT::getmeta('imdb_rate') != '') {
                        $number = abs(TT::getmeta('imdb_rate'));
                        $chartmarkup = "<div class='circle-chart' data-circle-width='10' data-percent='".($number*10)."' data-text='$number'></div>";
                    }

                    $contentmarkup = "$imagemarkup
                                    <div class='entry-hover bigger-meta'>
                                        <div class='meta-holder'>
                                            $authormarkup
                                            $metamarkup
                                            $titlemarkup
                                            $chartmarkup
                                        </div>
                                    </div>";

                }
                else{
                    $sizeclass = 'col-xs-12 col-sm-4 col-md-3';
                    $contentmarkup = sprintf(
                        '%s<div class="entry-hover">
                                %s %s %s
                            </div>',
                        $imagemarkup, $labelmarkup, $titlemarkup, $metamarkup
                    );
                }

                if( in_array($postsize, array('3','4')) ){
                    if( $post_type=='product' && class_exists('WooCommerce') ){
                        $imagemarkup = str_replace("class='label'", "class='label hidden'", $imagemarkup);
                        $contentmarkup = "$imagemarkup
                                          <div class='entry-hover'>
                                            $labelmarkup
                                            $titlemarkup
                                            $metamarkup
                                          </div>";
                    }
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


                $posts .= "<div class='$sizeclass masonry-item ab-item'>
                                <div class='post$postclass'>
                                    $contentmarkup
                                </div>
                            </div>";

            } // end while
        } else {
            $description = '';
            if(Tana_Std::get_mod('unique_posts') == '1'){ $description = esc_attr__('Consider to turn off Unique posts option.', 'tana');}
            else { $description =  esc_attr__('Please make sure you are added enough posts into your selected category.', 'tana'); }
            return esc_attr__('No posts found!', 'tana') . ' ' . $description;
        }

        $tana_exclude_posts = array_unique($tana_exclude_posts);

        // reset query
        wp_reset_postdata();

        $extra_class .= $animated_block=='1' ? ' animated-blocks' : '';
        $extra_class .= sprintf(' item-bg-pos-%s', $img_position);
        $extra_class = esc_attr($extra_class);


        // Final result
        return "<div class='masonry-layout-alternate little-space row $extra_class'>
                    $posts
                </div>";


    }

}



$post_types = array();
$data_post_types = Tana_Std::get_post_types();
foreach ($data_post_types as $key => $value) {
    $post_types[$value] = $key;
}



// Element options
vc_map( array(
    "name" => esc_html__('Movie Masonry Alternate', 'tana'),
    "description" => esc_html__("Posts in different sizes", 'tana'),
    "base" => 'tt_movie_masonry_alt',
    "icon" => "tana-vc-icon tana-vc-icon22",
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
            "type" => "dropdown",
            "param_name" => "img_position",
            "heading" => esc_html__("Image Vertical Position", 'tana'),
            "value" => array(
                esc_html__('Top', 'tana') => "top",
                esc_html__('Middle', 'tana') => "middle",
                esc_html__('Bottom', 'tana') => "bottom"
            ),
            "std" => "top",
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
