<?php
$header_class = array('header-blog');
$header_class = apply_filters( 'tana_header_classes', $header_class );
?>
<header id="header" class="<?php echo esc_attr(implode(' ', $header_class)); ?>">
    <div class="panel-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    
                    <div class="header-wrapper">

                        <!-- image logo -->
                        <div class="site-branding">
                            <?php Tana_Tpl::get_logo(); ?>
                        </div>

                        <nav class="main-nav">
                            <?php
                            wp_nav_menu( array(
                                'menu_id'           => 'primary-nav',
                                'theme_location'    => 'primary',
                                'container'         => '',
                                'fallback_cb'       => 'tana_primary_callback'
                            ) );
                            ?>
                        </nav>

                        <?php if( Tana_Std::get_mod('header_search_mode')=='1' ): ?>
                        <div class="right-content">
                            <div class="search-panel">
                                <?php get_search_form(); ?>
                            </div>

                            <div class="search_and_menu">
                                <a href="javascript:;" class="search-handler">
                                    <?php Tana_Tpl::the_search_icon(); ?>
                                </a>
                                <a href="javascript:;" class="burger-menu pm-right">
                                    <?php Tana_Tpl::the_burger_icon(); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>

        <a href="javascript:;" id="burger_menu" class="burger-menu">
            <?php Tana_Tpl::the_burger_icon(); ?>
        </a>
    </div>

    <?php get_template_part('templates/tpl', 'push-menu'); ?>

</header>