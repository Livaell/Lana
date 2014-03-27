/**
 * @version    $Id: jquery.imageshow.js 16583 2012-10-01 11:10:07Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
(function($){ 
	$.extend({
		JSNISThemeSlider: {
			
			ops:{},
			
			initialize : function (options)
			{
				$.extend(options, $.JSNISThemeSlider.ops);
			},
			
			visual: function() {
				this.addEvent2AllVisualElements('imagePanel');
				this.addEvent2AllVisualElements('informationPanel');
				this.addEvent2AllVisualElements('toolbarPanel');
				this.addEvent2AllVisualElements('slideshowPanel');
				this.addEvent2AllVisualElements('thumbnailPanel');
			},
			
			addEvent2AllVisualElements: function(elementClass) 
			{		
				var self = $.JSNISThemeSlider;
				$('.' + elementClass).each(function(index, element)
				{
					var el 		= $(element);
					var event 	= 'change';
					
					if (el.attr('type') != undefined && el.attr('type') == 'radio') event = 'click';
				
					el.unbind(event).bind(event, function(el)
					{	
						self.changeValueVisualElement(elementClass, element);
					});
					
					self.changeValueVisualElement(elementClass, element);
					
				});
			},
			
			changeValueVisualElement: function(panel, element)
			{
				var self = $.JSNISThemeSlider;
				var el = $(element);
				var name = el.attr('name');
				
				if (el.attr('type') != undefined && el.attr('type') == 'radio')
				{
					if(el.attr("checked") != undefined && el.attr("checked") == 'checked')
					{
						var value = el.val();
					}
				}
				else
				{
					var value = el.val();
				}
				
				var obj = {name : name, value : value};
				
				self.changeVisualCaption(obj);
				self.changeVisualThumbnail(obj);
				self.changeVisualToolbar(obj);
			},
			
			changeVisualThumbnail: function(obj) {
				var pagination 			= $('.paginations');
				var container  			= $('.jsn-slider-preview-wrapper');
				var slideDot  			= $('.info_slide_dots');
				var slideNumber  		= $('.info_slide');
				var imageNumberSelect 	= $('.image_number_select');
				var name				= obj.name;
				var value				= obj.value;
				
				if (name == 'thumbnail_panel_presentation')
				{
					
					if (value == 'hide')
					{
						pagination.css('display', 'none');
						container.css('height', '340px');
					}
					else
					{
						pagination.css('display', 'block');
					}	
				}
				else if (name == 'thumbnail_presentation_mode')
				{
					if (value == 'dots')
					{
						slideNumber.css('display', 'none');
						slideDot.css('display', 'block');
					}
					else if (value == 'numbers')
					{
						slideDot.css('display', 'none');
						slideNumber.css('display', 'block');				
					}			
				}	
				else if (name == 'thumnail_panel_position')
				{
					if (value == 'left')
					{
						slideDot.css('right','')
						slideDot.css('left', 15);
						slideNumber.css('right', '');
						slideNumber.css('left', 15);
					}	
					else if (value == 'right')
					{
						slideDot.css('left', '');
						slideDot.css('right', 15);
						slideNumber.css('left', '');
						slideNumber.css('right', 15);
					}	
					else if (value == 'center')	
					{
						slideDot.css('right', '');
						slideDot.css('left', '27%');
						slideNumber.css('right', '');
						slideNumber.css('left', '27%');
					}	
				}
				else if (name == 'thumbnail_active_state_color')
				{
					imageNumberSelect.css('background-color', obj.value);
				}
			},
			
			changeVisualToolbar: function(obj) 
			{
				var arrow 			= $('.slider-slide-arrow');
				var sliderControl 	= $('.slider-control');	
				var name			= obj.name;
				var value			= obj.value;			
				if (name == 'toolbar_navigation_arrows_presentation')
				{
					if (value == 'hide')
					{
						arrow.css('display', 'none');
					}
					else
					{
						arrow.css('display', 'block');
					}	
				}	
				else if (name == 'toolbar_slideshow_player_presentation')
				{
					if (value == 'hide')
					{
						sliderControl.css('display', 'none');
					}
					else
					{
						sliderControl.css('display', 'block');
					}	
				}		
			},
			
			changeVisualCaption: function(obj)
			{
				var self 			= $.JSNISThemeSlider;
				var captionTab 		= $('#themeslider-caption-tab');
				var caption 		= $('.slider-caption');
				var title		 	= $('.slider-title');
				var description 	= $('.slider-description');
				var alink 			= $('.slider-a-link');
				var plink 			= $('.slider-link');
				var slideDot  			= $('.info_slide_dots');
				var slideNumber  		= $('.info_slide');				
				var name			= obj.name;
				var value			= obj.value;
				
				if (name == 'caption_show_caption')
				{				
					if (value == 'show')
					{
						caption.css('display', 'block');
						var postion = $('#caption_position').val();
						if (postion == 'top')
						{
							caption.addClass('top').removeClass('bottom');
							slideNumber.addClass('bottom').removeClass('top');
							slideDot.addClass('bottom').removeClass('top');
						}
						else
						{
							caption.addClass('bottom').removeClass('top');
							slideNumber.addClass('top').removeClass('bottom');
							slideDot.addClass('top').removeClass('bottom');						
						}							
					}
					else
					{
						caption.css('display', 'none');
						slideNumber.addClass('top').removeClass('bottom');
						slideDot.addClass('top').removeClass('bottom');
						captionTab.find('div.control-group').each(function(index, el) {
							if (index)
							{
								$(el).css('display', 'none');
							}	
						});							
					}						
				}
				else if (name == 'caption_title_css') 
				{		
					if ($('input[name="caption_title_show"]:checked').val() == 'yes')
					{
						title.css('display', 'block');
					}
					else
					{
						title.css('display', 'none');
					}
					
					var objCsstitle = self.parserCss(value);
					title.css(objCsstitle);
				}
				else if (name == 'caption_description_css') 
				{			
					if ($('input[name="caption_description_show"]:checked').val() == 'yes')
					{
						description.css('display', 'block');
					}
					else
					{
						description.css('display', 'none');
					}			
					var objCssDescription = self.parserCss(value);
					description.css(objCssDescription);
				}
				else if (name == 'caption_link_css') 
				{			
					if ($('input[name="caption_link_show"]:checked').val() == 'yes')
					{
						plink.css('display', 'block');
					}
					else
					{
						plink.css('display', 'none');
					}			
					var objCssLink = self.parserCss(value);
					alink.css(objCssLink);
					plink.css(objCssLink);
				}
				else if (name == 'caption_caption_opacity')
				{
					var opacity = value*0.01;
					caption.css('opacity', opacity);
				}
				else if (name == 'caption_title_show')
				{
					if (value == 'yes')
					{
						title.css('display', 'block');							
					}
					else
					{
						title.css('display', 'none');
					}			
				}
				else if (name == 'caption_description_show')
				{
					if (value == 'yes')
					{
						description.css('display', 'block');
					}
					else
					{
						description.css('display', 'none');
					}			
				}	
				else if (name == 'caption_link_show')
				{
					if (value == 'yes')
					{
						plink.css('display', 'block');
					}
					else
					{
						plink.css('display', 'none');
					}			
				}	
				else if (name == 'caption_position')
				{
					var show_caption = $('#caption_show_caption').val();
					if (show_caption == 'show')
					{	
						if (value == 'top')
						{
							caption.addClass('top').removeClass('bottom');
							slideNumber.addClass('bottom').removeClass('top');
							slideDot.addClass('bottom').removeClass('top');
						}
						else
						{
							caption.addClass('bottom').removeClass('top');
							slideNumber.addClass('top').removeClass('bottom');
							slideDot.addClass('top').removeClass('bottom');						
						}	
					}
				}	
			},
			
			trim: function(str, chars) 
			{
				var self =  $.JSNISThemeSlider;
				return self.ltrim(self.rtrim(str, chars), chars);
			},
			
			ltrim: function(str, chars) 
			{
				chars = chars || "\\s";
				return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
			},
			
			rtrim: function(str, chars) 
			{
				chars = chars || "\\s";
				return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
			},	
			
			parserCss: function(str) 
			{
				var self 	=  $.JSNISThemeSlider;
				objCss 		= {};
				var css 	= str.split(';');
				var length 	= css.length;
				var index  	= 0;
				for (var i 	= 0; i < length; i++)
				{
					var value = css[i].replace(/(\r\n|\n|\r)/gm,"");
					if (value != '')
					{	
						var tmpCss = value.split(':');
						objCss [self.trim(tmpCss[0], " ")] = self.trim(tmpCss[1], " ");
						index++;
					}
				}
				
				return objCss;
			},
			
			toogleTab: function(index)
			{
				var el = $('#jsn-is-themeslider')
				el.tabs({'selected':index});
			},
			
			toggleCaptionOptions: function(el)
			{
				var value 		= $('option:selected', $(el)).val();
				var captionTab 	= $('#themeslider-caption-tab');
				if (value == 'hide') 
				{				
					captionTab.find('div.control-group').each(function(index, el) {
						if (index)
						{
							$(el).css('display', 'none');
						}	
					});
				}
				else 
				{
					captionTab.find('div.control-group').each(function(index, el) {
						if (index)
						{
							$(el).css('display', '');
						}	
					});
				}
				
			},
			
			toggleThumbnailOptions: function(el)
			{
				var value 		= $('option:selected', $(el)).val();
				var captionTab 	= $('#themeslider-thumbnail-tab');
				
				if (value == 'hide') 
				{				
					captionTab.find('div.control-group').each(function(index, el) {
						if (index)
						{
							$(el).css('display', 'none');
						}	
					});
				}
				else 
				{
					captionTab.find('div.control-group').each(function(index, el) {
						if (index)
						{
							$(el).css('display', '');
						}	
					});	
				}
				
				
				$('#click_action').parent().parent().css('display', '');
				if ($('#click_action').val() == 'open_image_link')
				{
					$('#jsn-open-link-in').css('display', 'block');
				}	
				else
				{
					$('#jsn-open-link-in').css('display', 'none');
				}				
			}			
			
		}
	});
})(jQuery);