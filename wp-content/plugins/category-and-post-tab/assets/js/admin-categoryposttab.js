jQuery(document).ready(function(){
	jQuery('.color-field').wpColorPicker();
	jQuery(document).ajaxComplete(function(){  
		jQuery('.color-field').each(function(){
			var obj_parent = jQuery(this).parent().parent();
			jQuery(this).removeClass("wp-color-picker"); 
			jQuery(this).removeAttr("style");
			jQuery(this).show();
			jQuery(this).parent().find('.wp-picker-clear').remove();
			var hmt_color_picker_val =  jQuery(this).val();
			var hmt_color_picker = jQuery(this).parent().html();
			jQuery(obj_parent).html(hmt_color_picker); 
			jQuery(obj_parent).find(".color-field").wpColorPicker();  
		}); 
	});
});

function ck_category_check(ob_check) {
		var is_checked_len = jQuery(ob_check).parent().parent().find('input:checked').length; 
		if( is_checked_len == 0 ) {
			ob_check.checked = true;
		} 
}

function sel_change_categories_on_type(ob){
	(function( $ ) { 
		$(function() {
			var category_type = $(ob).val();
			var loading_image =  '<img src="'+categoryposttab.cpt_media+'images/loader.gif" />';
			$("#category_on_types").html(loading_image);
			$.ajax({
				url: categoryposttab.cpt_ajax_url,
				security: categoryposttab.cpt_security,
				data: {'action':'getCategoriesOnTypes',security: categoryposttab.cpt_security,category_type:category_type},
				success:function(data) {
					 $("#category_on_types").html(data);
				},error: function(errorThrown){ console.log(errorThrown);}
			});   
		});  
	})( jQuery );	

}

function sel_change_categories_on_type_widget(ob){
	(function( $ ) { 
		$(function() {
			var category_type = $(ob).val();
			var category_field_name = $(ob).parent().find("input.hid-category-name").val();
			var loading_image =  '<img src="'+categoryposttab.cpt_media+'images/loader.gif" />';
			$(".chk-categories-all").html(loading_image);
			$.ajax({
				url: categoryposttab.cpt_ajax_url,
				security: categoryposttab.cpt_security,
				data: {'action':'getCategoriesOnTypes',security: categoryposttab.cpt_security,category_field_name:category_field_name,category_type:category_type},
				success:function(data) { 
					 $(".chk-categories-all").html(data);
				},error: function(errorThrown){ console.log(errorThrown);}
			});   
		});  
	})( jQuery );	
}