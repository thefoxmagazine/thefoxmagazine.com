<?php get_header(); ?>

<?php 
while ( have_posts() ) : the_post();
    Tana_Tpl::setPostViews(get_the_id());

    // Layout
    $layout_class = 'col-sm-9 with-sidebar ';

    $single_layout = Tana_Std::getmeta('page_layout');
    $single_layout = $single_layout!='customize' ? $single_layout : Tana_Std::get_mod('post_single_view');
    $single_layout = !empty($single_layout) ? $single_layout : 'center';

    if ( $single_layout == 'left' ) {
        $layout_class .= 'pull-right';
    }
    else if( $single_layout == 'right' ) {
        global $tana_sidebar_position;
        $tana_sidebar_position = 'pull-right';
    }
    else if( $single_layout == 'music' ) {
        $layout_class = 'col-sm-10 col-sm-push-1';
    }
    else {
        $layout_class = 'col-sm-8 col-sm-push-2';
    }
    

    // Top parallax image
    if(Tana_Std::getmeta('topparallax') == 1 && has_post_thumbnail()) {
        $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
        $parallax_image = $thumb_src[0];
        $high = Tana_Std::getmeta('parallaxheight');
        $high = $high != '' ? $high : '400';
        echo "<div class='fullwidth-section image height-$high' data-src='$parallax_image' data-section-type='parallax'></div>";
    }
?>
<section class="content-area <?php echo esc_attr(sprintf('single-layout-%s', $single_layout)); ?>">

    <div class="container">

        <?php Tana_Tpl::get_ads('ads_post_top'); ?>
        
        <div class="row sticky-parent">
            
            <div class="<?php echo esc_attr($layout_class); ?> sticky-column">
                <div class="theiaStickySidebar">
                    <?php
                    $content_type = 'single';
                    $content_type = $single_layout=='music' ? 'music' : $content_type;
                    get_template_part( 'content', $content_type );
                    ?>
                </div>
            </div>

            <?php
            if ( in_array($single_layout, array('left', 'right')) ) {
                get_sidebar();
            }
            ?>

        </div>
        <!-- end .row -->

        

        <?php
        if( $single_layout=='music' ){
            Tana_Tpl::get_related_music_posts();
        }
        else{
            Tana_Tpl::get_related_posts();
        }
        ?>

        <?php Tana_Tpl::get_ads('ads_post_bottom'); ?>

        <div class="row">
            <div class="col-md-8 col-md-push-2">
                <?php
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            </div>
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->

</section>
<!-- /.content area -->

<?php
endwhile;
?>


<?php get_footer(); ?>