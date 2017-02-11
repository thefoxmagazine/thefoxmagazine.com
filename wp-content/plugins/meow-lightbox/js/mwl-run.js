/*global $, jQuery, alert, MwlController, mwl, console */

jQuery(document).ready(function ($) {
	"use strict";
	
	var settings = {
			layout: mwl.settings.layout,
			selector: mwl.settings.selector,
			display_missing_images: false,
			theme: mwl.settings.theme
		},
		mwlController = new MwlController(settings);

	mwlController.init();

	$(document).on('click', '.mwl-img', function (e) {
		e.preventDefault();
		var image_index = $(this).attr('mwl-index'),
			mwl_img = mwlController.getMwlImageByIndex(image_index);
		mwlController.showLightbox(mwl_img, settings.layout, settings.theme);
	});

	$(document).on('click', '.mwl-container:not(invisible)', function (e) {
		// Except if it's a nav link
		if ($(e.target).is($('.control-nav')) || $(e.target).is($('.control-layout')) || $(e.target).is($('i'))) {
			var prev_index, prev_image, next_index, next_image;
			if ($(e.target).is($('.control-close')) || $(e.target).is($('.control-close i'))) {
				mwlController.closeLightbox();
			}
			if ($(e.target).is($('.control-previous')) || $(e.target).is($('.control-previous i'))) {
				prev_index = mwlController.getPrevIndex();
				prev_image = mwlController.getMwlImageByIndex(prev_index);
				mwlController.changeLightboxImage(prev_image, settings.layout, settings.theme, "previous");
			}
			if ($(e.target).is($('.control-next')) || $(e.target).is($('.control-next i'))) {
				next_index = mwlController.getNextIndex();
				next_image = mwlController.getMwlImageByIndex(next_index);
				mwlController.changeLightboxImage(next_image, settings.layout, settings.theme, "next");
			}
		} else {
			mwlController.closeLightbox();
		}
	});

	/*
	$(document).swipe({
		//Generic swipe handler for all directions
		swipe: function (event, direction, distance) {
			if(!$('.mwl-container').hasClass('invisible')) {
				var prev_index, prev_image, next_index, next_image;
				if (direction === 'right' && distance > 50) {
					prev_index = mwlController.getPrevIndex();
					prev_image = mwlController.getMwlImageByIndex(prev_index);
					mwlController.changeLightboxImage(prev_image, settings.layout, settings.theme, "previous");
				} else if (direction === 'left' && distance > 50) {
					next_index = mwlController.getNextIndex();
					next_image = mwlController.getMwlImageByIndex(next_index);
					mwlController.changeLightboxImage(next_image, settings.layout, settings.theme, "next");
				} else {
					mwlController.closeLightbox();
				}	
			}
		},
		threshold: 0
	});
	*/
	
	$(document).keydown(function (e) {
		if (!$('.mwl-container').hasClass('invisible')) {
			var prev_index, prev_image, next_index, next_image;
			switch (e.which) {
			case 37: // left
				prev_index = mwlController.getPrevIndex();
				prev_image = mwlController.getMwlImageByIndex(prev_index);
				mwlController.changeLightboxImage(prev_image, settings.layout, settings.theme, "previous");
				break;

			case 39: // right
				next_index = mwlController.getNextIndex();
				next_image = mwlController.getMwlImageByIndex(next_index);
				mwlController.changeLightboxImage(next_image, settings.layout, settings.theme, "next");
				break;

			case 27:
				mwlController.closeLightbox();
				break;

			default:
				return; // exit this handler for other keys
			}
			e.preventDefault(); // prevent the default action (scroll / move caret)
		}
	});

	$(window).on('resize', function () {
		mwlController.resizeImg();
	});

});
