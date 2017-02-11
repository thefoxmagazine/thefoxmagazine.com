<?php 
$sub_padding = "";
$border_line = "<div class='border-line'></div>";
if( Tana_Std::get_mod('sub_footer_mini') == '1' ):
    $sub_padding = 'pv2';
    $border_line = "";
endif; ?>
<div class="sub-footer <?php echo esc_attr($sub_padding); ?>">
    <div class="container">
        <?php $subfootercol = 'col-sm-12'; if(Tana_Std::get_mod('sub_footer_locationmeta') == '1') {$subfootercol = 'col-sm-6'; } ?>
        <?php if( Tana_Std::get_mod('social_subfooter')==1 && Tana_Std::get_mod('sub_footer_mini') != '1' ){ ?>
        <div class="row footer-row mv1">
            <div class="<?php echo esc_attr($subfootercol); ?>">
                <div class="widget">
                    <div class="social-links">
                        <?php Tana_Tpl::get_social_links(); ?>
                    </div>
                </div>
            </div>

            <?php if( Tana_Std::get_mod('sub_footer_locationmeta') == '1' ) { ?>
            <div class="<?php echo esc_attr($subfootercol); ?> text-right">
                <div class="tt-el-info inline-style tt-info-weather">
                    <?php Tana_Tpl::the_weather_info(); ?>
                </div>
                <div class="tt-el-info inline-style tt-info-date">
                    <?php Tana_Tpl::the_date_info(); ?>
                </div>
            </div>
            <?php } ?>

        </div>
        <?php } ?>

        <div class="row">
            <div class="col-sm-12">
                <?php print($border_line); ?>

                <?php
                wp_nav_menu( array(
                    'menu_class'        => 'list-inline pull-left',
                    'theme_location'    => 'footer_menu',
                    'container'         => '',
                    'depth'             => 1,
                    'fallback_cb'       => 'tana_footer_menu_callback'
                ));
                ?>
                <div class="copyright-text pull-right"><i class="fa fa-chevron-up scroll-to-top"></i> <?php echo Tana_Std::get_mod('copyright_content'); ?></div>
            </div>
        </div>
        
    </div>
</div>
<!-- end .sub-footer -->