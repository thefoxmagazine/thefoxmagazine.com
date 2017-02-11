<?php
$header_class = array('header-blog');
$header_class[] = 'header-shop';
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
                            <a href="javascript:;" id="burger_menu" class="burger-menu">
                                <?php Tana_Tpl::the_burger_icon(); ?>
                            </a>
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

                            <div class="search_and_menu">
                                <div class="right-content-item rc-item-search">
                                    <a href="javascript:;" class="search-handler">
                                        <?php Tana_Tpl::the_search_icon(); ?>
                                    </a>
                                    <div class="search-panel">
                                        <?php get_search_form(); ?>
                                    </div>
                                </div>
                                <?php
                                if( class_exists('WooCommerce') ):
                                    $count = WC()->cart->cart_contents_count; ?>
                                <div class="right-content-item rc-item-cart">
                                    <a class="cart-contents" href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php esc_attr_e('View your shopping cart', 'tana'); ?>">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 18 18" xml:space="preserve">
                                             <path d="M17.9,3.7c-0.1-0.1-0.3-0.2-0.4-0.2L6.3,3C6,3,5.7,3.2,5.7,3.6c0,0.3,0.2,0.6,0.6,0.6l10.4,0.5l-2,6.4H5.5L3.8,2c0-0.2-0.2-0.4-0.4-0.4L0.8,0.5C0.5,0.4,0.2,0.6,0,0.9c-0.1,0.3,0,0.6,0.3,0.8l2.4,0.9l1.7,9.1C4.5,12,4.7,12.2,5,12.2h0.3L4.6,14c-0.1,0.1,0,0.3,0.1,0.4c0.1,0.1,0.2,0.2,0.4,0.2h0.4c-0.3,0.3-0.4,0.7-0.4,1.2c0,1,0.8,1.7,1.7,1.7c1,0,1.7-0.8,1.7-1.7c0-0.4-0.2-0.9-0.4-1.2h3.8c-0.3,0.3-0.4,0.7-0.4,1.2c0,1,0.8,1.7,1.7,1.7c1,0,1.7-0.8,1.7-1.7c0-0.4-0.2-0.9-0.4-1.2H15c0.3,0,0.5-0.2,0.5-0.5c0-0.3-0.2-0.5-0.5-0.5H5.8l0.5-1.4H15c0.3,0,0.5-0.2,0.5-0.4L18,4.3C18,4.1,18,3.9,17.9,3.7z M6.8,16.5c-0.4,0-0.8-0.3-0.8-0.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8C7.6,16.2,7.3,16.5,6.8,16.5z M13.2,16.5c-0.4,0-0.8-0.3-0.8-0.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8C14,16.2,13.6,16.5,13.2,16.5z"/>
                                        </svg>
                                        <span class="cart-contents-count"><?php echo abs($count); ?></span>
                                    </a>
                                    <div class="cart-content-list">
                                        <div class="widget_shopping_cart_content"></div>
                                    </div>
                                </div>
                                <?php endif; ?>

                            </div>
                        </div>
                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <?php get_template_part('templates/tpl', 'push-menu'); ?>

</header>
