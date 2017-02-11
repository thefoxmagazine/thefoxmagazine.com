if((typeof jQuery === 'undefined') && window.jQuery) {
	jQuery = window.jQuery;
} else if((typeof jQuery !== 'undefined') && !window.jQuery) {
	window.jQuery = jQuery;
}
var flg_v1 = 0; 

jQuery(document).ready(function($){ 
	$(".default-active-cpt").trigger("click");
});
function CPT_loadMorePosts(category_id,limit,elementId,total,request_obj){
	if(flg_v1==1) return;
	jQuery(document).ready(function($){ 
			var root_element = $("#"+elementId).parent();
			if($("#"+elementId).parent().parent().hasClass("lt-tab"))
				root_element = $("#"+elementId).parent().parent(); 
			
			var post_search_text = $(root_element).find(".item-posts").find(".ik-post-search-text").val();
			if((category_id==='undefined')) category_id = 0;
			if((post_search_text==='undefined')) post_search_text = ""; 
 			$(root_element).find(".item-posts").find(".ik-post-load-more").html("<div align='center'>"+$(".wp-load-icon").html()+"</div>");
			flg_v1 = 1;
			$.ajax({
				url: categoryposttab.cpt_ajax_url, 
				data: {'action':'getMorePosts',security: categoryposttab.cpt_security,'limit_start' : limit,'total' : total,'category_id' : category_id,'post_search_text' : post_search_text,'category_type' : request_obj.category_type,'post_type' : request_obj.post_type,'hide_searchbox' : request_obj.hide_searchbox,'hide_post_title' : request_obj.hide_post_title,'post_title_color' : request_obj.post_title_color,'category_tab_text_color' : request_obj.category_tab_text_color,'category_tab_background_color' : request_obj.category_tab_background_color,'header_text_color' : request_obj.header_text_color,'header_background_color' : request_obj.header_background_color,'display_title_over_image' : request_obj.display_title_over_image,'number_of_post_display' : request_obj.number_of_post_display,'vcode' : request_obj.vcode	},
				success:function(data) {     
					CPT_printData(elementId,data,"loadmore");
				},error: function(errorThrown){ console.log(errorThrown);}
			});
	});
}
function CPT_fillPosts(elementId,category_id,request_obj,flg_pr){
	if(flg_v1==1) return;
 	jQuery(document).ready(function($){
	
			$("#"+elementId).parent().parent().find(".pn-active").removeClass("pn-active");
			
	
			if($("#"+elementId).hasClass('pn-active') && flg_pr==1){
				$("#"+elementId).removeClass("pn-active");
				$("#"+elementId).parent().find(".item-posts").slideUp(600);
				return;
			}
			
			var root_element = $("#"+elementId).parent();
			if($("#"+elementId).parent().parent().hasClass("lt-tab"))
				root_element = $("#"+elementId).parent().parent();  
			 
			$("#"+elementId).addClass("pn-active");	
			 
			if(flg_pr==2){
				$(root_element).find(".ik-search-button").html("<br />"+$(".wp-load-icon").html()); 
			}
			else{  
				$("#"+elementId).find(".ik-load-content,.ik-post-no-items").remove();
				$("#"+elementId).find(".ld-pst-item-text").html("<div class='ik-load-content'>"+$(".wp-load-icon").html()+"</div>");
			}	
			var post_search_text = $(root_element).find(".item-posts").find(".ik-post-search-text").val();
			if((category_id==='undefined')) category_id = 0;
			if((post_search_text==='undefined')) post_search_text = "";
 			flg_v1 = 1;
		 	$.ajax({
				url: categoryposttab.cpt_ajax_url,
				security: categoryposttab.cpt_security,
				data: {'action':'getPosts',security: categoryposttab.cpt_security,flg_pr:flg_pr,'category_id' : category_id,'post_search_text' : post_search_text,'hide_searchbox' : request_obj.hide_searchbox,'category_type' : request_obj.category_type,'post_type' : request_obj.post_type,'hide_post_title' : request_obj.hide_post_title,'post_title_color' : request_obj.post_title_color,'category_tab_text_color' : request_obj.category_tab_text_color,'category_tab_background_color' : request_obj.category_tab_background_color,'header_text_color' : request_obj.header_text_color,'header_background_color' : request_obj.header_background_color,'display_title_over_image' : request_obj.display_title_over_image,'number_of_post_display' : request_obj.number_of_post_display,'vcode' : request_obj.vcode},
				success:function(data) { 
					CPT_printData(elementId,data,"fillpost"); 
				},error: function(errorThrown){ console.log(errorThrown);}
			});   
	});		

	;(function($){
		$(window).resize(function(){
			$(".wea_content .item-posts").each(function(){
				var root_element = $(this).parent();
				var cnt_width = $(this).parent().width();
				$(this).find(".ik-post-item").each(function(){
					if(cnt_width > 1024)		
						$(this).css("width","230px");
					else if(cnt_width <= 1024 && cnt_width > 768)	
						$(this).css("width","19%");
					else if(cnt_width <= 858 && cnt_width > 640)	
						$(this).css("width","24%");
					else if(cnt_width <= 640 && cnt_width > 480)	
						$(this).css("width","32%"); 
					else if(cnt_width <= 480 && cnt_width > 260)	
						$(this).css("width","49%");  
					else if(cnt_width <= 260)	
						$(this).css("width","99%");     
				});
				if(cnt_width<=390 && cnt_width > 280){
					$(root_element).find(".item-posts").find(".ik-post-category .ik-search-title").css("width","82%");
					$(root_element).find(".item-posts").find(".ik-post-category .ik-search-category").css("width","82%");
					$(root_element).find(".item-posts").find(".ik-post-category .ik-search-category").css("padding-top","10px");
					$(root_element).find(".item-posts").find(".ik-post-category .ik-search-button").css("padding-top","14px"); 
				}else if(cnt_width<=280){
					$(root_element).find(".item-posts").find(".ik-post-category .ik-search-title").css("width","82%");
					$(root_element).find(".item-posts").find(".ik-post-category .ik-search-category").css("width","82%");
				}else{
					$(root_element).find(".item-posts").find(".ik-post-category .ik-search-title").removeAttr("style");
					$(root_element).find(".item-posts").find(".ik-post-category .ik-search-category").removeAttr("style");
					$(root_element).find(".item-posts").find(".ik-post-category .ik-search-button").removeAttr("style");
				}
			});
		});
	})(jQuery);	
}
function CPT_printData(elementId,data,flg){
	jQuery(document).ready(function($){
		
	  	var root_element = $("#"+elementId).parent();
		if($("#"+elementId).parent().parent().hasClass("lt-tab"))
			root_element = $("#"+elementId).parent().parent(); 
		 
		if(flg=="loadmore"){
			$(root_element).find(".item-posts").find(".wp-load-icon").remove();
			$(root_element).find(".item-posts").find(".clr").remove();
			$(root_element).find(".item-posts").find(".ik-post-load-more").remove(); 
			$(root_element).find(".item-posts").append(data).fadeIn(400); 
			$(root_element).find(".item-posts").append("<div class='clr'></div>");
		}else{ 
			$("#"+elementId).find(".ik-load-content,.ik-post-no-items").remove();
			//$(root_element).find(".item-posts").fadeOut(1);
			//$(root_element).parent().find(".item-posts").fadeOut(1);
			$(root_element).find(".item-posts").html(data).fadeIn(400);  
		}
		
		var cnt_width = $("#"+elementId).parent().parent().width();
		var prod_item_height = [];
		$(root_element).find(".item-posts").find(".ik-post-item").each(function(){		
			
			if(cnt_width > 1024)		
				$(this).css("width","230px");
			else if(cnt_width <= 1024 && cnt_width > 768)	
				$(this).css("width","19%");
			else if(cnt_width <= 858 && cnt_width > 640)	
				$(this).css("width","24%");
			else if(cnt_width <= 640 && cnt_width > 480)	
				$(this).css("width","32%"); 
			else if(cnt_width <= 480 && cnt_width > 260)	
				$(this).css("width","49%");  
			else if(cnt_width <= 260)	
				$(this).css("width","99%");  	 
				
			prod_item_height.push($(this).find(".ik-post-name").height()); 
		});
		
		if(cnt_width<=390 && cnt_width > 280){
				$(root_element).find(".item-posts").find(".ik-post-category .ik-search-title").css("width","82%");
				$(root_element).find(".item-posts").find(".ik-post-category .ik-search-category").css("width","82%");
			//	$(root_element).find(".item-posts").find(".ik-post-category .ik-search-category").css("padding-top","10px");
			//	$(root_element).find(".item-posts").find(".ik-post-category .ik-search-button").css("padding-top","14px"); 
		}else if(cnt_width<=280){
				$(root_element).find(".item-posts").find(".ik-post-category .ik-search-title").css("width","82%");
				$(root_element).find(".item-posts").find(".ik-post-category .ik-search-category").css("width","82%");
		}else{
				$(root_element).find(".item-posts").find(".ik-post-category .ik-search-title").removeAttr("style");
				$(root_element).find(".item-posts").find(".ik-post-category .ik-search-category").removeAttr("style");
				$(root_element).find(".item-posts").find(".ik-post-category .ik-search-button").removeAttr("style");
		}
		if($(root_element).find(".item-posts").find(".ik-post-category .ik-search-category").length<=0){
			$(root_element).find(".item-posts").find(".ik-post-category .ik-search-title").css("margin-right",0);
		}
		
		if(cnt_width > 260)
		$(root_element).find(".item-posts").find(".ik-post-item").find(".ik-post-name").css("height",(Math.max.apply(Math,prod_item_height))+"px");
		
		flg_v1 = 0;	
	});	  
}
var flg_ms_hover = 0;
function cpt_pr_item_image_mousehover(ob_pii){ 
	if(flg_ms_hover == 1) return;
	jQuery(document).ready(function($){
		$(ob_pii).find(".ov-layer").show();  
		$(ob_pii).find(".ov-layer").css("visibility","visible"); 
		$(ob_pii).find(".ov-layer").css("top","40");  
		flg_ms_hover = 1;
		if($.trim($(ob_pii).find(".ov-layer").html())!="")
			$(ob_pii).find(".ov-layer").animate({opacity:0.9,top:0},0); 
		else
			$(ob_pii).find(".ov-layer").animate({opacity:0.5,top:0},0); 
	});
} 
function cpt_pr_item_image_mouseout(ob_pii){
	jQuery(document).ready(function($){ 
		$(ob_pii).find(".ov-layer").animate({opacity:0,top:40},0);
		flg_ms_hover = 0;
		$(ob_pii).find(".ov-layer").hide();
		$(ob_pii).find(".ov-layer").css("visibility","hidden");  
	});
}

function cpt_cat_tab_ms_out(ob_ms_eff){
	jQuery(document).ready(function($){ 
		$(ob_ms_eff).removeClass("pn-active-bg"); 	
	});
}
function cpt_cat_tab_ms_hover(ob_ms_eff){
	jQuery(document).ready(function($){ 
		$(ob_ms_eff).addClass("pn-active-bg"); 	
	});
}