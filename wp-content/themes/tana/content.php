<?php

$content_class = 'post first text-bigger hover-dark';
if( is_single() ){
    $content_class .= ' blog-single';
}
else{
    $content_class .= ' blog-loop';
}

?>

<div <?php post_class($content_class); ?>>
    
    <?php
    echo Tana_TPL::get_post_media(); 
    ?>

    <div class="meta">
        <span class="author"><a href="<?php echo esc_url(Tana_TPL::get_author_link()); ?>"><?php the_author_meta( 'display_name', $post->post_author ); ?></a></span>
        <span class="date"><a href="<?php the_permalink(); ?>"><?php echo get_the_date(); ?></a></span>
    </div>

    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

    <?php
    $more_link = '<a href="'.esc_url(get_permalink()).'" class="category-more">'.esc_html__('Read More', 'tana').' <img src="'.get_template_directory_uri().'/images/arrow-right.png" alt="'.esc_attr__('Arrow', 'tana').'"></a>';
    if(strpos($post->post_content, '<!--more-->') > 0) :
        printf('<p>%s</p>', get_the_content(esc_html__('MORE >', 'tana') ));
    elseif(has_excerpt()) :
        printf('<p>%s</p>%s', wp_strip_all_tags(get_the_excerpt()), $more_link );
    else :
        printf( '<p>%s</p>%s', Tana_Tpl::clear_urls(wp_trim_words( wp_strip_all_tags(do_shortcode(get_the_content())), 30 )), $more_link );
    endif;
    ?>

</div>