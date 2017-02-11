/*global $, jQuery, alert, Mustache, view, mwl*/
/*jslint plusplus: true */

jQuery(document).ready(function ($) {

	"use strict";

	var MwlImage = function (datas) {
		this.id = datas.id;
		this.index = datas.index;
		this.exists = datas.exists;
		this.img_src = datas.img_src;
		this.img_srcset = datas.img_srcset;
		this.img_sizes = datas.img_sizes;
		this.img_dimensions = datas.img_dimensions;
		this.img_orientation = datas.img_orientation;
		this.img_exifs = datas.img_exifs;
	};

	window.MwlController = function (settings) {
		var $mwl_imgs,
			mwl_imgs_number,
			current_image_index,
			display_missing_images,
			mwl_imgs_array = [];

		display_missing_images = settings.display_missing_images;

		function preloadImage(arrayOfImages) {
			$(arrayOfImages).each(function(){
				$('<img/>')[0].src = this;
			});
		}

		function addClassToImgs() {
			$(settings.selector).find('img').addClass('mwl-img');
			mwl_imgs_number = $('.mwl-img').length;
		}

		function addIndexToImgs() {
			$('.mwl-img').each(function (index) {
				var $image = $(this);
				$image.attr('mwl-index', index);
			});
		}

		function createMwlTargetDiv(template) {
			var lightbox_container = "<script type='text/mustache' id='mwl-script-target'></script><div id='mwl-target'></div>";
			$('body').append(lightbox_container);
		}

		function createMwlImgsArray() {

			$('.mwl-img').each(function (index) {
				// if we already have an id from the filter
				var $image = $(this),
					image_id = $image.attr('mwl-img-id'),
					image_classes,
					datas,
					temp_mwl_img;
				// else, we try to get it from the wp-image-id class
				if (!image_id) {
					image_classes = $image.attr('class').split(" ");
					image_classes.forEach(function (the_class) {
						if (the_class.indexOf("wp-image") >= 0) {
							image_id = the_class.substring(9);
						}
					});
				}

				// if we managed to retrieve an id, we make an ajax call
				if (image_id !== undefined) {
					jQuery.ajax({
						url: mwl.url_api + 'info/' + image_id,
						success: function (response) {
							if(response) {
								if (response.file && response.data && response.dimension) {
									datas = {
										id: image_id,
										index: index,
										exists: true,
										img_src: response.file,
										img_srcset: response.file_srcset,
										img_sizes: response.file_sizes,
										img_dimensions: response.dimension,
										img_orientation: response.dimension.width > response.dimension.height ? "landscape" : "portrait",
										img_exifs: response.data
									};
									temp_mwl_img = new MwlImage(datas);
									mwl_imgs_array[index] = temp_mwl_img;
								} else {
									datas = {
										exists: false,
										index: index
									};
									temp_mwl_img = new MwlImage(datas);
									mwl_imgs_array[index] = temp_mwl_img;
								}
							}
						}
					});
				} else {
					datas = {
						exists: false,
						index: index
					};
					temp_mwl_img = new MwlImage(datas);
					mwl_imgs_array[index] = temp_mwl_img;
				}
			});

		}

		function resizeImg() {
			var $mwl_img_container = $('.mwl-img-container'),
				$img = $('.mwl-img-in-lightbox'),

				container_width = $mwl_img_container.outerWidth(),
				container_height = $mwl_img_container.outerHeight(),

				img_width = mwl_imgs_array[current_image_index].img_dimensions.width,
				img_height = mwl_imgs_array[current_image_index].img_dimensions.height,

				width_dif = container_width - img_width,
				height_dif = container_height - img_height,

				img_ratio = img_width / img_height,
				container_ratio = container_width / container_height;

			if (img_ratio > container_ratio) {
				$img.css('width', '100%');
				$img.css('height', 'auto');
			} else {
				$img.css('width', 'auto');
				$img.css('height', '100%');
			}
		}

		function updateData(template, mwl_image) {

			// Orientation
			if (!jQuery('.mwl-container').hasClass(mwl_image.img_orientation)) {
				jQuery('.mwl-container').removeClass('landscape portrait');
				jQuery('.mwl-container').addClass(mwl_image.img_orientation);
			}

			// Image
			jQuery('.mwl-img-container img').attr('url', mwl_image.img_src);
			jQuery('.mwl-img-container img').attr('srcset', mwl_image.img_srcset);

			// EXIF
			jQuery('.mwl-content .exif-data-item').each(function () {
				var item = jQuery(this).attr('mwl-data-item');
				if (item) {
					var item_value = mwl_image.img_exifs[item];
					if (!item_value || item_value == "N/A") {
						if(item != "caption") {
							jQuery(this).parent().hide();	
						}
					}
					else {
						jQuery(this).html(item_value);
						jQuery(this).parent().show();
					}
				}
			});
		}

		function getTemplate(mwl_image, mwl_layout, mwl_theme, animations) {
			if (mwl_image && mwl_image.exists) {
				$("#mwl-script-target").load(mwl.plugin_url + "/templates/" + mwl_layout + ".html", function (template) {
					//var rendered = Mustache.render(template, mwl_image);
					$('#mwl-target').html(template);
					updateData(template, mwl_image);
					if (animations === 1) {
						$('.mwl-container').removeClass('invisible');
						setTimeout(function () {
							$('.mwl-content').addClass('visible');
						}, 1);
					}
					if (animations === 2) {
						setTimeout(function () {
							$('.mwl-container').removeClass('invisible');
							$('.mwl-content').addClass('visible');
						}, 1);
					}
					$('.mwl-container').addClass(mwl_theme);
					resizeImg();
				});
			} else if (display_missing_images) {
				$("#mwl-script-target").load(mwl.plugin_url + "/templates/undefined.html", function (template) {
					//var rendered = Mustache.render(template, mwl_image);
					$('#mwl-target').html(template);
					updateData(template, mwl_image);
					$('.mwl-container').removeClass('invisible');
					setTimeout(function () {
						$('.mwl-content').addClass('visible');
					}, 1);
					$('.mwl-container').addClass(mwl_theme);
				});
			}
		}

		/**
		 * Initialize lightbox
		 */
		this.init = function () {
			createMwlTargetDiv();
			addClassToImgs('.entry-content');
			addIndexToImgs();
			createMwlImgsArray();
		};

		/**
		 * Resize images in lightbox to fill the screen
		 */
		this.resizeImg = function () {
			if (mwl_imgs_array[current_image_index]) {
				if (mwl_imgs_array[current_image_index].exists) {
					var $mwl_img_container = $('.mwl-img-container'),
						$img = $('.mwl-img-in-lightbox'),

						container_width = $mwl_img_container.outerWidth(),
						container_height = $mwl_img_container.outerHeight(),

						img_width = mwl_imgs_array[current_image_index].img_dimensions.width,
						img_height = mwl_imgs_array[current_image_index].img_dimensions.height,

						width_dif = container_width - img_width,
						height_dif = container_height - img_height,

						img_ratio = img_width / img_height,
						container_ratio = container_width / container_height;

					if (img_ratio > container_ratio) {
						$img.css('width', '100%');
						$img.css('height', 'auto');
					} else {
						$img.css('width', 'auto');
						$img.css('height', '100%');
					}
				}
			}
		};

		/**
		 * Get the next image index
		 * @returns {[[int]]} index [[next image index]]
		 */
		this.getNextIndex = function () {
			var index = current_image_index,
				next_index = index + 1;
			if (next_index === mwl_imgs_number) {
				next_index = 0;
			} else {
				next_index = index + 1;
			}
			return next_index;
		};

		/**
		 * Get the previous image index
		 * @returns {[[int]]} index [[previous image index]]
		 */
		this.getPrevIndex = function () {
			var index = current_image_index,
				prev_index = index - 1;
			if (prev_index < 0) {
				prev_index = mwl_imgs_number - 1;
			} else {
				prev_index = index - 1;
			}
			return prev_index;
		};

		/**
		 * Get an image with its index
		 * @param   {[[int]]} index [[image index]]
		 * @returns {[[MwlImage]]} [[image]]
		 */
		this.getMwlImageByIndex = function (index) {
			return mwl_imgs_array[index];
		};

		/**
		 * Change the image in the lightbox ( opened )
		 * @param {[[MwlImage]]} mwl_image    [[image to display]]
		 * @param {[[string]]} mwl_layout [[lightbox template]]
		 */
		this.changeLightboxImage = function (mwl_image, mwl_layout, mwl_theme, navigating) {
			current_image_index = mwl_image.index;
			if (mwl_image && mwl_image.exists) {
				getTemplate(mwl_image, mwl_layout, mwl_theme, 1);
				var next_image_index = this.getNextIndex();
				var next_image = this.getMwlImageByIndex(next_image_index);
				if(next_image.exists) {
					preloadImage([next_image.img_src]);	
				}
			} else {
				var next_index, prev_index, temp_mwl_img;
				if (navigating === "next") {
					next_index = this.getNextIndex();
					temp_mwl_img = this.getMwlImageByIndex(next_index);
					this.changeLightboxImage(temp_mwl_img, mwl_layout, mwl_theme, navigating);
				}
				if (navigating === "previous") {
					prev_index = this.getPrevIndex();
					temp_mwl_img = this.getMwlImageByIndex(prev_index);
					this.changeLightboxImage(temp_mwl_img, mwl_layout, mwl_theme, navigating);
				}
			}
		};

		/**
		 * Open the lightbox
		 * @param {[[MwlImage]]} mwl_image    [[image to display]]
		 * @param {[[string]]} mwl_layout [[lightbox template]]
		 */
		this.showLightbox = function (mwl_image, mwl_layout, mwl_theme) {
			if (mwl_image) {
				getTemplate(mwl_image, mwl_layout, mwl_theme, 2);
				current_image_index = mwl_image.index;
				var next_image_index = this.getNextIndex();
				var next_image = this.getMwlImageByIndex(next_image_index);
				if(next_image.exists) {
					preloadImage([next_image.img_src]);	
				}
			}
		};

		/**
		 * Close the lightbox
		 */
		this.closeLightbox = function () {
			$('.mwl-container').addClass('invisible');
		};

	};

});
