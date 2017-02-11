<?php

// =============================================================================
// VIEWS/ETHOS/WP-HEADER.PHP
// -----------------------------------------------------------------------------
// Header output for Ethos.
// =============================================================================

?>

<?php x_get_view( 'global', '_header' ); ?>

  <?php x_get_view( 'global', '_slider-above' ); ?>

  <header class="<?php x_masthead_class(); ?>" role="banner">
    <?php x_get_view( 'ethos', '_post', 'carousel' ); ?>
    <?php x_get_view( 'global', '_topbar' ); ?>
    <?php x_get_view( 'global', '_navbar' ); ?>
    <?php x_get_view( 'ethos', '_breadcrumbs' ); ?>
  </header>

  <?php x_get_view( 'global', '_slider-below' ); ?>
  <?php x_get_view( 'ethos', '_landmark-header' ); ?>
  
 <?php 
  if ( is_category('3') ):
    echo do_shortcode('[rev_slider alias="photographyslider"]');
  elseif ( is_category('33') ):
    echo do_shortcode('[rev_slider alias="fashion-slider"]'); 
  elseif ( is_category('2') ):
    echo do_shortcode('[rev_slider alias="travel-slider"]');
  elseif ( is_category('4') ):
    echo do_shortcode('[rev_slider alias="technology-slider"]'); 
  elseif ( is_category('31') ):
    echo do_shortcode('[rev_slider alias="music-slider"]');
  elseif ( is_category('32') ):
    echo do_shortcode('[rev_slider alias="food-slider"]'); 
  elseif ( is_category('72') ):
    echo do_shortcode('[rev_slider alias="photos-of-the-week-slider"]');
  endif;
?>