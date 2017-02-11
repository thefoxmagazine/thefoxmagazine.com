<?php
$pm_classes = array('push-menu');

$pm_overlay = Tana_Std::get_mod('pm_overlay');
if( $pm_overlay=='dark' ){
    $pm_classes[] = 'overlay-dark';
}

$pm_bg_dots = Tana_Std::get_mod('pm_bg_dots');
if( $pm_bg_dots=='1' ){
    $pm_classes[] = 'overlay-pattern';
}

$pm_hide_close = Tana_Std::get_mod('pm_close');
if( $pm_hide_close!='1' ){
    $pm_classes[] = 'hide-close-button';
}


?>
<div class="<?php echo esc_attr(implode(' ', $pm_classes)); ?>">
    <div class="pm-overlay"></div>
    <div class="pm-container">
        <div class="pm-viewport">
            <div class="pm-wrap">
                <a href="javascript:;" class="close-menu"></a>
                <div class="push-menu-widgets">
                <?php
                if ( is_active_sidebar('sidebar-push-menu') ) :
                    dynamic_sidebar('sidebar-push-menu');
                else: 
                    echo "<div class='widget'>
                            <h5>".esc_html__('Please add your widgets.', 'tana')."</h5>
                        </div>";
                endif;
                ?>
                </div>
                <?php if(Tana_Std::get_mod('pm_home_link')=='1'): ?>
                <div class="pm-go-home">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <i data-bg-image="<?php echo get_template_directory_uri();?>/images/pm-en-ico-home.png"></i>
                        <?php esc_html_e('GO TO HOME', 'tana'); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>