/*hoverItent.js*/
;(function(e){e.fn.hoverIntent=function(t,n,r){var i={interval:100,sensitivity:7,timeout:0};if(typeof t==="object"){i=e.extend(i,t)}else if(e.isFunction(n)){i=e.extend(i,{over:t,out:n,selector:r})}else{i=e.extend(i,{over:t,out:t,selector:n})}var s,o,u,a;var f=function(e){s=e.pageX;o=e.pageY};var l=function(t,n){n.hoverIntent_t=clearTimeout(n.hoverIntent_t);if(Math.abs(u-s)+Math.abs(a-o)<i.sensitivity){e(n).off("mousemove.hoverIntent",f);n.hoverIntent_s=1;return i.over.apply(n,[t])}else{u=s;a=o;n.hoverIntent_t=setTimeout(function(){l(t,n)},i.interval)}};var c=function(e,t){t.hoverIntent_t=clearTimeout(t.hoverIntent_t);t.hoverIntent_s=0;return i.out.apply(t,[e])};var h=function(t){var n=jQuery.extend({},t);var r=this;if(r.hoverIntent_t){r.hoverIntent_t=clearTimeout(r.hoverIntent_t)}if(t.type=="mouseenter"){u=n.pageX;a=n.pageY;e(r).on("mousemove.hoverIntent",f);if(r.hoverIntent_s!=1){r.hoverIntent_t=setTimeout(function(){l(n,r)},i.interval)}}else{e(r).off("mousemove.hoverIntent",f);if(r.hoverIntent_s==1){r.hoverIntent_t=setTimeout(function(){c(n,r)},i.timeout)}}};return this.on({"mouseenter.hoverIntent":h,"mouseleave.hoverIntent":h},i.selector)}})(jQuery);

/*
 * DC Mega Menu - jQuery mega menu
 * Copyright (c) 2011 Design Chemical
 *
 */
