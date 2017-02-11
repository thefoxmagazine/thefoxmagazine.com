<?php
$header_class = array('header-news');
$header_class = apply_filters( 'tana_header_classes', $header_class );
?>
<header id="header" class="<?php echo esc_attr(implode(' ', $header_class)); ?>">
    <div class="panel-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    
                    <div class="header-wrapper">

                        <div class="site-branding">
                            <?php Tana_Tpl::get_logo(); ?>
                        </div>

                        <div class="right-content">
                            <?php get_template_part('templates/tpl', 'user-menu'); ?>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <div class="panel-menu">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <nav class="main-nav">
                        <?php
                        wp_nav_menu( array(
                            'menu_id'           => 'primary-nav',
                            'theme_location'    => 'primary',
                            'container'         => '',
                            'fallback_cb'       => 'tana_primary_callback'
                        ) );
                        ?>

                        <div class="search-panel">
                            <form method="get">
                                <input type="text" name="s" placeholder="<?php esc_attr_e('Search...', 'tana'); ?>">
                                <button type="submit"></button>
                            </form>
                        </div>

                        
                        <div class="right-content news-search-menu">
                            <!-- Search Handler -->
                            <a href="javascript:;" class="search-handler" id="search_handler">
                                <?php Tana_Tpl::the_search_icon(); ?>
                                <?php Tana_Tpl::the_close_icon(); ?>
                            </a>
                            <!-- Burget Menu -->
                            <a href="javascript:;" class="burger-menu pm-right"><?php Tana_Tpl::the_burger_icon(); ?></a>
                        </div>

                    </nav>
                </div>
            </div>
        </div>
    </div>


    <?php
    $showticker = false;
    $ticker_enabled = Tana_Std::get_mod('ticker_enable');
    $ticker_single_enabled = Tana_Std::get_mod('ticker_single_enable');
    if( $ticker_enabled=='1'):
        $showticker = true;
        $ticker_title = Tana_Std::get_mod('ticker_title');
        $ticker_title = !empty($ticker_title) ? $ticker_title : esc_html__('Last Rumor', 'tana');
    endif;
    if( $ticker_enabled=='1' && is_single()):
        if($ticker_single_enabled == '1'):
            $showticker = true;
        else:
            $showticker = false;
        endif;
    endif;

    if($showticker):
    ?>
    <div class="panel-ticker">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="border-line mv0"></div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-9 col-sm-6">
                    <div class="tt-el-ticker">
                        <strong><?php printf('%s', $ticker_title); ?>: </strong>
                        <span class="entry-arrows">
                            <a href="javascript:;" class="ticker-arrow-prev"><img src="<?php echo get_template_directory_uri(); ?>/images/arrow-lr-left.png" alt="<?php echo esc_attr__('Proportion', 'tana'); ?>"></a>
                            <a href="javascript:;" class="ticker-arrow-next"><img src="<?php echo get_template_directory_uri(); ?>/images/arrow-lr-right.png" alt="<?php echo esc_attr__('Proportion', 'tana'); ?>"></a>
                        </span>
                        <span class="entry-ticker">
                            <?php
                            $ticker_slug = Tana_Std::get_mod('ticker_cat_slug');
                            $ticker_count = Tana_Std::get_mod('ticker_count');
                            $ticker_args = array(
                                'post_type' => 'post',
                                'posts_per_page' => abs($ticker_count),
                                'ignore_sticky_posts' => true,
                                'category_name' => $ticker_slug
                            );
                            $posts_query = new WP_Query($ticker_args);
                            while ( $posts_query->have_posts() ) {
                                $posts_query->the_post();
                                printf('<span><a href="%s">%s</a></span>', get_permalink(), get_the_title());
                            }
                            ?>
                        </span>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 text-right phl0">
                    <div class="tt-el-info text-right">
                        <?php Tana_Tpl::the_date_info(); ?>
                    </div>                            
                    <div class="tt-el-info text-right">
                        <?php Tana_Tpl::the_weather_info(); ?>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="border-line mvt0"></div>
                </div>
            </div>
        </div>
    </div>
    <?php
    endif;
    ?>

    <?php get_template_part('templates/tpl', 'push-menu'); ?>

</header>
