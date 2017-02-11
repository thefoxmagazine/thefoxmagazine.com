jQuery(document).ready(function ($) {

	var MwlController = (function () {

		$.preloadImages = function () {
			for (var i = 0; i < arguments.length; i++) {
				$("<img />").attr("src", arguments[i]);
			}
		}

		var $mwl_imgs = $('.mwl-img'); // Images, not links.
		var mwl_imgs_number = $mwl_imgs.length;
		var mwl_imgs_src = new Array;
		var mwl_imgs_srcset = new Array;
		var mwl_imgs_size = new Array;
		var mwl_imgs_dimensions = new Array;
		var mwl_imgs_exifs = new Array;
		var mwl_imgs_exists = new Array;

		var img_width = 0;
		var img_height = 0;

		var resizeInit = false;
		var gotAllImagesInfos = false;

		function init(options) {
			var container_selector = options.container_selector;
			var template = options.template;

			addClassToImgs(container_selector);
			createLightbox(template);
			createMwlImagesArrays();
		};

		function addClassToImgs(container_selector) {
			$(container_selector).find('img').addClass('mwl-img');
			$mwl_imgs = $('.mwl-img');
			mwl_imgs_number = $mwl_imgs.length;
		}

		function createLightbox(template) {
			if (template == "standard") {
				var lightbox =
					"<div class='mwl-container standard invisible'> \
					  <div class='control-nav control-previous'><i class='ionicons ion-ios-arrow-left'></i></div> \
					  <div class='control-nav control-next'><i class='ionicons ion-ios-arrow-right'></i></div> \
					  <div class='control-layout control-close'><i class='ionicons ion-ios-close-empty'></i></div> \
					  <div class='mwl-img-container'></div>\
					</div>"
			}
			if (template == "photography") {
				var lightbox =
					"<div class='mwl-container photography invisible'> \
					  <div class='control-nav control-previous'><i class='ionicons ion-ios-arrow-left'></i></div> \
					  <div class='control-nav control-next'><i class='ionicons ion-ios-arrow-right'></i></div> \
					  <div class='control-layout control-go-full'><i class='ionicons ion-arrow-expand'></i></div> \
					  <div class='control-layout control-go-small'><i class='ionicons ion-arrow-shrink'></i></div> \
					  <div class='control-layout control-close'><i class='ionicons ion-ios-close-empty'></i></div> \
					  <div class='mwl-img-container'></div>\
					  <div class='mwl-img-infos-container'> \
						<div class='mwl-img-infos'> \
						  <h1></h1> \
						  <p></p> \
						</div> \
					  </div> \
					</div>"
			}
			$('body').append(lightbox);
		}

		function createMwlImagesArrays() {

			function pushInArrays(data, index) {
				mwl_imgs_exists[index] = data.file_exists;
				mwl_imgs_exifs[index] = data.file_data;
				mwl_imgs_dimensions[index] = data.file_dimension;
				mwl_imgs_src[index] = data.file_src;

				if (mwl_imgs_src.length == mwl_imgs_number) {
					gotAllImagesInfos = true;
				}
			}

			$mwl_imgs.each(function (index) {
				var $image = $(this);
				$image.attr('mwl-index', index);

				var image_id = $image.attr('mwl-img-id');
				if (image_id == undefined) {
					var image_classes = $image.attr('class').split(" ");
					image_classes.forEach(function (the_class) {
						var the_class = the_class;
						if (the_class.indexOf("wp-image") >= 0) {
							image_id = the_class.substring(9);
						}
					});
				}

				if (image_id != undefined) {
					jQuery.ajax({
						url: mwl.url_api + 'info/' + image_id,
						success: function (response) {
							if (response.file && response.data && response.dimension) {
								var file_exists = true;
								var file_src = response.file;
								var file_data = response.data;
								var file_dimension = response.dimension;
								var data = {
									file_exists: file_exists,
									file_data: file_data,
									file_dimension: file_dimension,
									file_src: file_src
								}
								pushInArrays(data, index);
							} else {
								var file_exists = false;
								var file_src = false;
								var file_data = false;
								var file_dimension = false;
								var data = {
									file_exists: file_exists,
									file_data: file_data,
									file_dimension: file_dimension,
									file_src: file_src
								}
								pushInArrays(data, index);
							}
						}
					});
				}
			});
		}

		function showLightbox(mwl_index) {
			if (gotAllImagesInfos) {
				var $mwl_container = $('.mwl-container');

				$mwl_container.removeClass('invisible');
				showImageInLightbox(mwl_index);
			}
			else if (mwl_imgs_src[mwl_index]) {
				var $mwl_container = $('.mwl-container');

				$mwl_container.removeClass('invisible');
				showImageInLightbox(mwl_index);
			}
		}

		function showImageInLightbox(mwl_index) {

			if (!mwl_imgs_exists[mwl_index]) {
				var img_error = true;
			} else {
				var img_error = false;
			}

			resizeInit = false;

			var $mwl_container = $('.mwl-container');
			var $mwl_img_container = $('.mwl-img-container');

			var next_index = mwl_index + 1;
			if (next_index > mwl_imgs_number - 1) {
				next_index = 0;
			}

			var next_url = mwl_imgs_src[next_index];
		  	if(next_url) {
				$.preloadImages(next_url); 
			}

			$mwl_img_container.removeClass('visible');
		  
			// No Image template / Image error template
			if (img_error) {
				$mwl_container.attr('mwl-current-img', mwl_index);
			    $mwl_img_container.addClass('no-loader');

				var image_tag = "<h3>Sorry, but this image couldn't be loaded !</h3>";

				$mwl_img_container.html(image_tag);

				var the_img = $mwl_img_container.find('img');

				resizeImg();

				$mwl_img_container.imagesLoaded(function () {
					setTimeout(function () {
						$mwl_img_container.addClass('visible');
					}, 10);
				});
			}

			// If standard template
			if ($mwl_container.hasClass('standard') && !img_error) {
		  		$mwl_img_container.removeClass('no-loader');
				$mwl_container.attr('mwl-current-img', mwl_index);

				var image_tag = "<img src='" + mwl_imgs_src[mwl_index] + "' class='mwl-img-in-lightbox'>";

				$mwl_img_container.html(image_tag);

				var the_img = $mwl_img_container.find('img');

				resizeImg();

				$mwl_img_container.imagesLoaded(function () {
					setTimeout(function () {
						$mwl_img_container.addClass('visible');
					}, 10);
				});
			}

			// If photography template
			if ($mwl_container.hasClass('photography') && !img_error) {
		  		$mwl_img_container.removeClass('no-loader');
				$mwl_container.attr('mwl-current-img', mwl_index);

				var image_tag = "<img src='" + mwl_imgs_src[mwl_index] + "' class='mwl-img-in-lightbox'>";

				$mwl_img_container.html(image_tag);

				var the_img = $mwl_img_container.find('img');

				resizeImg();

				$mwl_img_container.imagesLoaded(function () {
					setTimeout(function () {
						$mwl_img_container.addClass('visible');
					}, 10);
				});

				var $img_infos = $('.mwl-img-infos');

				// Make an ajax call to get the image infos
				var image_title = mwl_imgs_exifs[mwl_index].title;
				var image_description = mwl_imgs_exifs[mwl_index].caption;
				var image_camera = mwl_imgs_exifs[mwl_index].camera;
				var image_aperture = mwl_imgs_exifs[mwl_index].aperture;
				var image_focal_length = mwl_imgs_exifs[mwl_index].focal_length;
				var image_speed = mwl_imgs_exifs[mwl_index].shutter_speed;
				var image_iso = mwl_imgs_exifs[mwl_index].iso;

				// Create the html to go inside .mwl-img-infos
				var image_infos = "";


				image_infos = "<h1>" + image_title + "</h1>";
				<p>" + image_description + "</p>";
				<div class='image-exif'>";
				<span class="exif-data"><i class="ionicons ion-camera"></i>' + image_camera + "</span>";
				<span class="exif-data"><i class="ionicons ion-aperture"></i>' + image_aperture + "</span>";
				<span class="exif-data"><i class="ionicons ion-eye"></i>' + image_focal_length + "</span>";
				<span class="exif-data"><i class="ionicons ion-ios-stopwatch"></i>' + image_speed + "</span>";
				<span class="exif-data"><i class="ionicons ion-flash"></i>' + image_iso + "</span>";
				</div>";

				// We append the image_infos
				$img_infos.html(image_infos);
				$img_infos.removeClass('visible');
				setTimeout(function () {
					$img_infos.addClass('visible');
				}, 10);
				var timeout = 0;
				$img_infos.find('.image-exif .exif-data').each(function () {
					var exif_data = $(this);
					timeout += 50;
					setTimeout(function () {
						exif_data.addClass('visible');
					}, timeout);
				});
			}
		}

		function goToPrevImg() {
			var $mwl_container = $('.mwl-container');
			if (!$mwl_container.hasClass('invisible')) {
				var current_img = parseInt($mwl_container.attr('mwl-current-img'));
				var prev_img = current_img - 1;
				if (prev_img < 0) {
					prev_img = mwl_imgs_number - 1;
				}
				showImageInLightbox(prev_img);
			}
		}

		function goToNextImg() {
			var $mwl_container = $('.mwl-container');
			if (!$mwl_container.hasClass('invisible')) {
				var current_img = parseInt($mwl_container.attr('mwl-current-img'));
				var next_img = current_img + 1;
				if (next_img > mwl_imgs_number - 1) {
					next_img = 0;
				}
				showImageInLightbox(next_img);
			}
		}

		function hideLightbox() {
			var $mwl_container = $('.mwl-container');
			$mwl_container.addClass('invisible');
		}

		function resizeImg() {
			if (gotAllImagesInfos) {
				var current_img_index = $('.mwl-container').attr('mwl-current-img');
				var mwl_img_container = $('.mwl-img-container');
				var container_width = mwl_img_container.outerWidth();
				var container_height = mwl_img_container.outerHeight();

				var img = mwl_img_container.find('img');

				if (typeof mwl_imgs_dimensions[current_img_index] != 'undefined') {
					img_width = mwl_imgs_dimensions[current_img_index].width;
					img_height = mwl_imgs_dimensions[current_img_index].height;
				}

				var width_dif = container_width - img_width;
				var height_dif = container_height - img_height;

				var img_ratio = img_width / img_height;
				var container_ratio = container_width / container_height;

				if (img_ratio > container_ratio) {
					img.css('width', '100%');
					img.css('height', 'auto');
				} else {
					img.css('width', 'auto');
					img.css('height', '100%');
				}
			}
		}

		return {
			init: init,
			resizeImg: resizeImg,
			showLightbox: showLightbox,
			hideLightbox: hideLightbox,
			goToNextImg: goToNextImg,
			goToPrevImg: goToPrevImg,
			gotAllImgsInfos: gotAllImagesInfos
		};

	})();


	$(document).ready(function () {
		
		var options = {
			container_selector: mwl.settings.selector, // js selector
			template: mwl.settings.layout // standard | photography
		}

		MwlController.init(options);

		$('.mwl-img').on('click', function (e) {
			e.preventDefault();
			var image_index = $(this).attr('mwl-index');
			MwlController.showLightbox(image_index);
		});

		$(window).resize(function () {
			MwlController.resizeImg();
		});

		$(document).on('click', '.mwl-container:not(invisible)', function (e) {
			// Except if it's a nav link
			if ($(e.target).is($('.control-nav')) || $(e.target).is($('.control-layout')) || $(e.target).is($('i'))) {
				if ($(e.target).is($('.control-next')) || $(e.target).is($('.control-next i'))) {
					MwlController.goToNextImg();
				}
				if ($(e.target).is($('.control-previous')) || $(e.target).is($('.control-previous i'))) {
					MwlController.goToPrevImg();
				}
				if ($(e.target).is($('.control-go-full')) || $(e.target).is($('.control-go-full i'))) {
					$('.mwl-container').addClass('hide-infos');
					$('.control-go-full').hide();
					$('.control-go-small').show();
					MwlController.resizeImg();
				}
				if ($(e.target).is($('.control-go-small')) || $(e.target).is($('.control-go-small i'))) {
					$('.mwl-container').removeClass('hide-infos');
					$('.control-go-small').hide();
					$('.control-go-full').show();
					MwlController.resizeImg();
				}
				if ($(e.target).is($('.control-close')) || $(e.target).is($('.control-close i'))) {
					MwlController.hideLightbox();
				}
			} else {
				MwlController.hideLightbox();
			}
		});

		$(".mwl-img-container").swipe({
			//Generic swipe handler for all directions
			swipe: function (event, direction, distance) {
				if (direction == 'right' && distance > 50) {
					MwlController.goToPrevImg();
				} else if (direction == 'left' && distance > 50) {
					MwlController.goToNextImg();
				} else {
					MwlController.hideLightbox();
				}
			},
			threshold: 0
		});

		$(document).keydown(function (e) {
			if (!$('.mwl-container').hasClass('invisible')) {
				switch (e.which) {
				case 37: // left
					MwlController.goToPrevImg();
					break;

				case 39: // right
					MwlController.goToNextImg();
					break;

				case 27:
					MwlController.hideLightbox();
					break;

				default:
					return; // exit this handler for other keys
				}
				e.preventDefault(); // prevent the default action (scroll / move caret)
			}
		});

	});

});