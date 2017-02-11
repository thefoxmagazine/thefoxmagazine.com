<?php
$header_class = array('header-welcome');
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

                        <div class="right-content welcome-navigation-menu"></div>
                        <div class="welcome-nav-hidden hidden">
                            <?php
                            wp_nav_menu( array(
                                'menu_id'           => 'primary-nav',
                                'theme_location'    => 'primary',
                                'container'         => '',
                                'fallback_cb'       => 'tana_primary_callback'
                            ) );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>