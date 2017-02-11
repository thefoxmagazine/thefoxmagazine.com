<!-- Sidebar
================================================== -->
<?php 
global $tana_sidebar, $tana_sidebar_position;
$sidebar_id = 'sidebar';
if( isset($tana_sidebar) && !empty($tana_sidebar) ){
    $sidebar_id = $sidebar_id ."-". $tana_sidebar;
}

$sidebar_class = array('col-sm-3', 'sidebar', 'sticky-column', 'mb2');
if( !empty($tana_sidebar_position) ){
	$sidebar_class[] = $tana_sidebar_position;
}

$sidebar_class[] = "area-" . $sidebar_id;

$sidebar_class = implode(' ', $sidebar_class);
?>
<div class="<?php echo esc_attr($sidebar_class); ?>">
	<div class="theiaStickySidebar">
	    <?php
	    if ( is_active_sidebar( $sidebar_id ) ) :
	        dynamic_sidebar($sidebar_id);
	    else: 
	        echo "<div class='widget row'><h5>".esc_html__('Please add your widgets.', 'tana')."</h5></div>";
	    endif;
	    ?>
    </div>
</div>