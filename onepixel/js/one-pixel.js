(function($) {
	'use strict';
	$(document).ready(function() {
		// @link http://pixiebox.com/code/docs/srcbox
		srcBox.init('.srcbox', {
				breakpoints : json_breakpoints
			, parentOffset: true 
		});

		srcBox.init('.srcbox-header', { 
				breakpoints : json_breakpoints
			, parentOffset : true
		});

		// gallery with masonry layout
		srcBox.init('.srcbox-portfolio', {
				breakpoints : json_breakpoints
			, parentOffset: true 
			, onComplete : function () {
					$('.grid').masonry({
						itemSelector	: '.item'
					});
				}
		});

		// @link http://pixiebox.com/code/docs/dialogbox
		$('a[href*=".png"], a[href*=".gif"], a[href*=".jpg"]').each(function () {
			var el = $(this);

			el.attr('aria-haspopup', 'true')
				.attr('aria-expanded', 'false')
				.attr('data-dialog', '<img src="' + el.attr('href') + '" />')
				.attr('data-rel', 'alert')
				.attr('data-width', '100%');
		});

		/* Thanks to CSS Tricks for pointing out this bit of jQuery
		http://css-tricks.com/equal-height-blocks-in-rows/
		It's been modified into a function called at page load and then each time the page is resized.
		One large modification was to remove the set height before each new calculation. */

		var equalheight = function equalheight(container){
			var currentTallest = 0
				,	currentRowStart = 0
				,	rowDivs = []
				,	currentDiv
				,	$el
				,	topPosition = 0;

			$(container).each(function() {
				$el = $(this);
				$el.height('auto');
				topPosition = $el.position().top;

				if (currentRowStart != topPosition) {
					for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
						rowDivs[currentDiv].height(currentTallest);
					}

					rowDivs.length = 0; // empty the array
					currentRowStart = topPosition;
					currentTallest = $el.height();
					rowDivs.push($el);
				} else {
					rowDivs.push($el);
					currentTallest = currentTallest < $el.height() ? $el.height() : currentTallest;
				}

				for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
					rowDivs[currentDiv].height(currentTallest);
				}
			});
		};

		// underscore debounce function
		var debounce = function debounce(func, wait, immediate) {
			var timeout;

			return function() {
				var context = this
					, args 	= arguments
					, later 	= function() {
							timeout = null;
							if (!immediate) func.apply(context, args);
						}
					, callNow = immediate && !timeout;

				clearTimeout(timeout);
				timeout = setTimeout(later, wait);

				if (callNow) func.apply(context, args);
			};
		};

		var $postContainer = $('.post-container');

		$(window).load(function(){
			$postContainer.each(function(){
				equalheight($(this).find('.post-content'));
			});
		}).resize(debounce(function () {
			$postContainer.each(function(){
				equalheight($(this).find('.post-content'));
			});
		}, 250));

		if ( ('ontouchstart' in window) ) {
			$('html').addClass('mobile');
		} else {
			$('html').addClass('desktop');
		}

		$(document).on('mouseover', 'table', function() {
			$(this).addClass('table-hover');
		}).on('mouseout', 'table', function() {
			$(this).removeClass('table-hover');
		}).on('click', 'table tr', function() {
			$(this).addClass('active');
		});

		function get_the_excerpt( item, len ) {
			// split by whitespace, slice the array, join by whitespace, strip HTML
			return item.post_content.split(/\s+/).slice(0,len).join(' ')
				.replace(/(<([^>]+)>)/ig, '')	+
				'... <a href="' + item.guid + '">' + objectL10n.read_more + '</a>';
		}

		// document.addEventListener is a feature detection for IE9+, ScrollMagic supports IE9+
		if (!document.addEventListener) $('html').addClass('lt9');

		if (!('ontouchstart' in window) && document.addEventListener && $('body').hasClass('home')) {
			var smPath = '//' + window.location.host +
				'/wp-content/themes/onepixel/libs/scrollMagic/';

			window.head.load(
				[
					smPath + 'scrollmagic/minified/ScrollMagic.min.js'
				, smPath + 'js/lib/greensock/TweenMax.min.js'
				, smPath + 'js/lib/greensock/easing/EasePack.min.js'
				, smPath + 'scrollmagic/minified/plugins/animation.gsap.min.js'
				, smPath + 'js/lib/greensock/plugins/ScrollToPlugin.min.js'
				]
			, function () {
					// init controller
					var controller = new ScrollMagic.Controller({
						globalSceneOptions: {
							/*duration		: '100%'
						, */triggerHook	: 'onEnter'
						}
					});

					// default swipe of panels
					var controllerPanels = new ScrollMagic.Controller({
						globalSceneOptions: {
								reverse			: true
							, triggerHook	: 'onLeave'
						}
					});

					// get all panels
					// http://janpaepke.github.io/ScrollMagic/examples/basic/section_wipes_natural.html
					var panels = document.querySelectorAll('body [role="main"] > .sm-panel');

					if (panels.length) {
						// create scene for every slide
						for (var i = 0; i < panels.length; i++) {
							new ScrollMagic.Scene({
									duration				: '100%'
								, triggerElement 	: panels[i]
							})
							.setPin(panels[i])
							.addTo(controllerPanels);
						}

						// Change behaviour of controller
						// to animate scroll instead of jump
						controllerPanels.scrollTo(function(target) {
							TweenMax.to(window, 0.5, {
								ease 			: Cubic.easeInOut
							, scrollTo 	: {
									autoKill	: true // Allow scroll position to change outside itself
								, y 				: target
								}
							});
						});

						$('.menu-item a[href*=#]').on('click', function( event ) {
							var id = $(this).attr('href');

							if ( $(id).length ) {
								event.preventDefault();

								// trigger scroll
								controllerPanels.scrollTo(id);

								// If supported by the browser we can also update the URL
								if ( window.history && window.history.pushState ) {
									history.pushState('', document.title, id);
								}
							}
						});
					}

					// customized direction of swipes
					// http://janpaepke.github.io/ScrollMagic/examples/advanced/section_wipes_manual.html
					var controllerPanelsDirections = new ScrollMagic.Controller()
						, slides = document.querySelectorAll('.pin-container > .sm-panel');

					if (slides.length) {
						// define movement of panels
						var wipeAnimation = new TimelineMax()
							, direction;

						for (i = 0; i < slides.length; i++) {
							direction = slides[i].getAttribute('data-direction');
							switch (direction) {
								case 'ltr':
									wipeAnimation.fromTo(
											slides[i]
										,	1
										,	{x: '-100%'}
										,	{x: '0%', ease: Linear.easeNone}
									); // in from left
									break;
								case 'rtl':
									wipeAnimation.fromTo(
											slides[i]
										,	1
										,	{x:  '100%'}
										,	{x: '0%', ease: Linear.easeNone}
									); // in from right
									break;
								case 'up':
									wipeAnimation.fromTo(
											slides[i]
										,	1
										,	{y: '-100%'}
										,	{y: '0%', ease: Linear.easeNone}
										); // in from top
									break;
							}
						}
						
						// create scene to pin and link animation
						new ScrollMagic.Scene({
								duration				: (slides.length * 100) + '%'
							,	triggerElement	: '.pin-container'
							,	triggerHook			: 'onLeave'
						})
						.setPin('.pin-container')
						.setTween(wipeAnimation)
						.addTo(controllerPanelsDirections);

						// Change behaviour of controller
						// to animate scroll instead of jump
						controllerPanelsDirections.scrollTo(function(target) {
							TweenMax.to(window, 0.5, {
									ease 			: Cubic.easeInOut
								,	scrollTo 	: {
											autoKill	: true // Allow scroll position to change outside itself
										, y 				: target
									}
							});
						});
					}
				}
			);
		}
	});
})(jQuery);