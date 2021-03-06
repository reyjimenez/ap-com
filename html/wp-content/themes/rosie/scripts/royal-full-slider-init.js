$full_royal_slider = null;
jQuery(window).load(function() {
	if(jQuery('#royal-classic-full').length>0) {
		jQuery('#royal-classic-full').imagesLoaded(function() {	
			fix_height_to_fit_slider();
			$full_royal_slider = jQuery('#royal-classic-full').royalSlider({ 
				transitionSpeed: 800,
				slidesSpacing: 0,
				arrowsNavAutoHide: false,
				imageScaleMode: 'fill',
				imageAlignCenter:false,
				blockLoop: true,
				loop: false,
				loopRewind: true,
				numImagesToPreload: 3,
				keyboardNavEnabled: true,
				block: {
					delay: 400
				},
				autoPlay: {
					enabled: true,
					pauseOnHover: true,
					delay: 3300
				}		
			}).data('royalSlider');
			ozy_royal_counter();
		});
	}

	if(jQuery('#royal-nearby-full').length>0) {
		jQuery('#royal-nearby-full').imagesLoaded(function() {	
			fix_height_to_fit_slider();
			$full_royal_slider = jQuery('#royal-nearby-full').royalSlider({
				slidesSpacing: 0, 
				addActiveClass: true, 
				arrowsNav: false,	
				controlNavigation: 'none', 
				autoScaleSlider: false, 
				loop: true, 
				fadeinLoadedSlide: true, 
				globalCaption: true, 
				keyboardNavEnabled: true, 
				globalCaptionInside: false,
				visibleNearby: {
					enabled: true, 
					centerArea: 0.5,	
					center: true, 
					breakpoint: 650, 
					breakpointCenterArea: 0.64, 
					navigateByCenterClick: true
				},
				autoPlay: {
					enabled: true, 
					pauseOnHover: true, 
					delay: 1300
				}	
			}).data('royalSlider');
			ozy_royal_counter();
		});	
	}
	
	if(jQuery('#royal-classic-thumbnail-full').length>0) {
		jQuery('#royal-classic-thumbnail-full').imagesLoaded(function() {
			fix_height_to_fit_slider();
			$full_royal_slider = jQuery('#royal-classic-thumbnail-full').royalSlider({
				arrowsNav: true,
				numImagesToPreload: 3,
				keyboardNavEnabled: true,
				/*controlsInside: false,*/
				imageScalePadding: 40,
				imageScaleMode: 'fit-if-smaller',
				arrowsNavAutoHide: false,
				autoScaleSlider: false,
				autoHeight: false,
				usePreloader: true,
				controlNavigation: 'thumbnails',
				thumbs: {
					arrows: false,
					spacing: 0,
					firstMargin: false,
					autoCenter:	true,
					fitInViewport: true
				},
				navigateByClick: true,
				startSlideId: 0,
				autoPlay: {
					enabled: false,
					pauseOnHover: false,
					delay: 6000
				},
				transitionType: 'move',
				globalCaption: false,
				loop: true,
				slidesSpacing: 0,
				fullscreen: {
					enabled: false,
					nativeFS: true
				}
			}).data('royalSlider');
			ozy_royal_counter();
		});
	}
	
	jQuery(window).resize();
});

function ozy_royal_counter() {
	jQuery('#royal-slider-counter').text(ozy_add_zero($full_royal_slider.currSlideId+1) + "/" + ozy_add_zero($full_royal_slider.numSlides));
	$full_royal_slider.ev.on('rsAfterSlideChange', function(event) {
		jQuery('#royal-slider-counter').text(ozy_add_zero($full_royal_slider.currSlideId+1) + "/" + ozy_add_zero($full_royal_slider.numSlides));
	});	
}

jQuery(window).resize(function() {
	fix_height_to_fit_slider();
	if($full_royal_slider !== null) {
		$full_royal_slider.updateSliderSize(true);
	}
})

function fix_height_to_fit_slider() {
	var h = window.innerHeight - parseInt(jQuery('#main').css('margin-top'));
	if((navigator.userAgent.match(/msie|trident/i))) {
		h = h + 6;
	}
	jQuery('.post-content').height(h);
}

function ozy_add_zero(v) {
	if(parseInt(v)<9) return "0" + v;
	return v;	
}