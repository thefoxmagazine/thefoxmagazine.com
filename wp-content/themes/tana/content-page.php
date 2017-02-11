<div class="page-content">
    <?php
    if( Tana_Std::getmeta('title_show') == '1' ) {
        get_template_part('templates/tpl', 'page-title');
    }
    
    the_content();

    wp_link_pages( array(
        'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'tana') . '</span>',
        'after'       => '</div>',
        'link_before' => '<span>',
        'link_after'  => '</span>',
        'pagelink'    => '<span class="screen-reader-text">' . esc_html__('Page', 'tana') . ' </span>%',
        'separator'   => '<span class="screen-reader-text">, </span>',
    ) );
    ?>

    <?php
    if ( comments_open() || get_comments_number() ) :
        comments_template();
    endif;
    ?>
</div>