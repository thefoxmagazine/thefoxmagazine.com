<?php
$header_class = array('header-entertainment');
$header_class = apply_filters( 'tana_header_classes', $header_class );
?>
<header id="header" class="<?php echo esc_attr(implode(' ', $header_class)); ?>">
    <div class="panel-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    
                    <div class="header-wrapper">

                        <div class="left-content">
                            <div class="tools">
                                <a href="javascript:;" id="search_handler">
                                    <?php Tana_Tpl::the_search_icon(); ?>
                                    <?php Tana_Tpl::the_close_icon(); ?>
                                </a>
                                <a href="javascript:;" id="burger_menu" class="burger-menu">
                                    <?php Tana_Tpl::the_burger_icon(); ?>
                                </a>
                                <div class="search-panel">
                                    <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
                                        <?php Tana_Tpl::the_search_icon(); ?>
                                        <input type="text" name="s" placeholder="<?php esc_attr_e('Type and press enter to search...', 'tana'); ?>">
                                        <button type="submit"></button>
                                    </form>
                                </div>
                            </div>

                            <div class="site-branding">
                                <?php Tana_Tpl::get_logo(); ?>
                            </div>
                        </div>

                        <div class="right-content">
                    
                            <?php get_template_part('templates/tpl', 'user-menu'); ?>

                            <?php if( Tana_Std::get_mod('header_info_el')=='1' ): ?>
                            <div class="tt-el-info inline-style tt-info-weather">
                                <?php Tana_Tpl::the_weather_info(); ?>
                            </div>
                            <div class="tt-el-info inline-style tt-info-date">
                                <?php Tana_Tpl::the_date_info(); ?>
                            </div>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>
            </div>
        </div>
        
    </div>

    <?php get_template_part('templates/tpl', 'push-menu'); ?>

</header>