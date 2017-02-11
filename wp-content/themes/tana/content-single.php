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

$title_txt = Tana_Std::get_mod('post_cover_title');
if ( $title_txt != '' && Tana_Std::getmeta('topparallax') !== '1' ) { ?>
<h2 class="title-block mt3 mb5" data-title="<?php echo esc_attr($last_cat); ?>"><?php printf('%s', $title_txt); ?></h2>
<?php } ?>

<article class="blog-item blog-single">
    
    <?php
    if(Tana_Std::getmeta('topparallax') !== '1' && Tana_Std::get_mod('disable_featured_media') !== '1') {
        echo Tana_TPL::get_post_media();
    }
    ?>

    <h2 class="post-title"><?php the_title(); ?></h2>

    <div class="row">

        <div class="<?php echo esc_attr($meta_layout); ?>">
            <div class="entry-details">
                <div class="entry-date"><?php echo get_the_date(get_option('date_format')); ?></div>
                <div class="entry-author">
                    <p>
                    <?php global $post; echo get_avatar($post->post_author, 54, '', esc_attr__( 'Avatar', 'tana' ), array('class'=>'image-small')); ?>
                    </p>
                    <h5><a href="<?php echo Tana_TPL::get_author_link(); ?>"><?php the_author_meta( 'display_name', $post->post_author ); ?></a></h5>
                    <span><?php comments_number(esc_html__('No comment', 'tana'), esc_html__('1 comment', 'tana'), esc_html__('% comments','tana')); ?></span>
                </div>
                <div class="entry-views"><?php echo Tana_Tpl::getPostViews(get_the_id()); ?> <?php esc_html_e('views', 'tana'); ?></div>
                <div class="entry-social">
                    <?php
                        echo Tana_Tpl::get_share_links();
                    ?>
                </div>
            </div>
            <!-- .entry-details -->
        </div>

        <div class="<?php echo esc_attr($content_layout); ?> entry-content">
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