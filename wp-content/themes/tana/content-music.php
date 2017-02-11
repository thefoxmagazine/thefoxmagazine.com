<?php
global $post;
$categories = get_the_category();
$last_cat = '';
if( !empty($categories) ){
    foreach( $categories as $category ){
    	$last_cat = $category->name;
    }
}


$meta_layout = 'col-md-2';
$content_layout = 'col-md-10';
$meta_horizontal = Tana_Std::get_mod('meta_inline');
if($meta_horizontal == '1') {
    $meta_layout = 'col-md-12 meta-horizontal';
    $content_layout = 'col-md-12';
}
?>

<article class="blog-item blog-single music-single">

    <div class="row div-table-small">
        <div class="col-sm-5">
            <?php
            if( has_post_thumbnail() ){
                $thumb_img = wp_get_attachment_image( get_post_thumbnail_id(), 'large' );
                printf('%s', $thumb_img);
            }
            ?>
        </div>
        <div class="col-sm-7">
            <div class="blog-single-meta">
                <?php
                $meta_author = Tana_Std::getmeta('movie_author');
                printf('<h5>%s</h5>', $meta_author);

                printf('<h1>%s</h1>', get_the_title());

                printf('<div class="entry-play-action">
                            <a href="javascript:;" class="button fill small text-light action-play-list">%s</a>
                            <a href="%s" class="button small">%s</a>
                        </div>',
                        esc_html__('play all', 'tana'), esc_url(Tana_Std::getmeta('trailer')), esc_html__('visit', 'tana')
                );

                $release_date = Tana_Std::getmeta('release_date');
                printf( '<div class="entry-post-info">
                            <div class="release-date"><strong>%s</strong> - %s</div>
                            <div class="view-date-share">
                                <span class="entry-view"><i class="fa fa-eye"></i>%s</span>
                                <span class="entry-date"><i class="fa fa-calendar"></i>%s</span>
                                <span class="entry-share">
                                    <i class="fa fa-share-alt"></i>Share
                                    <span class="share-links">%s</span>
                                </span>
                            </div>
                        </div>',
                        esc_html__('Release Date', 'tana'), $release_date, Tana_Tpl::getPostViews(get_the_id()),
                        get_the_date(get_option('date_format')), Tana_Tpl::get_share_links()
                );
                ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 entry-content">
        	<?php the_content(); ?>
            <?php wp_link_pages(array(
                    'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'tana') . '</span>',
                    'after' => '</div>',
                    'link_before' => '<span>',
                    'link_after' => '</span>',
                    'pagelink' => '<span class="screen-reader-text">' . esc_html__('Page', 'tana') . ' </span>%',
                    'separator' => '<span class="screen-reader-text">, </span>',
                ));
            ?>

            <?php
                $categories = get_the_category();
                $output = '';
                if (!empty($categories)) {
                    $numItems = count($categories);
                    $indx = 0;
                    foreach ($categories as $category) {
                        $output .= '<a href="' . esc_url(get_category_link($category->term_id)) . '" >' . esc_html($category->name) . '</a>';
                        if(++$indx !== $numItems) {
                            $output .= ', ';
                        }
                    }
                    print '<div class="content_tags">';
                    print '<span>'.esc_html_e('Categories: ', 'tana').'</span>';
                    printf($output);
                    print '</div>';
                }
            ?>
            
            <?php 
                $tag_list = get_the_tag_list();
                if( !empty($tag_list) ): ?>
                    <div class="content_tags">
                        <span><?php esc_html_e('Tags: ', 'tana'); ?></span>
                    <?php echo get_the_tag_list('', ', '); ?>
                    </div>
            <?php
                endif;
            ?>
        </div>

    </div>
    
</article>