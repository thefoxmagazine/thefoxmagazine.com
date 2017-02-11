jQuery(document).ready(function($) {
		
		
	$(document).on('click', '.paginate .paginate-ajax', function() {

		//alert('Hello');

		var paged = $(this).attr('paged');
		var meta_keys = $(this).attr('meta_keys');
		var _location = $(this).attr('location');
		var company_name = $(this).attr('company_name');
			
			
		$('.paginate .paginate-ajax .fa-eercast').addClass('fa-spin');
			
		var hash, keywords, job_cat, job_type, job_status, expire_date;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		
		for(var i = 0; i < hashes.length; i++) {
		
			hash = hashes[i].split('=');
				
			if( hash[0] == 'keywords' ) keywords = hash[1];
			if( hash[0] == 'job_cat' ) job_cat = hash[1];
			if( hash[0] == 'job_type' ) job_type = hash[1];
			if( hash[0] == 'job_status' ) job_status = hash[1];
			if( hash[0] == 'expire_date' ) expire_date = hash[1];				
		}

		if( ! keywords ) keywords = $(this).attr('keywords');
		if( ! job_type ) job_type = $(this).attr('job_type');
		if( ! job_status ) job_status = $(this).attr('job_status');
			
			
			
			$.ajax(
				{
			type: 'POST',
			context: this,
			url:job_bm_ajax.job_bm_ajaxurl,
			data: { 
				"action": "job_bm_ajax_paginate_load_more", 
				'paged':paged,  
				'keywords':keywords,  
				'job_cat':job_cat,  
				'job_type':job_type,  
				'job_status':job_status,  
				'expire_date':expire_date,  
				'meta_keys':meta_keys,  
				'location':_location,  
				'company_name':company_name,  
			},
			success: function( data ) {
				

				$( data ).insertBefore( $(this).parent() );
				$('.paginate .paginate-ajax .fa-eercast').removeClass('fa-spin');


				// $('html, body').stop().animate({
					// scrollTop: $("#paged-" + paged).offset().top - 50
				// }, 1000);
				
				paged++;
				
				$(this).attr('paged', paged);
				
			},
			error: function (xhr, ajaxOptions, thrownError) {
				// alert(xhr.status);
				alert(thrownError);
			}
				});	
		
		})
		
		
		
		
		
		
		
		
		
		
		
		$(document).on('click', '.job-submit .plupload-upload-ui .delete', function()
			{

				var attach_id = $(this).attr('attach_id');
				
				//alert(attach_id);
				if(confirm('Do you really want to delete ?')){
					
					$.ajax(
						{
					type: 'POST',
					context: this,
					url:job_bm_ajax.job_bm_ajaxurl,
					data: {"action": "job_bm_ajax_delete_attachment", "attach_id":attach_id,},
					success: function(data)
							{	
								//alert('Hello');
								//$('#classified_maker_ads_thumbs').val(data);
								
								$(this).parent().fadeOut()
		
							}
						});	
					
					}
				

				
				
				})
		
		
		
		
		
		
		
		
		
		
		
		

		$(document).on('click', '.client-job-list .delete-job', function()
			{
				
				
				var is_confirm = $(this).attr('confirm');
				
				if(is_confirm=='ok'){
					
					var job_id = $(this).attr('job-id');
					
					$.ajax(
						{
					type: 'POST',
					context: this,
					url:job_bm_ajax.job_bm_ajaxurl,
					data: {"action": "job_bm_ajax_delete_job_by_id", "job_id":job_id,},
					success: function(data)
							{	
								//alert(data);
								$(this).html(data);
								
								$(this).parent().parent().fadeOut();
								//$('.see-phone-number .phone-number').html(data);
								//location.reload(true);
		
							}
						});
					
					
					}
				else{
						$(this).attr('confirm','ok');
						$(this).html('Confirm');
					
					}

				

				})

	});	







