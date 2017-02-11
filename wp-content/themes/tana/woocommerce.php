<?php get_header(); ?>


<?php

function tana_shop_loop_columns() {
    $woo_columns = Tana_Std::get_mod('woo_columns');
    return abs($woo_columns);
}

$woo_columns = Tana_Std::get_mod('woo_columns');
if( !empty($woo_columns) && $woo_columns!='default' && !is_product() ){
    add_filter('loop_shop_columns', 'tana_shop_loop_columns', 999);
}


$content_class = array();
$page_layout = Tana_Std::get_mod('woo_sidebars');
$page_layout = !empty($page_layout) ? $page_layout : 'full';
$page_layout = is_product() ? 'full' : $page_layout;

if( !empty($woo_columns) && $woo_columns!='default' && !is_product() ){
    $content_class[] = "woocommerce";
    $content_class[] = "columns-" . abs($woo_columns);
}

// array to strong
$content_class = implode(' ', $content_class);
?>
<div class="content-area <?php echo esc_attr($content_class); ?>">

    <div class="section-full">
        <div class="container">

        <?php TPL::get_ads('ads_page_top'); ?>

        <?php if( $page_layout=='full' ): ?>

            <div class="row">
                <div class="col-sm-12">
                    <?php woocommerce_content(); ?>
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
                        <?php woocommerce_content(); ?>
                    </div>
                </div>
                <?php
                global $tana_sidebar;
                $tana_sidebar = 'woo';
                get_sidebar();
                ?>
            </div>
        <?php endif; ?>

        <?php TPL::get_ads('ads_page_bottom'); ?>

        </div>
    </div>

</div>


<?php get_footer(); ?>