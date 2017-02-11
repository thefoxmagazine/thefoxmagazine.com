<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php endif; ?>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php if(Tana_Std::get_mod('preloader_disable') !== '1') : ?>
    <!-- Loader -->
	<div class="tana-loader">
        <div class="loader-content">
            <div class="loader-circle"></div>
            <div class="loader-line-mask">
                <div class="loader-line"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Wrapper -->
    <div class="wrapper">

        <?php
        $header_layout = Tana_Std::get_mod('header_layout');
        switch($header_layout){
            case 'menu-left':
                get_template_part('templates/tpl', 'header-music');
                break;
            case 'menu-center':
                get_template_part('templates/tpl', 'header-fashion');
                break;
            case 'menu-right':
                get_template_part('templates/tpl', 'header-travel');
                break;
            case 'menu-burger':
                get_template_part('templates/tpl', 'header-movie');
                break;
            case 'menu-minimal':
                get_template_part('templates/tpl', 'header-minimal');
                break;
            case 'menu-shop':
                get_template_part('templates/tpl', 'header-shop');
                break;
            default:
                get_template_part('templates/tpl', 'header-news');
                break;
        }
        ?>
