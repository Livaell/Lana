window.addEvent("load", function(){	
    $$('.nspMainPortalMode4').each(function(module){
		var id = module.getProperty('id');
		var $G = $Gavick[id];
		var current_offset = 0;
		var arts = module.getElements('.nspArt');
		var headline_size = module.getElement('.nspArtHeadline').getSize().y;
		var headline_titles = module.getElements('.nspArtHeadline');
		var auto_anim = module.hasClass('autoanim');
		var anim_speed = $G['animation_speed'];
		var anim_interval = $G['animation_interval'];
		var animation = false;
		var scrollWrap = module.getElement('.nspArts');
		var scroller = new Fx.Scroll(scrollWrap, {duration: anim_speed, wheelStops: false});
		var headlines = new Fx.Morph(module.getElement('.nspTextWrap'), {duration: anim_speed, wheelStops: false});
		var dimensions = scrollWrap.getSize();
		var startItem = 0;
		var sizeWrap = scrollWrap.getCoordinates();
		
		module.getElement('.nspArt').addClass('active');
		module.getElement('.nspArtsScroll').setStyle('width', (arts[arts.length-1].getSize().x * arts.length) + 2);
		
		var offset = module.getElement('.nspArt').getSize().x;
		var size = module.getElement('.nspArts').getSize().x;
		var scrollSize = (arts[arts.length-1].getSize().x * arts.length);
		var amountInView = Math.floor(size / offset);
		var totalAmount = module.getElements('.nspArt').length;
		
		// reset
		scroller.start(0,0);
		current_art = amountInView;
		
		if(totalAmount > amountInView) {
		
			if(module.getElement('.nspPrev')) {
				module.getElement('.nspPrev').addEvent('click', function() {
					animation = true;
					
					if(current_offset <= 0) {
						current_offset = scrollSize - size;
					} else {
						current_offset -= offset;
					}
					
					scroller.start(current_offset, 0);
				});
			}
			
			if(module.getElement('.nspNext')) {
				module.getElement('.nspNext').addEvent('click', function() {
					animation = true;
					
					if(current_offset <= scrollSize - size) {
						current_offset += offset;
					} else {
						current_offset = 0;
					}
					
					scroller.start(current_offset, 0);
				});
			}
			
			if(auto_anim){
				(function(){
					if(!animation) module.getElement('.nspNext').fireEvent("click");
					else animation = false;
				}).periodical($G['animation_interval'] / 2);
			}
		}
		
		if(Browser.ie7) {
			module.getElements('.nspArtHeadline').each(function(headline) {
				headline.setStyle('position', 'static');
				headline.setStyle('margin-top', -headline.getSize().y + "px");
			});
		}
	});
});