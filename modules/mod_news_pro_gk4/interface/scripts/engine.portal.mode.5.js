window.addEvent("load", function(){	
    $$('.nspMainPortalMode5').each(function(module){
		var id = module.getProperty('id');
		var $G = $Gavick[id];
		var current_offset = 0;
		var arts = module.getElements('.nspArt');
		var auto_anim = module.hasClass('autoanim');
		var anim_speed = $G['animation_speed'];
		var anim_interval = $G['animation_interval'];
		var animation = false;
		var scrollWrap = module.getElement('.nspArts');
		var scroller = new Fx.Scroll(scrollWrap, {duration: anim_speed, wheelStops: false});
		var dimensions = scrollWrap.getSize();
		var startItem = 0;
		var sizeWrap = scrollWrap.getCoordinates();
		var rtl_mode = module.getProperty('data-direction') == 'rtl' ? true : false;
		
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
					if(rtl_mode) {
						if(current_offset >= 0) {
							current_offset = scrollSize - size;
						} else {
							current_offset += offset;
						}
					} else {
						if(current_offset <= 0) {
							current_offset = scrollSize - size;
						} else {
							current_offset -= offset;
						}	
					}
					
					scroller.start(current_offset, 0);
				});
			}
			
			if(module.getElement('.nspNext')) {
				module.getElement('.nspNext').addEvent('click', function() {
					animation = true;
					if(rtl_mode) {
						if(current_offset <= scrollSize - size) {
							current_offset -= offset;
						} else {
							current_offset = 0;
						}
					} else {
						if(current_offset <= scrollSize - size) {
							current_offset += offset;
						} else {
							current_offset = 0;
						}
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
	});
});