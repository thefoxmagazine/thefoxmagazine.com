<?php 
$footer_class = '';
if( Tana_Std::get_mod('footer_ancient')=='1' ){
    $footer_class .= ' footer-entertainment';
}
if( Tana_Std::get_mod('footer_light')=='1' ){
    $footer_class .= ' light';
}
$align_right = '';
if( Tana_Std::get_mod('footer_lastcol_right')=='1' ){
    $align_right = ' text-right';
}
global $post;
if( isset($post->post_name) && $post->post_name == 'welcome' ){
    $footer_class .= ' footer-welcome';
}

?>
<div class="clearfix"></div>

<footer id="footer" class="<?php echo esc_attr($footer_class);?>">
    <?php $footer_bg_style = Tana_Std::get_option_bg_value('footer_bg_image');
    if($footer_bg_style != '') : ?>
    <div class="footer-overlay" style="<?php echo esc_attr($footer_bg_style); ?>"></div>
    <?php endif; ?>
    <div class="container footer-container">


        <?php if(Tana_Std::get_mod('footer_top_enable') == '1') : ?>
        <div class="row footer-row mvt0 mv6">
            <div class="col-sm-6">
                <?php if( Tana_Std::get_mod('footer_logo') ){ ?>
                <img class="logo" src="<?php echo esc_attr(Tana_Std::get_mod('footer_logo'));?>" alt="<?php bloginfo('name'); ?>">
                <?php } ?>
            </div>
            <div class="col-sm-3">
            </div>
            <div class="col-sm-3 text-right">
            
                <?php if(Tana_Std::get_mod('footer_top_subscribe') == '1') : ?>
                <div class="widget">
                    <div class="subscribe-form">
                        <form>
                            <input type="text" placeholder="<?php esc_attr_e('ENTER YOUR EMAIL', 'tana');?>s">
                            <button type="submit"><i class="fa fa-envelope-o"></i></button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
        <?php endif; ?>


        <div class="row footer-row">
            <?php
            $footer_col = 4;
            $footer_columns = array();
            $footer_style = Tana_Std::get_mod('footer_style');
            switch($footer_style){
                case '1':
                    $footer_col = 1;
                    $footer_columns = array(
                            'col-sm-12'
                        );
                    break;
                case '2':
                    $footer_col = 2;
                    $footer_columns = array(
                            'col-sm-6',
                            'col-sm-6'
                        );
                    break;
                case '3':
                    $footer_col = 3;
                    $footer_columns = array(
                            'col-sm-4',
                            'col-sm-4',
                            'col-sm-4'
                        );
                    break;
                case '31':
                    $footer_col = 3;
                    $footer_columns = array(
                            'col-sm-4',
                            'col-sm-3',
                            'col-sm-5'
                        );
                    break;
                case '4':
                    $footer_col = 4;
                    $footer_columns = array(
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-3'
                        );
                    break;
                case '41':
                    $footer_col = 4;
                    $footer_columns = array(
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-4',
                            'col-sm-6 col-md-3'
                        );
                    break;
                case '42':
                    $footer_col = 4;
                    $footer_columns = array(
                            'col-sm-6 col-md-4',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-3'
                        );
                    break;
                case '5':
                    $footer_col = 5;
                    $footer_columns = array(
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-3'
                        );
                    break;
                case '51':
                    $footer_col = 5;
                    $footer_columns = array(
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-2'
                        );
                    break;
                case '52':
                    $footer_col = 5;
                    $footer_columns = array(
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-2'
                        );
                    break;
                case '5':
                    $footer_col = 5;
                    $footer_columns = array(
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-2'
                        );
                    break;
                case '6': // News home
                    $footer_col = 6;
                    $footer_columns = array(
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-2'
                        );
                    break;
                default:
                    $footer_col = 4;
                    $footer_columns = array(
                            'col-sm-6 col-md-4',
                            'col-sm-6 col-md-3',
                            'col-sm-6 col-md-2',
                            'col-sm-6 col-md-3'
                        );
                    break;
            }
            for ($i = 1; $i <= $footer_col; $i++) {
                // Align right last column if selected on customize option
                if($i == $footer_col) { $footer_columns[$i - 1] = $footer_columns[$i - 1].$align_right; }

                // Footer columns
                echo "<div class='".$footer_columns[$i - 1]." footer-column footer-column-$i'>";
                    dynamic_sidebar('footer'.$i);
                echo "</div>";
            }
            ?>
        </div>
        
    </div>

<?php 
if( Tana_Std::get_mod('sub_footer') == 1 ){
    get_template_part('templates/tpl', 'sub-footer');
}
?>

</footer>