(function($){

	//define the defaults for the plugin and how to call it	
	$.fn.dcMegaMenu = function(options){
		//set default options  
		var defaults = {
			classParent: 'dc-mega',
			classContainer: 'sub-container',
			classSubParent: 'mega-hdr',
			classSubLink: 'mega-hdr',
			classWidget: 'dc-extra',
			rowItems: 3,
			speed: 'fast',
			effect: 'fade',
			event: 'hover',
			fullWidth: false,
			onLoad : function(){},
            beforeOpen : function(){},
			beforeClose: function(){}
		};

		//call in the default otions
		var options = $.extend(defaults, options);
		var $dcMegaMenuObj = this;

		//act upon the element that is passed into the design    
		return $dcMegaMenuObj.each(function(options){

			var clSubParent = defaults.classSubParent;
			var clSubLink = defaults.classSubLink;
			var clParent = defaults.classParent;
			var clContainer = defaults.classContainer;
			var clWidget = defaults.classWidget;
			
			megaSetup();
			
			function megaOver(){
				var subNav = $('.sub',this);
				$(this).addClass('mega-hover');
				if(defaults.effect == 'fade'){
					$(subNav).fadeIn(defaults.speed);
				}
				if(defaults.effect == 'slide'){
					$(subNav).slideToggle(defaults.speed, 'easeInOutExpo');// .show(defaults.speed);
				}
				// beforeOpen callback;
				defaults.beforeOpen.call(this);
			}
			function megaAction(obj){
				var subNav = $('.sub',obj);
				$(obj).addClass('mega-hover');
				if(defaults.effect == 'fade'){
					$(subNav).fadeIn(defaults.speed);
				}
				if(defaults.effect == 'slide'){
					$(subNav).show(defaults.speed);
				}
				// beforeOpen callback;
				defaults.beforeOpen.call(this);
			}
			function megaOut(){
				var subNav = $('.sub',this);
				$(this).removeClass('mega-hover');
				//$(subNav).hide();
				$(subNav).slideToggle(defaults.speed, 'easeInOutExpo');// .show(defaults.speed);
				// beforeClose callback;
				defaults.beforeClose.call(this);
			}
			function megaActionClose(obj){
				var subNav = $('.sub',obj);
				$(obj).removeClass('mega-hover');
				//$(subNav).hide();
				$(subNav).slideToggle(defaults.speed, 'easeInOutExpo');// .show(defaults.speed);
				// beforeClose callback;
				defaults.beforeClose.call(this);
			}
			function megaReset(){
				$('li',$dcMegaMenuObj).removeClass('mega-hover');
				$('.sub',$dcMegaMenuObj).hide();
			}

			function megaSetup(){
				$arrow = '<span class="dc-mega-icon"></span>';
				var clParentLi = clParent+'-li';
				var menuWidth = $dcMegaMenuObj.outerWidth();
				$('> li',$dcMegaMenuObj).each(function(){
					//Set Width of sub
					var $mainSub = $('> ul',this);
					var $primaryLink = $('> a',this);
					if($mainSub.length){
						$primaryLink.addClass(clParent).append($arrow);
						$mainSub.addClass('sub').wrap('<div class="'+clContainer+'" />');
						var pos = $(this).position();
						if(headerType.menu_align === 'right') {
							pl = pos.left;//-20;
						}else{
							if($mainSub.hasClass('mega-menu-html-shortcode')) {
								pl = pos.left-13;
							}else{
								pl = pos.left-13;//+10;
							}
						}

						if($('ul',$mainSub).length){
							$(this).addClass(clParentLi);
							$('.'+clContainer,this).addClass('mega');
							
							$('> li',$mainSub).each(function(){
								if(!$(this).hasClass(clWidget)){
									$(this).addClass('mega-unit');
									if($('> ul',this).length){
										$(this).addClass(clSubParent);
										$('> a',this).addClass(clSubParent+'-a');
									} else {
										$(this).addClass(clSubLink);
										$('> a',this).addClass(clSubLink+'-a');
									}
								}
							});

							// Create Rows
							var hdrs = $('.mega-unit',this);
							rowSize = parseInt(defaults.rowItems);
							for(var i = 0; i < hdrs.length; i+=rowSize){
								hdrs.slice(i, i+rowSize).wrapAll('<div class="row" />');
							}

							// Get Sub Dimensions & Set Row Height
							$mainSub.show();
							
							// Get Position of Parent Item
							var pw = $(this).width();
							var pr = pl + pw;
							
							// Check available right margin
							var mr = menuWidth - pr;

							// // Calc Width of Sub Menu
							var subw = $mainSub.outerWidth();
							var totw = $mainSub.parent('.'+clContainer).outerWidth();
							var cpad = totw - subw;
							
							if(defaults.fullWidth == true){
								var fw = menuWidth - cpad;
								$mainSub.parent('.'+clContainer).css({width: fw+'px'});
								$dcMegaMenuObj.addClass('full-width');
							}
							var iw = $('.mega-unit',$mainSub).outerWidth(true);
							var rowItems = $('.row:eq(0) .mega-unit',$mainSub).length;
							var inneriw = iw * rowItems;
							var totiw = inneriw + cpad;
							
							// Set mega header height
							$('.row',this).each(function(){
								$('.mega-unit:last',this).addClass('last');
								var maxValue = undefined;
								$('.mega-unit > a',this).each(function(){
									var val = parseInt($(this).height());
									if (maxValue === undefined || maxValue < val){
										maxValue = val;
									}
								});
								$('.mega-unit > a',this).css('height',maxValue+'px');
								$(this).css('width',inneriw+'px');
							});
							
							// Calc Required Left Margin incl additional required for right align
							
							if(defaults.fullWidth == true){
								params = {left: 0};
							} else {
								
								var _menu_right = (jQuery('#nav-primary').width() + jQuery('#nav-primary').offset().left);

								var ml = mr < ml ? ml + ml - mr : (totiw - pw)/2;
								var subLeft = pl - ml;

								//console.log(mr  + '***' + ml + '***' + pl + '***' + subLeft + "***" +  (pl + subLeft) + "***" + _menu_right );

								// If Left Position Is Negative Set To Left Margin
								var params = {left: pl+'px', marginLeft: -ml+'px'};
								if((pl + subLeft) > _menu_right) {
									params = {right: 0};
								}else{
									if(subLeft < 0){
										params = {left: 0};
									}else if(mr < ml){
										params = {right: 0};
									}
								}
							}
							$('.'+clContainer,this).css(params);
							
							// Calculate Row Height
							$('.row',$mainSub).each(function(){
								var rh = $(this).height();
								//$('.mega-unit',this).css({height: rh+'px'}); //ozy deactivated. causing issues if data added later into dropdown. no side affect appeared.
								$(this).parent('.row').css({height: rh+'px'});
							});
							$mainSub.hide();
					
						} else {
							$('.'+clContainer,this).addClass('non-mega').css('left',pl+'px');
						}
					}
				});
				// Set position of mega dropdown to bottom of main menu
				var menuHeight = $('> li > a',$dcMegaMenuObj).outerHeight(true);
				$('.'+clContainer,$dcMegaMenuObj).css({top: (menuHeight+1)+'px'}).css('z-index','1000');
				
				if(defaults.event == 'hover'){
					// HoverIntent Configuration
					var config = {
						sensitivity: 1,
						interval: 50,
						over: megaOver,
						timeout: 200,
						out: megaOut
					};
					$('li',$dcMegaMenuObj).hoverIntent(config);
				}
				
				if(defaults.event == 'click'){
				
					$('body').mouseup(function(e){
						if(!$(e.target).parents('.mega-hover').length){
							megaReset();
						}
					});

					$('> li > a.'+clParent,$dcMegaMenuObj).click(function(e){
						var $parentLi = $(this).parent();
						if($parentLi.hasClass('mega-hover')){
							megaActionClose($parentLi);
						} else {
							megaAction($parentLi);
						}
						e.preventDefault();
					});
				}
				
				// onLoad callback;
				defaults.onLoad.call(this);
			}
		});
	};
})(jQuery);