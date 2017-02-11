<?php get_header(); ?>


<?php
while ( have_posts() ) : the_post();
    $content_class = array();
    $page_layout = Tana_Std::getmeta('page_layout');
    $remove_padding = Tana_Std::getmeta('remove_padding');

    if( $remove_padding=='1' ){
        $content_class[] = 'pv0';
    }

    // array to strong
    $content_class = implode(' ', $content_class);
?>
<div class="content-area <?php echo esc_attr($content_class); ?>">

    <div class="section-full">
        <div class="container">

        <?php TPL::get_ads('ads_page_top'); ?>

        <?php if( $page_layout=='full' ): ?>
            <!-- no sidebar -->
            <div class="row">
                <div class="col-sm-12">
                    <?php get_template_part('content', 'page'); ?>
                </div>
            </div>
        <?php else: ?>
            <!-- with sidebar -->
            <?php
            $col_class = $page_layout=='left' ? ' pull-right' : '';
            ?>
            <div class="row sticky-parent">
                <div class="col-sm-9 with-sidebar sticky-column<?php echo esc_attr($col_class); ?>">
                    <div class="theiaStickySidebar">
                        <?php get_template_part('content', 'page'); ?>
                    </div>
                </div>
                <?php
                global $tana_sidebar;
                $tana_sidebar = 'page';
                get_sidebar();
                ?>
            </div>
        <?php endif; ?>

        <?php TPL::get_ads('ads_page_bottom'); ?>

        </div>
    </div>

</div>
<?php endwhile; ?>


<?php get_footer(); ?>