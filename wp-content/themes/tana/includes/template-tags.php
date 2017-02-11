<?php

// wp_oembedd media filter
global $wp_embed;
add_filter( 'themeton_media_filter', array( $wp_embed, 'autoembed' ), 8 );


class TPL{

    // Print Sites Logo
    public static function get_logo(){
        $custom_logo = '';
        if( function_exists('get_custom_logo') ){
            $custom_logo = get_custom_logo();
        }
        else{
            $logo = TT::get_mod('logo');
            if( !empty($logo) ){
                $custom_logo = sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url"><img src="%2$s" alt="'.get_bloginfo('name').'" class="custom-logo"></a>',
                    esc_url(home_url('/')),
                    esc_url($logo)
                );
            }
        }

        if( !empty($custom_logo) && strpos($custom_logo, " src=") ){
            print($custom_logo);
        }
        else{
            printf('<a href="%s" rel="home" class="logo-text-link">%s</a>', esc_url(home_url('/')), get_bloginfo('name') );
            $description = get_bloginfo('description', 'display');
            if ( !empty($description) ){
                printf('<p class="site-description">%s</p>', $description);
            }
        }
    }


    public static function build_theme_image_support(){
        add_theme_support('custom-header');
        add_theme_support('custom-background');
        add_editor_style( array('css/editor-style.css') );
    }

    public static function print_post_thumbnail(){
        the_post_thumbnail();
    }


    public static function the_date_info(){
        ?>
        <h4><?php echo date('d'); ?></h4>
        <p><?php echo date_i18n( 'M, l',  strtotime(date( "Y-m-d" )) ); ?></p>
        <?php
    }

    public static function limit_text($text, $limit) {
      if (str_word_count($text, 0) > $limit) {
          $words = str_word_count($text, 2);
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limit]) . '...';
      }
      return $text;
    }

    public static function the_weather_info(){
        $weather_temp = Tana_Std::get_mod('weather_temp');
        $weather_temp = !empty($weather_temp) ? $weather_temp : '0';
        $weather_title = Tana_Std::get_mod('weather_title');
        $weather_title = !empty($weather_title) ? $weather_title : esc_html__('Current location', 'tana');

        $is_update = false;
        $city = esc_attr(Tana_Std::get_mod('weather_city'));
        $city = !empty($city) ? $city : 'ulaanbaatar';
        $last_city = esc_attr(Tana_Std::get_mod('weather_last_city', ''));
        $weather_date = Tana_Std::get_mod('weather_date');
        $today = date('Y-m-d');

        if( $city!=$last_city || $weather_date!=$today || empty($weather_temp) ){
            $is_update = true;
        }
        ?>
        <h4 class="<?php echo esc_attr($is_update ? 'weather-update-required' : ''); ?>"><span><?php echo esc_attr($weather_temp); ?></span>&deg;C</h4>
        <p><?php printf('%s', $weather_title); ?></p>
        <?php
    }

    public static function the_close_icon(){
        ?>
        <svg class="icon-close" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 16 16" enable-background="new 0 0 16 16" xml:space="preserve">
            <polygon points="16,1.6 14.4,0 8,6.4 1.6,0 0,1.6 6.4,8 0,14.4 1.6,16 8,9.6 14.4,16 16,14.4 9.6,8"/>
        </svg>
        <?php
    }


    public static function the_search_icon(){
        ?>
        <svg class="icon-search" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 14 14" xml:space="preserve">
            <path d="M5.4,0C2.4,0,0,2.4,0,5.4s2.4,5.4,5.4,5.4c1.2,0,2.2-0.4,3.1-1l0,0l4,4c0.1,0.1,0.2,0.1,0.3,0l1.1-1.1c0.1-0.1,0.1-0.2,0-0.3l-4-4c0.6-0.9,1-2,1-3.1C10.9,2.4,8.4,0,5.4,0z M5.4,9.6c-2.3,0-4.2-1.9-4.2-4.2s1.9-4.2,4.2-4.2s4.2,1.9,4.2,4.2S7.7,9.6,5.4,9.6z"/>
        </svg>
        <?php
    }

    public static function the_burger_icon(){
        ?>
        <svg class="icon-burger" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 14 12" xml:space="preserve">
            <path d="M1.1,0.1h11.7c0.6,0,1.1,0.5,1.1,1.1s-0.5,1.1-1.1,1.1H1.1C0.5,2.3,0,1.8,0,1.2S0.5,0.1,1.1,0.1z"/>
            <path d="M1.1,4.9h11.7C13.5,4.9,14,5.4,14,6s-0.5,1.1-1.1,1.1H1.1C0.5,7.1,0,6.6,0,6S0.5,4.9,1.1,4.9z"/>
            <path d="M1.1,9.7h11.7c0.6,0,1.1,0.5,1.1,1.1c0,0.6-0.5,1.1-1.1,1.1H1.1c-0.6,0-1.1-0.5-1.1-1.1C0,10.2,0.5,9.7,1.1,9.7z"/>
        </svg>
        <?php
    }
    
    public static function get_post_media(){
        global $post;
        $media = '';
        if( has_post_thumbnail() ){
            $thumb_img = wp_get_attachment_image( get_post_thumbnail_id(), 'large' );
            $media = $thumb_img;
        }

        $format = get_post_format();

        if( current_theme_supports('post-formats', $format) ){

            // Image
            if( $format=='image' ){
                if(!has_post_thumbnail()){
            
                    $first_img = '';
                    ob_start();
                    ob_end_clean();
                    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
                    $first_img = $matches[1][0];

                    $media ='<div class="image" data-src="'. $first_img .'">
                            <a href="'.get_permalink().'">
                            <img src="'. get_template_directory_uri().'/images/5x3.png" alt="'.get_the_title().'">
                            </a></div>';
                } else {
                    $media = wp_get_attachment_image( get_post_thumbnail_id(), 'large');
                }
            }


            // blockquote
            else if( $format=='quote' ){
                preg_match("/<blockquote>(.*?)<\/blockquote>/msi", get_the_content(), $matches);
                if( isset($matches[0]) && !empty($matches[0]) ){
                    $media = $matches[0];
                    $media = str_replace("<blockquote", "<blockquote class='quote-element'", $media);
                }
            }


            // link
            else if( $format=='link' ){
                preg_match('/<a\s[^>]*href=\"([^\"]*)\"[^>]*>(.*)<\/a>/siU', get_the_content(), $matches);
                if( isset($matches[1],$matches[2]) && !empty($matches[2]) ){
                    $media = "<blockquote class='link-element'>
                                $matches[2]
                                <cite><a href='$matches[1]'>$matches[1]</a></cite>
                              </blockquote>";
                }
            }


            // gallery
            else if( $format=='gallery' && has_shortcode($post->post_content, 'gallery') ){
                $galleryObject = get_post_gallery( get_the_ID(), false );
                $ids = explode(",", isset($galleryObject['ids']) ? $galleryObject['ids'] : "");

                $gallery = '';
                if( $ids == "" || count($ids) < 2) {
                    foreach ($galleryObject['src'] as $key => $value) {
                        $gallery .= "<div class='swiper-slide'><img src='$value' alt='".get_the_title()."'/></div>";
                    }
                } else {
                    foreach ($ids as $gid) {
                        $img = wp_get_attachment_image( $gid, 'thumbnail' );
                        $gallery .= "<div class='swiper-slide'>$img</div>";
                    }
                }


                $media = !empty($gallery) ? "<div class='gallery-slideshow'>
                                                <div class='swiper-container gallery-container'>
                                                    <div class='swiper-wrapper'>$gallery</div>
                                                </div>
                                                <div class='swiper-button-prev'></div>
                                                <div class='swiper-button-next'></div>
                                            </div>" : $media;

                $media = $media;
            }


            // audio
            else if( $format=='audio' ){
                $pattern = get_shortcode_regex();
                preg_match('/'.$pattern.'/s', $post->post_content, $matches);
                if (is_array($matches) && isset($matches[2]) && $matches[2] == 'audio') {
                    $shortcode = $matches[0];
                    $media = '<div class="mejs-wrapper audio">'. do_shortcode($shortcode) . '</div>';
                }
                else{
                    $frame = "frame";
                    $regx = "/<i$frame(.)*<\/i$frame>/msi";
                    preg_match($regx, get_the_content(), $matches);
                    if( isset($matches[0]) && !empty($matches[0]) ){
                        $media = $matches[0];
                    }
                    else{
                        if ( preg_match( '|^\s*(https?://[^\s"]+)\s*$|im', $post->post_content, $matches ) ) {
                            if(isset($matches[1])) {
                                $media = "<div class='audio-post'>".apply_filters( "themeton_media_filter", $matches[1] )."</div>";
                            }
                        }
                    }
                }
                $media = $media;
            }



            // video
            else if( $format=='video' ){
                if ( preg_match( '|^\s*(https?://[^\s"]+)\s*$|im', $post->post_content, $matches ) ) {
                    if(isset($matches[1])) {
                        $media = "<div class='video-post'>".apply_filters( "themeton_media_filter", $matches[1] )."</div>";
                    }
                }
            }
            
        }

        return !empty($media) ? "<div class='entry-media'>$media</div>" : "";
    }

    public static function gallery_slideshow( $galleryObject = array() ) {

        $ids = explode(",", isset($galleryObject['ids']) ? $galleryObject['ids'] : "");

        $gallery = '';
        if( $ids == "" || count($ids) < 2) {
            if( !empty($galleryObject) && array_key_exists('src', $galleryObject) ){
                foreach ($galleryObject['src'] as $key => $value) {
                    $gallery .= "<div class='ms-slide'>
                                    <div class='slide-pattern tint'></div>
                                    <img src='".get_template_directory_uri()."/vendors/masterslider/style/blank.gif' data-src='$value' alt='".esc_attr__('Gallery Image', 'tana')."'/>
                                    <div class='ms-thumb'>
                                        <div class='image' data-src='".esc_attr($value)."'></div>
                                    </div>
                                </div>";
                }
            }
        }
        else {
            foreach ($ids as $gid) {
                $value = wp_get_attachment_image_src( $gid, 'large' );
                $value = $value[0];
                $img = get_post( $gid );
                $title = isset($img->post_title) ? $img->post_title : '';
                $caption = isset($img->post_excerpt) && $img->post_excerpt != '' ? ' <br><span>('.$img->post_excerpt.')</span>': '';
                $content = isset($img->post_content) && $img->post_content != '' ? ' <br><span>'.$img->post_content.'</span>': '';
                $alt = get_post_meta( $gid, '_wp_attachment_image_alt', true );

                $gallery .= "<div class='ms-slide'>
                                <div class='slide-pattern tint'></div>
                                <img src='".get_template_directory_uri()."/vendors/masterslider/style/blank.gif' data-src='$value' alt='$alt'/>
                                <div class='ms-thumb'>
                                    <div class='image' data-src='$value'></div>
                                </div>
                                <div class='ms-layer' data-effect='fade' data-duration='300' data-ease='easeInOut'>
                                    $title$caption$content
                                </div>
                            </div>";
            }
        }

        $gallery_id = uniqid();

        $media = !empty($gallery) ? "<div class='gallery-slider mv2' data-speed='100'>
                                        <div class='master-slider gallery-style ms-skin-default' id='$gallery_id' data-autoplay='true'>
                                            $gallery
                                        </div>
                                    </div>" : '';

        return $media;
    }

    public static function get_post_video_url(){
        global $post;

        if( Tana_Std::get_mod('video_lightbox_disable') == '1' ) {
            return get_permalink();
        }

        $format = get_post_format();
        if( $format=='video' ){
            if ( preg_match( '|^\s*(https?://[^\s"]+)\s*$|im', $post->post_content, $matches ) ) {
                if (isset($matches[1]) && !empty($matches[1])) {
                    return $matches[1];
                }
            }
        }
        return '';
    }


    public static function get_post_image($size = 'tana-blog-grid', $ratio = '5x3', $playbtn = '', $return_empty = false, $label = '') {
        global $post;
        $thumbnail = $thumb = $media = "";

        if(has_post_thumbnail(get_the_ID())) {
            $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'tana-blog-grid');
            $thumb = $thumb_src[0];
        } elseif($return_empty == true) {
            return '';
        }

        $image = "<img src='".get_template_directory_uri()."/images/$ratio.png' alt='".esc_attr__('Proportion', 'tana')."'/>";

        $thumbnail = "<a href='".get_permalink()."'><div class='image' data-src='".$thumb."'>$image</div></a>";

        if($label !== '') {
            $thumbnail = "<a href='".get_permalink()."'><div class='image' data-src='".$thumb."'>$image<span class='label'>$label</span></div></a>";
        }

        if(get_post_format() == 'video' && $playbtn != 'no-player-icon') {

            if( strpos($playbtn, 'playpermalink') !== false) {
                $media = get_permalink();
            } else {
                $media = Tana_Tpl::get_post_video_url();
            }

            $thumbnail = "<div class='image video-frame' data-src='$thumb'>
                $image
                <a class='video-player $playbtn' href='$media'></a>
            </div>";
        }

        return $thumbnail;

    }


    public static function get_folio_gallery($fpost){
        if( has_shortcode($fpost->post_content, 'gallery') ):
            $gallery = get_post_gallery( $fpost->ID, false );
            $ids = explode(",", isset($gallery['ids']) ? $gallery['ids'] : "");

            $gallery_items = '';
            foreach ($ids as $a_id):
                $img_full = wp_get_attachment_image( $a_id, 'full' );

                $gallery_items .= '<div class="gallery-item">
                                        '.$img_full.'
                                    </div>';

            endforeach;

            return '<div class="gallery-slider owl-carousel">'.$gallery_items.'</div>';
        endif;

        return '';
    }


    public static function get_author_link(){
        global $post;
        return get_author_posts_url(get_the_author_meta('ID'));
    }


    
    public static function get_author_name(){
        global $post;
        return get_the_author();
    }


     
    public static function pagination( $query=null ) {
         
        global $wp_query;
        $query = $query ? $query : $wp_query;
        $big = 999999999;

        $paginate = paginate_links( array(
            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'type' => 'array',
            'total' => $query->max_num_pages,
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var('paged') ),
            'prev_text' => '<i class="fa fa-angle-left"></i>',
            'next_text' => '<i class="fa fa-angle-right"></i>',
            )
        );

        $result = '';

        if ($query->max_num_pages > 1) :
            $result .= "<ul class='pagination'>";
            foreach ( $paginate as $page ) {
                $result .= "<li>$page</li>";
            }
            $result .= "</ul>";
        endif;
        
        return $result;
    }





    public static function getCategories($post_id, $post_type){
        $cats = array();
        $taxonomies = get_object_taxonomies($post_type);
        if( !empty($taxonomies) ){
            $tax = $taxonomies[0];
            if( $post_type=='product' )
                $tax = 'product_cat';
            $terms = wp_get_post_terms($post_id, $tax);
            foreach ($terms as $term){
                $cats[] = array(
                                'term_id' => $term->term_id,
                                'name' => $term->name,
                                'slug' => $term->slug,
                                'link' => get_term_link($term)
                                );
            }
        }

        return $cats;
    }




    public static function getPostViews($postID){
        $count_key = 'post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        if($count=='' || $count=='0'){
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
            return "0";
        }
        return $count;
    }
    public static function setPostViews($postID) {
        $count_key = 'post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        if($count==''){
            $count = 0;
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
        }else{
            $count++;
            update_post_meta($postID, $count_key, $count);
        }
    }





    public static function get_share_links(){
        global $post;

        $thumb = array();
        if( has_post_thumbnail() ) {
           $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium'); 
        }
        $social = '';

        $social .= '<a href="http://www.facebook.com/sharer.php?u='.esc_url(get_permalink()).'" target="_blank" title="Facebook"><i class="fa fa-facebook"></i></a>';
        $social .= '<a href="https://twitter.com/share?url='.esc_url(get_permalink()).'&text='.esc_attr(get_the_title()).'" target="_blank"><i class="fa fa-twitter"></i></a>';
        $social .= '<a href="https://plus.google.com/share?url='.esc_url(get_permalink()).'" target="_blank"><i class="fa fa-google-plus"></i></a>';
        $social .= '<a href="https://pinterest.com/pin/create/bookmarklet/?media='.esc_url(isset($thumb[0]) ? $thumb[0] : '').'&url='.esc_url(get_permalink()).'&description='.esc_attr(get_the_title()).'" target="_blank"><i class="fa fa-pinterest"></i></a>';
        $social .= '<a href="#" onclick="window.print();return false;"><i class="fa fa-print"></i></a>'; 

        return $social;
    }


    public static function get_social_links($print = true){
        $social_fb = TT::get_mod('social_fb');
        $social_tw = TT::get_mod('social_tw');
        $social_gp = TT::get_mod('social_gp');
        $social_vm = TT::get_mod('social_vm');
        $social_yt = TT::get_mod('social_yt');
        $social_ln = TT::get_mod('social_ln');
        $social_in = TT::get_mod('social_in');

        $result = '';
        
        if( !empty($social_fb) ){
            $result .= '<a href="'.esc_attr($social_fb).'"><i class="fa fa-facebook"></i></a>';
        }
        if( !empty($social_tw) ){
            $result .= '<a href="'.esc_attr($social_tw).'"><i class="fa fa-twitter"></i></a>';
        }
        if( !empty($social_gp) ){
            $result .= '<a href="'.esc_attr($social_gp).'"><i class="fa fa-google-plus"></i></a>';
        }
        if( !empty($social_vm) ){
            $result .= '<a href="'.esc_attr($social_vm).'"><i class="fa fa-vimeo"></i></a>';
        }
        if( !empty($social_yt) ){
            $result .= '<a href="'.esc_attr($social_yt).'"><i class="fa fa-youtube"></i></a>';
        }
        if( !empty($social_ln) ){
            $result .= '<a href="'.esc_attr($social_ln).'"><i class="fa fa-linkedin"></i></a>';
        }
        if( !empty($social_in) ){
            $result .= '<a href="'.esc_attr($social_in).'"><i class="fa fa-instagram"></i></a>';
        }

        if( $print ){
            print($result);
        }
        else{
            return $result;
        }
    }

    public static function get_ads( $position = 'ads_post_bottom' ){
        $position = $position == '' ? 'ads_post_bottom' : $position;
        $ads = Tana_Std::get_mod($position);
        if( $ads != '' ) {
            printf('<div class="row ads"><div class="col-md-12">%s</div></div><!-- end .ads -->', $ads);
        }
    }

    public static function get_related_posts( $options=array() ){
        $options = array_merge(array(
                    'per_page'=>'6'
                    ),
                    $options);

        global $post;

        $args = array(
            'post__not_in' => array($post->ID),
            'posts_per_page' => $options['per_page']
        );
        $post_type_class = 'blog';

        $categories = get_the_category($post->ID);
        if ($categories) {
            $category_ids = array();
            foreach ($categories as $individual_category) {
                $category_ids[] = $individual_category->term_id;
            }
            $args['category__in'] = $category_ids;
        }

        // For portfolio post and another than Post
        if($post->post_type == 'portfolio') {
            $tax_name = 'portfolio_entries'; //should change it to dynamic and for any custom post types
            $args['post_type'] =  get_post_type(get_the_ID());
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $tax_name,
                    'field' => 'id',
                    'terms' => wp_get_post_terms($post->ID, $tax_name, array('fields'=>'ids'))
                )
            );
            $post_type_class = 'portfolio';
        }

        if(isset($args)) {
            $my_query = new wp_query($args);
            if ($my_query->have_posts()) {

                $html = '';
                $index = 0;
                while ($my_query->have_posts()) {
                    $my_query->the_post();

                    if($index == 3) {
                    $html .= "</div><!-- .row -->
                            <div class='row'>";
                    }
                    $index++;
                    
                    $html .= "<div class='col-md-4 col-sm-6'>

                                <div class='category-block articles'>
                                    <div class='post first'>
                                        <div class='meta'>
                                            <span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span>
                                            <span class='date'>".get_the_date()."</span>
                                        </div>
                                        <h4><a href='".get_permalink()."'>".get_the_title()."</a></h4>
                                        <p>".get_the_excerpt()."</p>
                                    </div>
                                </div>
                            </div>";
                }


                $html = "<div class='row'>
                            <div class='col-md-12'>
                                <div class='related-news'>
                                    <div class='border-line mv5'></div>
                                    <h2 class='title-block mv8' data-title='".esc_attr__('Related', 'tana')."'>
                                        ".esc_html__('You may like', 'tana')."
                                    </h2>
                                    <div class='row'>
                                        $html
                                    </div>
                                    <div class='border-line mv3'></div>
                                </div>
                            </div>
                        </div>";

                printf('%s', $html);
                
            }
        }
        wp_reset_postdata();
    }


    public static function get_related_music_posts( $options=array() ){
        $options = array_merge(array(
                    'per_page'=>'5'
                    ),
                    $options);

        global $post;

        $args = array(
            'post__not_in' => array($post->ID),
            'posts_per_page' => $options['per_page']
        );
        $post_type_class = 'blog';

        $categories = get_the_category($post->ID);
        if ($categories) {
            $category_ids = array();
            foreach ($categories as $individual_category) {
                $category_ids[] = $individual_category->term_id;
            }
            $args['category__in'] = $category_ids;
        }

        // For portfolio post and another than Post
        if($post->post_type == 'portfolio') {
            $tax_name = 'portfolio_entries'; //should change it to dynamic and for any custom post types
            $args['post_type'] =  get_post_type(get_the_ID());
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $tax_name,
                    'field' => 'id',
                    'terms' => wp_get_post_terms($post->ID, $tax_name, array('fields'=>'ids'))
                )
            );
            $post_type_class = 'portfolio';
        }

        if(isset($args)) {
            $my_query = new wp_query($args);
            if ($my_query->have_posts()) {

                $html = '';
                $index = 0;
                $column_class = 'col-xs-6 col-sm-4 col-md-15';
                while ($my_query->have_posts()) {
                    $my_query->the_post();
                    $index++;

                    $author = Tana_Std::getmeta('movie_author');

                    $thumb = '';
                    if( has_post_thumbnail() ){
                        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'tana-blog-square');
                        $thumb = !empty($thumb) ? $thumb[0] : '';
                    }

                    $html .= sprintf( '<div class="%7$s">
                                            <div class="post boxoffice-style ms-style ">
                                                <div class="image" style="background-image: url(%3$s);">
                                                    <a href="%1$s"><img src="%4$s/images/1x1.png" alt="image"></a>
                                                    <span class="label">#%2$s</span>
                                                    <a href="%1$s" class="icon-more"></a>
                                                </div>
                                                <h4><a href="%1$s">%5$s</a></h4>
                                                <h5><a href="%1$s">%6$s</a></h5>
                                            </div>
                                        </div>',
                                        get_permalink(), $index, $thumb, get_template_directory_uri(),
                                        get_the_title(), $author, $column_class
                            );
                }

                $html = "<div class='row'>
                            <div class='col-md-12'>
                                <div class='related-news'>
                                    <div class='border-line mv5'></div>
                                    <h2 class='title-block mv8' data-title='".esc_attr__('Related', 'tana')."'>
                                        ".esc_html__('You may like', 'tana')."
                                    </h2>
                                    <div class='row row-has-5-columns'>
                                        $html
                                    </div>
                                    <div class='border-line mv3'></div>
                                </div>
                            </div>
                        </div>";

                printf('%s', $html);
                
            }
        }
        wp_reset_postdata();
    }

    

    public static function clear_urls($content){
        $pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?]))/";
        $content = preg_replace($pattern, "", $content);
        return trim( $content );
    }



    public static function get_page_title(){
        global $post;
        $title = '';
        if( function_exists('is_shop') && is_shop() ):
            $title = esc_html__('Shop', 'tana');
        elseif( function_exists('is_shop') && is_product() ):
            $title = esc_html__('Shop Details', 'tana');
        elseif( is_archive() ):
            if(function_exists('the_archive_title')) :
                $title = get_the_archive_title();
            else:
                $title = sprintf( wp_kses( __('Category: <span>%s</span>', 'tana'), array('span'=>array()) ), single_cat_title( '', false ) );
            endif;

        elseif( is_search() ):
            $title = sprintf( wp_kses( __('For: <span>%s</span>', 'tana'), array('span'=>array()) ), get_search_query() );
        elseif( is_singular('portfolio') ):
            $title = get_the_title();
        elseif( is_single() ):
            $title = get_the_title();
        elseif( is_front_page() || is_home() ):
            if( is_home() ):
                $title = esc_html__('Blog', 'tana');
            elseif( get_query_var('post_type')=='portfolio' ):
                $title = esc_html__('Projects', 'tana');
            elseif( !is_front_page() && is_home() ):
                $reading_blog_page = get_option('page_for_posts');
                $po = get_post($reading_blog_page);
                $title = apply_filters('the_title', $po->post_title);
            else:
                $title = esc_html__('Home', 'tana');
            endif;
        elseif( is_404() ):
            $title = esc_html__('404 Page', 'tana');
        else:
            $title = get_the_title();
        endif;

        return $title;
    }



}