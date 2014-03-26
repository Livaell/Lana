/**
* Main script file
* @package News Show Pro GK4
* @Copyright (C) 2009-2012 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.4 $
**/
window.addEvent("load", function(){	
	$$('.nspMain').each(function(module){	
		if(!module.hasClass('activated')) {
			module.addClass('activated');	
			var $G = $Gavick[module.getProperty('id')];
			var arts_actual = 0;
			var list_actual = 0;
			var arts_block_width = 0;
			var links_block_width = 0;
			var arts = module.getElements('.nspArt');
			var links = (module.getElement('.nspLinkScroll1')) ? module.getElement('.nspLinkScroll1').getElements('li') : [];
			var nspArtWidth = module.getElement('.nspArt') ? arts[0].getStyle('width') : null;
			var arts_per_page = $G['news_column'] * $G['news_rows'];
			var pages_amount = Math.ceil(arts.length / arts_per_page);
			var links_pages_amount = Math.ceil(Math.ceil(links.length / $G['links_amount']) / $G['links_columns_amount']);
			var hover_anim = module.hasClass('hover');
			var animation = true;
			var art_scroller;
			var link_scroller;
			var direction = module.getProperty('data-direction') == 'rtl' ? 'margin-right' : 'margin-left';
			
			var modInterface = { 
				top: module.getElement('.nspTopInterface'), 
				bottom: module.getElement('.nspBotInterface')
			};
			// arts
			if(arts.length > 0){
				arts_block_width = 100;
				
				art_scroller = new Fx.Tween(
					module.getElement('.nspArtScroll2'), 
					{
						duration:$G['animation_speed'], 
						wait:false, 
						property: direction, 
						unit: '%',
						transition: $G['animation_function']
					}
				);
			}
			
			// links
			if(links.length > 0){
				links_block_width = 100;
				
				link_scroller = new Fx.Tween(
					module.getElement('.nspLinkScroll2'), 
					{
						duration:$G['animation_speed'], 
						wait:false, 
						property: direction,
						unit: '%',
						transition: $G['animation_function']
					}
				);
			}
			
			// top interface
			nsp_art_list(0, module, modInterface.top, pages_amount);
			nsp_art_list(0, module, modInterface.bottom, links_pages_amount);
			if(modInterface.top && modInterface.top.getElement('.nspPagination')){
				modInterface.top.getElement('.nspPagination').getElements('li').each(function(item,i){
					item.addEvent(hover_anim ? 'mouseenter' : 'click', function(){
						art_scroller.start(-1 * i * arts_block_width);
						arts_actual = i;
						
						nsp_art_list(i, module, modInterface.top, pages_amount);
						animation = false;
						(function(){animation = true;}).delay($G['animation_interval'] * 0.8);
					});	
				});
			}
			if(modInterface.top && modInterface.top.getElement('.nspPrev')){
				modInterface.top.getElement('.nspPrev').addEvent("click", function(){
					if(arts_actual == 0) arts_actual = pages_amount - 1;
					else arts_actual--;
					art_scroller.start(-1 * arts_actual * arts_block_width);
					nsp_art_list(arts_actual, module, modInterface.top, pages_amount);
					animation = false;
					(function(){animation = true;}).delay($G['animation_interval'] * 0.8);
				});
				modInterface.top.getElement('.nspNext').addEvent("click", function(){
					if(arts_actual == pages_amount - 1) arts_actual = 0;
					else arts_actual++;
					art_scroller.start(-1 * arts_actual * arts_block_width);
					nsp_art_list(arts_actual, module, modInterface.top, pages_amount);
					animation = false;
					(function(){animation = true;}).delay($G['animation_interval'] * 0.8);
				});
			}
			// bottom interface
			if(modInterface.bottom && modInterface.bottom.getElement('.nspPagination')){
				modInterface.bottom.getElement('.nspPagination').getElements('li').each(function(item,i){
					item.addEvent(hover_anim ? 'mouseenter' : 'click', function(){
						link_scroller.start(-1 * i * links_block_width);
						list_actual = i;
						
						nsp_art_list(i, module, modInterface.bottom, links_pages_amount);
					});	
				});
			}
			if(modInterface.bottom && modInterface.bottom.getElement('.nspPrev')){
				modInterface.bottom.getElement('.nspPrev').addEvent("click", function(){
					if(list_actual == 0) list_actual = links_pages_amount - 1;
					else list_actual--;
					
					link_scroller.start(-1 * list_actual * links_block_width);
										
					nsp_art_list(list_actual, module, modInterface.bottom, links_pages_amount);
				});
				
				modInterface.bottom.getElement('.nspNext').addEvent("click", function(){
					if(list_actual == links_pages_amount - 1) list_actual = 0;
					else list_actual++;
					link_scroller.start(-1 * list_actual * links_block_width);
					
					nsp_art_list(list_actual, module, modInterface.bottom, links_pages_amount);
				});
			}
			if(module.hasClass('autoanim')){
				(function(){
					if(modInterface.top && modInterface.top.getElement('.nspNext')){
						if(animation) modInterface.top.getElement('.nspNext').fireEvent("click");
					}else{
						if(arts_actual == pages_amount - 1) arts_actual = 0;
						else arts_actual++;
						
						art_scroller.start(-1 * arts_actual * arts_block_width);
						
						nsp_art_list(arts_actual, module, modInterface.top, pages_amount);
					}
				}).periodical($G['animation_interval']);
			}
		}
	});
	function nsp_art_list(i, module, position, num){
		if(position && position.getElement('.nspPagination')){
			var pagination = position.getElement('.nspPagination');
			pagination.getElements('li').setProperty('class', '');
			pagination.getElements('li')[i].setProperty('class', 'active');
		}
		if(position && position.getElement('.nspCounter')){
			position.getElement('.nspCounter').getElement('span').innerHTML =  (i+1) + ' / ' + num;
		}
	}
});