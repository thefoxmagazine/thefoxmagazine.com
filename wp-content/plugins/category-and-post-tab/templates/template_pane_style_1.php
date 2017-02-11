<?php if ( ! defined( 'ABSPATH' ) ) exit;   $vcode = $this->_config["vcode"];  ?>
 <script type='text/javascript' language='javascript'>
	var default_category_id_<?php echo esc_js( $vcode ); ?> = '<?php echo  esc_js( $this->_config["category_id"] ); ?>';
	var request_obj_<?php echo esc_js( $vcode ); ?> = {
			category_id:'<?php echo  esc_js( $this->_config["category_id"]) ; ?>',  
			hide_post_title:'<?php echo esc_js( $this->_config["hide_post_title"] ); ?>', 
			post_title_color:'<?php echo esc_js( $this->_config["title_text_color"] ); ?>',
			category_tab_text_color:'<?php echo esc_js( $this->_config["category_tab_text_color"] ); ?>', 
			category_tab_background_color:'<?php echo esc_js( $this->_config["category_tab_background_color"] ); ?>',
			header_text_color:'<?php echo esc_js( $this->_config["header_text_color"] ); ?>', 
			header_background_color:'<?php echo esc_js( $this->_config["header_background_color"] ); ?>',
			display_title_over_image:'<?php echo esc_js( $this->_config["display_title_over_image"] ); ?>', 
			number_of_post_display:'<?php echo esc_js( $this->_config["number_of_post_display"] ); ?>', 
			vcode:'<?php echo esc_js( $vcode ); ?>'
		}
 </script> 
 
 <?php $_categories = $this->_config["category_id"]; ?>
  
 <div id="categoryposttab" style="width:<?php echo $this->_config["tp_widget_width"]; ?>"  class="pane_style_1 <?php echo ( ( trim( $this->_config["display_title_over_image"] ) == "yes" ) ? "disp_title_over_img" : "" ); ?>">
	<?php if($this->_config["hide_widget_title"]=="no"){ ?>
		<div class="ik-pst-tab-title-head" style="background-color:<?php echo esc_attr( $this->_config["header_background_color"] ); ?>;color:<?php echo esc_attr( $this->_config["header_text_color"] ); ?>"  >
			<?php echo esc_html( $this->_config["widget_title"] ); ?>   
		</div>
	<?php } ?> 
	<span class='wp-load-icon'>
		<img width="18px" height="18px" src="<?php echo CPT_MEDIA.'images/loader.gif'; ?>" />
	</span>
	<div class="wea_content lt-tab">
		<?php 
			
			$_category_res = array();
			
			if( trim($_categories)=="0" || trim($_categories) == "" )
				$_category_res = $this->getCategories();
			else 
				$_category_res = $this->getCategories($_categories);
				 
			
			if( count( $_category_res ) > 0 ) {
				$_ik  = 0;
				foreach($_category_res as $_category){ 
					$_category_name = $_category->category;
					$_category_id = $_category->id;
					
					$_ik_active_class = "";
					if($_ik == 0)
						$_ik_active_class = "default-active-cpt";
					
					$_ik++;
					?>
					<div class="item-pst-list">
						<div class="pst-item  <?php echo $_ik_active_class; ?>"  onmouseout="cpt_cat_tab_ms_out( this )" onmouseover="cpt_cat_tab_ms_hover( this )" id="<?php echo $vcode.'-'.$_category_id; ?>" onclick="CPT_fillPosts( this.id, '<?php echo esc_js($_category_id ); ?>', request_obj_<?php echo esc_js( $vcode ); ?>, 1 )"  style="color:<?php echo esc_attr($this->_config["category_tab_text_color"] ); ?>;background-color:<?php echo esc_attr( $this->_config["category_tab_background_color"] ); ?>;" >
							<div class="pst-item-text"  onmouseout="cpt_cat_tab_ms_out( this.parentNode )" onmouseover="cpt_cat_tab_ms_hover( this.parentNode )">
								<?php echo esc_html( $_category_name ); ?>
							</div>
							<div class="ld-pst-item-text"></div>
							<div class="clr"></div>
						</div> 
					 </div>  
				   <?php
				}
			} 
		?>
		<div class="clr"></div>
		<div class="item-posts"><?php echo __( 'Loading.....', 'categoryposttab' ); ?></div>
		<div class="clr"></div>
	</div>
</div>