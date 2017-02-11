<?php
$header_class = array('header-blog', 'header-fashion');
$header_class = apply_filters( 'tana_header_classes', $header_class );
?>
<header id="header" class="<?php echo esc_attr(implode(' ', $header_class)); ?>">
    <div class="panel-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    
                    <div class="header-wrapper">

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

                        <div class="right-content">
                            <div class="search-panel">
                                <?php get_search_form(); ?>

                                <a href="javascript:;" class="search-handler search-fashion"><?php Tana_Tpl::the_search_icon(); ?></a>
                                <a href="javascript:;" class="burger-menu pm-right"><?php Tana_Tpl::the_burger_icon(); ?></a>
                                
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php get_template_part('templates/tpl', 'push-menu'); ?>

</header>