/**
 * @copyright	Copyright (C) 2012 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Accordeon CK
 * @license		GNU/GPL
 * */

(function($) {

	//define the defaults for the plugin and how to call it
	$.fn.accordeonmenuck = function(options) {
		//set default options
		var defaults = {
			eventtype: 'click',
			fadetransition: false, // pas encore implemente
			transition: 'linear',
			duree: 500,
			imageplus: 'modules/mod_accordeonck/assets/plus.png',
			imageminus: 'modules/mod_accordeonck/assets/minus.png',
			// menuID : 'accordeonck',
			defaultopenedid: '0',
			showactive: true
		};

		//call in the default otions
		var opts = $.extend(defaults, options);
		var menu = this;

		//act upon the element that is passed into the design
		return menu.each(function(options) {

			var fxtransition = defaults.fxtransition;
			accordeonmenuInit();

			function accordeonmenuInit() {
				$(".parent > ul", menu).hide();
				if (opts.showactive) {
					$(".parent.active > ul", menu).show().parent().addClass("open");
					$(".parent.active > img.toggler", menu).attr('src', opts.imageminus);
				}
				if (opts.defaultopenedid && !$(".active", menu).length) {
					$(".item-"+opts.defaultopenedid+" > ul", menu).show().parent().addClass("open");
					$(".item-"+opts.defaultopenedid+" > img.toggler", menu).attr('src', opts.imageminus);
				}
				if (opts.eventtype == 'click') {
					$("li.parent > .toggler", menu).click(function() {
						togglemenu($(this));
					});
				} else {
					$("li.parent > .toggler", menu).mouseenter(function() {
						togglemenu($(this));
					});
				}
			}

			function togglemenu(link) {
				ck_content = link.parent();
				if (!link.parent().hasClass("open")) {
					$(".parent > ul", ck_content.parent()).slideUp({
						duration: opts.duree,
						easing: opts.transition,
						complete: function() {
							$(".parent", ck_content.parent()).removeClass("open");
							$(".parent > img.toggler", ck_content.parent()).attr('src', opts.imageplus);
							if (link.get(0).tagName.toLowerCase() == 'img')
								link.attr('src', opts.imageplus);
						}
					});
					link.nextAll("ul").slideDown({
						duration: opts.duree,
						easing: opts.transition,
						complete: function() {
							link.parent().addClass("open");
							if (link.get(0).tagName.toLowerCase() == 'img')
								link.attr('src', opts.imageminus);
						}
					});
				} else {
					link.nextAll("ul").slideUp({
						duration: opts.duree,
						easing: opts.transition,
						complete: function() {
							link.parent().removeClass("open");
							if (link.get(0).tagName.toLowerCase() == 'img')
								link.attr('src', opts.imageplus);
						}
					});
				}
			}
		});
	};
})(jQuery);