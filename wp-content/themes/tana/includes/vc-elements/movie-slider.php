<?php

class WPBakeryShortCode_Tt_Movie_Slider extends WPBakeryShortCode {
    protected function content( $atts, $content = null){

        // Initial argument sets
        extract(shortcode_atts(array(
            'count' => '12',
            'post_type' => 'post',
            'categories' => '',
            'autoplay' => '0',
            'autoplay_seconds' => '5',
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


        // Variable descares
        $posts = '';
        $index = 0;


        // Query posts loop
        $posts_query = new WP_Query($args);
        if($posts_query->have_posts()) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                global $post;

                // Unique posts
                $tana_exclude_posts[] = $post->ID;

                // Reset markups
                $imagemarkup = $catname = '';
                if(has_post_thumbnail(get_the_ID())) {
                    $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
                    $thumb = $thumb_src[0];
                    $imagemarkup = "<img src='".get_template_directory_uri()."/vendors/masterslider/style/blank.gif' data-src='$thumb' alt='".esc_attr__('Image', 'tana')."'/>";
                }
                
                $categories = get_the_category();
                if ( ! empty( $categories ) ) {
                    $catname = $categories[0]->name;
                    $catname = "<span class='author'>$catname</span>";
                }
                $metamarkup = "<div class='meta animate-element' data-anim='fadeInUp'>
                        $catname
                        <span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span>
                        <span class='date'>".get_the_date()."</span>
                    </div>";
                $titlemarkup = "<a href='".get_permalink()."'><h2 class='animate-element' data-anim='fadeInUp'>".get_the_title()." <span class='label'>".TT::getmeta('rating', 'PG-13')."</span></h2></a>";
                $excerptmarkup = "<p class='animate-element' data-anim='fadeInUp'>".get_the_excerpt()."</p>";
                $trailermarkup = '';
                if(TT::getmeta('trailer') != '') {
                    $url = TT::getmeta('trailer');
                    $playerclass = '';

                    if(preg_match('/youtu\.be/i', $url) || preg_match('/youtube\.com\/watch/i', $url)){ $playerclass = ' video-player'; }
                    elseif(preg_match('/vimeo\.com/i', $url)){ $playerclass = ' video-player'; }

                    $trailermarkup = "<a href='".esc_url($url)."' class='button beauty-hover$playerclass'><i class='fa fa-play'></i> ".esc_attr__('Watch Trailer', 'tana')."</a>";
                }
                $linkmarkup = "<a href='".get_permalink()."' class='button beauty-hover'><i class='fa fa-ellipsis-h'></i> ".esc_attr__('Read more', 'tana')."</a>";
                $chartmarkup = '';
                if(TT::getmeta('imdb_rate') !== '0' && TT::getmeta('imdb_rate') !== '') {
                    $number = abs(TT::getmeta('imdb_rate'));
                    $chartmarkup = "<div class='circle-chart' data-circle-width='7' data-percent='".esc_attr($number*10)."' data-text='$number <small>IMDB</small>'></div>";
                }

                $titlethumbmarkup = "<h4>".get_the_title()."</h4>";
                $metathumbmarkup = "<div class='meta'>
                                    <span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span>
                                    <span class='date'>".get_the_date()."</span>
                                </div>";
                $categorythumbmarkup = "<p>".TPL::limit_text(get_the_excerpt(), 3)."</p>";
                
                // Building posts markup
                $posts .= "<div class='ms-slide' data-delay='".abs($autoplay_seconds)."'>
                        <div class='slide-pattern tint'></div>
                        $imagemarkup
                        <div class='ms-thumb post'>
                            <div class='thumb-meta'>
                                $metathumbmarkup
                                $titlethumbmarkup
                                $categorythumbmarkup
                            </div>
                        </div>

                        <div class='ms-layer max-width-780' data-effect='bottom(45)' data-duration='300' data-ease='easeInOut' data-origin='bl'>

                            $metamarkup
                            $titlemarkup
                            $excerptmarkup
                            <div class='animate-element' data-anim='fadeInUp'>
                                $trailermarkup
                                $linkmarkup
                                $chartmarkup
                            </div>

                        </div>

                    </div><!-- .ms-slide -->";

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

        // Extra class
        $extra_class = esc_attr($extra_class);

        // Auto play
        $auto_play_attr = $autoplay == '1' ? ' data-autoplay="true"' : '';

        // Final result
        return "<div class='entertainment-slider news-slider-hover'>

                <!-- masterslider -->
                <div class='master-slider ms-skin-default' id='masterslider".uniqid()."'$auto_play_attr>

                    $posts

                </div>
                <!-- end of masterslider -->

            </div>
            <!-- end of entertainment-slider -->";

    }

}


$post_types = array();
$data_post_types = Tana_Std::get_post_types();
foreach ($data_post_types as $key => $value) {
    $post_types[$value] = $key;
}


// Element options
vc_map( array(
    "name" => esc_html__('Movie Slider', 'tana'),
    "description" => esc_html__("Top slideshow", 'tana'),
    "base" => 'tt_movie_slider',
    "icon" => "tana-vc-icon tana-vc-icon16",
    "content_element" => true,
    "category" => 'Tana',
    "class" => 'tana-vc-element',
    'params' => array(

        array(
            "type" => "textfield",
            "param_name" => "count",
            "heading" => esc_html__("Posts Limit", 'tana'),
            "value" => "5"
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
            "param_name" => "autoplay",
            "heading" => esc_html__("Autoplay next slides", 'tana'),
            'value' => array( esc_html__( 'Yes', 'tana' ) => '1' ),
            "std" => "0"
        ),
        array(
            'type' => 'textfield',
            "param_name" => "autoplay_seconds",
            "heading" => esc_html__("Autoplay seconds", 'tana'),
            'value' => "5",
            "std" => "5",
            'dependency' => array('element' => 'autoplay', 'value' => '1',),
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