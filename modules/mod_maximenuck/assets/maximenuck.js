/**
 * @copyright	Copyright (C) 2012 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */

(function($) {

	//define the defaults for the plugin and how to call it
	$.fn.DropdownMaxiMenu = function(options) {
		//set default options
		var defaults = {
			fxtransition: 'linear',
			fxduration: 500,
			menuID: 'maximenuck',
			testoverflow: '0',
			orientation: 'horizontal',
			behavior: 'mouseover',
			opentype: 'open',
			direction: 'normal',
			directionoffset1: '30',
			directionoffset2: '30',
			dureeIn: 0,
			dureeOut: 500,
			ismobile: false,
			menuposition: '0',
			showactivesubitems: '0',
			effecttype: 'dropdown'
		};

		//call in the default otions
		var options = $.extend(defaults, options);
		var maximenuObj = this;

		//act upon the element that is passed into the design
		return maximenuObj.each(function(options) {

			var fxtransition = defaults.fxtransition;
			var fxduration = defaults.fxduration;
			var dureeOut = defaults.dureeOut;
			var dureeIn = defaults.dureeIn;
			// var useOpacity = defaults.useOpacity;
			var menuID = defaults.menuID;
			var orientation = defaults.orientation;
			var behavior = defaults.behavior;
			var opentype = defaults.opentype;
			var fxdirection = defaults.fxdirection;
			var directionoffset1 = defaults.directionoffset1;
			var directionoffset2 = defaults.directionoffset2;
			var ismobile = defaults.ismobile;
			var showactivesubitems = defaults.showactivesubitems;
			var testoverflow = defaults.testoverflow;
			var effecttype = defaults.effecttype;
			var transitiontype = 0;
			var status = new Array();

			maximenuInit();
			if (defaults.menuposition == 'topfixed') {
				var menuy = $(this).offset().top;
				$(window).bind('scroll', function() {
					if ($(window).scrollTop() > menuy) {
						maximenuObj.addClass('maximenufixed');
					} else {
						maximenuObj.removeClass('maximenufixed');
					}
				});
			} else if (defaults.menuposition == 'bottomfixed') {
				$(this).addClass('maximenufixed').find('ul.maximenuck').css('position', 'static');
			}

			function openMaximenuck(el) {
				if ((el.data('status') == 'opened' && opentype != 'open')
						|| (status[el.data('level') - 1] == 'showing' && opentype == 'drop'))
					return;
				if (el.hasClass('fullwidth')) {
					el.submenu.css('left', '0');
				} else {
					el.submenu.css('left', 'auto');
				}
				if (effecttype == 'pushdown') el.submenu.css('position','relative');
				if (opentype != 'noeffect')
					status[el.data('level')] = 'showing';

				switch (opentype) {
					case 'noeffect':
						status[el.data('level')] = '';
						el.data('status', 'opened');
						break;
					case 'slide':
						el.submenu.stop(true, true);
						slideconteneur = $('.maximenuck2', el);
						if (el.hasClass('level1') && orientation == 'horizontal') {
							slideconteneur.css('marginTop', -el.submenuHeight);
							slideconteneur.animate({
								marginTop: 0
							}, {
								duration: fxduration,
								queue: false,
								easing: fxtransition,
								complete: function() {
									status[el.data('level')] = '';
									el.submenu.css('overflow', 'visible');
									el.data('status', 'opened');
								}
							});
							el.submenu.animate({
								height: el.submenuHeight
							}, {
								duration: fxduration,
								queue: false,
								easing: fxtransition,
								complete: function() {
									status[el.data('level')] = '';
									el.submenu.css('overflow', 'visible');
									el.data('status', 'opened');
								}
							});
						} else {
							slideconteneur.css('marginLeft', -el.submenuHeight);
							slideconteneur.animate({
								marginLeft: 0
							}, {
								duration: fxduration,
								queue: false,
								easing: fxtransition,
								complete: function() {
									status[el.data('level')] = '';
									el.submenu.css('overflow', 'visible');
									el.data('status', 'opened');
								}
							});
							el.submenu.animate({
								width: el.submenuWidth
							}, {
								duration: fxduration,
								queue: false,
								easing: fxtransition,
								complete: function() {
									status[el.data('level')] = '';
									el.submenu.css('overflow', 'visible');
									el.data('status', 'opened');
								}
							});
						}
						break;
					case 'show':
						el.submenu.stop(true, true);
						el.submenu.show(fxduration, fxtransition, {
							complete: function() {
								status[el.data('level')] = '';
								el.data('status', 'opened');
							}
						});
						break;
					case 'fade':
						el.submenu.stop(true, true);
						el.submenu.fadeIn(fxduration, fxtransition, {
							complete: function() {
								status[el.data('level')] = '';
								el.data('status', 'opened');
							}
						});
						break;
					case 'scale':
						el.submenu.stop(true, true);
						el.submenu.show("scale", {
							duration: fxduration,
							easing: fxtransition,
							complete: function() {
								status[el.data('level')] = '';
								el.status = 'opened';
							}
						});
						break;
					case 'puff':
						el.submenu.stop(true, true);
						el.submenu.show("puff", {
							duration: fxduration,
							easing: fxtransition,
							complete: function() {
								status[el.data('level')] = '';
								// el.data('status','opened');
							}
						});
						break;
					case 'drop':
						el.submenu.stop(true, true);
						if (el.hasClass('level1') && orientation == 'horizontal') {
							if (fxdirection == 'inverse') {
								dropdirection = 'down';
								el.submenu.css('bottom', directionoffset1 + 'px');
							} else {
								dropdirection = 'up';
							}
						} else {
							if (fxdirection == 'inverse') {
								dropdirection = 'right';
								el.submenu.css('right', directionoffset2 + 'px');
							} else {
								dropdirection = 'left';
							}
						}
						el.submenu.show("drop", {
							direction: dropdirection,
							duration: fxduration,
							easing: fxtransition,
							complete: function() {
								status[el.data('level')] = '';
								// el.data('status','opened');
							}
						});
						break;
					case 'open':
					default:
						el.submenu.stop();
						el.submenu.css('overflow', 'hidden');
						if (el.hasClass('level1') && orientation == 'horizontal') {
							el.submenu.animate({
								height: el.submenuHeight
							}, {
								duration: fxduration,
								queue: false,
								easing: fxtransition,
								complete: function() {
									status[el.data('level')] = '';
									if (effecttype == 'dropdown') el.submenu.css('overflow', 'visible');
									el.data('status', 'opened');
								}
							});
						} else {
							el.submenu.animate({
								width: el.submenuWidth
							}, {
								duration: fxduration,
								queue: false,
								easing: fxtransition,
								complete: function() {
									status[el.data('level')] = '';
									if (effecttype == 'dropdown') el.submenu.css('overflow', 'visible');
									el.data('status', 'opened');
								}
							});
						}
						break;
				}
			}

			function closeMaximenuck(el) {
				el.submenu.stop(true, true);
				status[el.data('level')] = '';

				switch (opentype) {
					case 'noeffect':
						el.submenu.css('left', '-999em');
						el.submenu.css('position','absolute');
						status[el.data('level')] = '';
						el.data('status', 'closed');
						break;
					case 'fade':
						el.submenu.fadeOut(fxduration, fxtransition, {
							complete: function() {
								status[el.data('level')] = '';
								el.data('status', 'closed');
							}
						});
						break;
					case 'slide':
						if (el.hasClass('level1') && orientation == 'horizontal') {
							el.submenu.css('height', '0');
						} else {
							el.submenu.css('width', '0');
						}
						el.submenu.css('left', '-999em');
						el.submenu.css('position','absolute');
						status[el.data('level')] = '';
						el.data('status', 'closed');
						break;
					case 'open':
						el.submenu.stop();
						status[el.data('level')] = '';
						el.submenu.css('overflow', 'hidden');
						if (el.hasClass('level1') && orientation == 'horizontal') {
							el.submenu.css('overflow', 'hidden').animate({
								height: 0
							}, {
								duration: fxduration,
								queue: false,
								easing: fxtransition,
								complete: function() {
									el.submenu.css('left', '-999em');
									el.submenu.css('position','absolute');
									status[el.data('level')] = '';
									el.data('status', 'closed');
								}
							});
						} else {
							el.submenu.css('width', '0');
							el.submenu.css('left', '-999em');
							el.submenu.css('position','absolute');
							status[el.data('level')] = '';
							el.data('status', 'closed');
						}
						break;
					default:
					case 'drop':
						el.submenu.hide(0, {
							complete: function() {
								status[el.data('level')] = '';
								el.data('status', 'closed');
							}
						});
						break;
				}
			}

			function showSubmenuck(el) {
				el.css('z-index', 15000);
				el.submenu.css('z-index', 15000);
				clearTimeout(el.timeout);
				el.timeout = setTimeout(function() {
					openMaximenuck(el);
				}, dureeIn);
			}

			function hideSubmenuck(el) {
				el.css('z-index', 12001);
				el.submenu.css('z-index', 12001);
				clearTimeout(el.timeout);
				el.timeout = setTimeout(function() {
					closeMaximenuck(el);
				}, dureeOut);
			}

			function testOverflowmenuck(el) {
				var pageWidth = $(document.body).outerWidth();
				var elementPosition = el.offset().left + el.outerWidth() + el.submenuWidth;
				if (elementPosition > pageWidth) {
					if ((el.data('level')) == 1) {
						el.submenu.css('right', '0px');
					} else {
						el.submenu.css('right', el.outerWidth());
					}
					el.submenu.css('marginRight', '0px');
					el.submenu.addClass('fixRight');
				}
			}

			function maximenuInit() {
//				var menuWidth = maximenuObj.outerWidth();
				if (effecttype == 'pushdown') {
					$('li.maximenuck.level1', maximenuObj).each(function(i, el) {
						if (!$(el).hasClass('parent')) {
							$(el).mouseenter(function() {
								$('li.maximenuck.level1.parent', maximenuObj).each(function(j, el2) {
									el2 = $(el2);
									if ($(el).prop('class') != el2.prop('class')) {
										el2.submenu = $('> .maxipushdownck > .floatck',maximenuObj).eq(j);
										hideSubmenuck(el2);
									}
								});
							});
						}
					});
					els = $('li.maximenuck.level1.parent', maximenuObj);
				} else {
					els = $('li.maximenuck.parent', maximenuObj);
				}
				els.each(function(i, el) {
					el = $(el);
					// test if dropdown is required
					if (el.hasClass('nodropdown')) {
						return true;
					}
					// manage item level
					if (el.hasClass('level1'))
						el.data('level', 1);
					$('li.maximenuck.parent', el).each(function(j, child) {
						$(child).data('level', el.data('level') + 1);
					});
					// manage submenus
					if (effecttype == 'pushdown') {
						el.submenu = $('> .maxipushdownck > .floatck',maximenuObj).eq(i);
						el.submenu
							.css('width','inherit')
//							.css('margin','0')
//							.css('padding','0')
							//.css('position','relative')
							.css('overflow','hidden');
					} else {
						el.submenu = $('> .floatck', el);
					}
					if (opentype == 'noeffect' || opentype == 'open' || opentype == 'slide') {
						el.submenu.css('left', '-999em');
					} else {
						el.submenu.hide();
					}
					el.submenuHeight = el.submenu.height();
					el.submenuWidth = el.submenu.width();
					if (opentype == 'open' || opentype == 'slide') {
						if (el.hasClass('level1') && orientation == 'horizontal') {
							el.submenu.css('height', '0');
						} else {
							el.submenu.css('width', '0');
						}
					}
					//manage active submenus
					if (showactivesubitems == '1' && el.hasClass('active')) {
						if (el.hasClass('fullwidth')) {
							el.submenu.css('left', '0');
						} else {
							el.submenu.css('left', 'auto');
						}
						el.submenu.css('height', el.submenuHeight);
						el.submenu.show();
					}
					// manage inverse direction
					if (fxdirection == 'inverse' && el.hasClass('level1') && orientation == 'horizontal')
						el.submenu.css('bottom', directionoffset1 + 'px');
					if (fxdirection == 'inverse' && el.hasClass('level1') && orientation == 'vertical')
						el.submenu.css('right', directionoffset1 + 'px');
					if (fxdirection == 'inverse' && !el.hasClass('level1') && orientation == 'vertical')
						el.submenu.css('right', directionoffset2 + 'px');

					if (behavior == 'clickclose') {
						el.mouseenter(function() {
							if (testoverflow == '1')
								testOverflowmenuck(el);
							$('li.maximenuck.parent.level' + el.data('level'), maximenuObj).each(function(j, el2) {
								el2 = $(el2);
								if (el.prop('class') != el2.prop('class')) {
									if (effecttype == 'pushdown') {
										el2.submenu = $('> .maxipushdownck > .floatck',maximenuObj).eq(j);
									} else {
										el2.submenu = $('> .floatck', el2);
									}
									// el2.data('status','closed');
									// status[el2.data('level')] = '';
									hideSubmenuck(el2);
								}
							});
							showSubmenuck(el);
						});

						$('> div > .maxiclose', el).click(function() {
							hideSubmenuck(el);
						});
					} else if (behavior == 'click') {
						if (el.hasClass('parent') && $('> a.maximenuck', el).length) {
							el.redirection = $('> a.maximenuck', el).prop('href');
							$('> a.maximenuck', el).prop('href', 'javascript:void(0)');
							el.hasBeenClicked = false;
						}

						// hide when clicked outside
						// if (ismobile) {
						// document.body.addEvent('click',function(e) {
						// if(element && !e.target || !$(e.target).getParents().contains(element)) {
						// el.hasBeenClicked = false;
						// el.hideMaxiCK();
						// }
						// });
						// }
						$('> a.maximenuck,> span.separator', el).click(function() {
							// event.stopPropagation();
							// set the redirection again for mobile
							// if (el.hasBeenClicked == true && ismobile) {
							// el.getFirst('a.maximenuck').setProperty('href',el.redirection);
							// }
							// el.hasBeenClicked = true;
							if (testoverflow == '1')
								testOverflowmenuck(el);
							if (el.data('status') == 'opened') {
								hideSubmenuck(el);
								$('li.maximenuck.parent', el).each(function(j, el2) {
									el2 = $(el2);
									if (el.prop('class') != el2.prop('class')) {
										if (effecttype == 'pushdown') {
										el2.submenu = $('> .maxipushdownck > .floatck',maximenuObj).eq(j);
									} else {
										el2.submenu = $('> .floatck', el2);
									}
										hideSubmenuck(el2);
									}
								});
							} else {
								$('li.maximenuck.parent.level' + el.data('level'), maximenuObj).each(function(j, el2) {
									el2 = $(el2);
									if (el.prop('class') != el2.prop('class')) {
										if (effecttype == 'pushdown') {
										el2.submenu = $('> .maxipushdownck > .floatck',maximenuObj).eq(j);
									} else {
										el2.submenu = $('> .floatck', el2);
									}
										hideSubmenuck(el2);
									}
								});
								showSubmenuck(el);
							}
						});

					} else {
						el.mouseenter(function() {
							if (effecttype == 'pushdown') {
								$('li.maximenuck.level1.parent', maximenuObj).each(function(j, el2) {
									el2 = $(el2);
									if (el.prop('class') != el2.prop('class')) {
										el2.submenu = $('> .maxipushdownck > .floatck',maximenuObj).eq(j);
										hideSubmenuck(el2);
									}
								});
							} else {
								if (testoverflow == '1')
									testOverflowmenuck(el);
							}
							showSubmenuck(el);
						});
						if (effecttype == 'pushdown') {
							maximenuObj.mouseleave(function() {
								hideSubmenuck(el);
							});
						} else {
							el.mouseleave(function() {
								hideSubmenuck(el);
							});
						}
					}
				});

			}
		});
	};
})(jQuery);

// jQuery(document).ready(function($){
// $('#maximenuck').DropdownMaxiMenu({
// });
// });