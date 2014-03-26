window.addEvent("load", function(){	
    $$('.nspMainPortalMode1').each(function(module){
		var id = module.getProperty('id');
		var $G = $Gavick[id];
		var current_art = 0;
		var arts = module.getElements('.nspArt');
		var arts_pos = [];
		var auto_anim = module.hasClass('autoanim');
		var anim_speed = $G['animation_speed'];
		var anim_interval = $G['animation_interval'];
		var animation = false;
		var scrollWrap = module.getElement('.nspArts');
		var scroller = new Fx.Scroll(scrollWrap, {duration: anim_speed, wheelStops: false});
		var dimensions = scrollWrap.getSize();
		var startItem = 0;
		var sizeWrap = scrollWrap.getCoordinates();
		// reset
		scroller.set(0,0);
		//
		if(arts.length > 0 &&  scrollWrap.getScrollSize().y >  scrollWrap.getSize().y){
			// find first unvisible news
			var found = false;
			//
			for(var i = 0; i < arts.length && !found; i++) {
				var size = arts[i].getCoordinates();
				if((size.top - sizeWrap.top) + size.height - 10 > dimensions.y) {
					found = i;
				}	
			}
			//
			start_item = found;
		}
		
		arts.each(function(art,i) {
			arts_pos[i] = {
				top: art.getCoordinates().top, 
				height: art.getCoordinates().height
			};
		});
		if(module.getElement('.nspTopInterface .nspPrev')) {
			module.getElement('.nspTopInterface .nspPrev').addEvent('click', function() {
				animation = true;
				if(current_art == 0) {
					current_art = arts.length - 1;
				} else {
					current_art--;
				}
				if(current_art > 0) {
					var to = arts_pos[current_art];
					scroller.start(0, Math.abs(sizeWrap.height - ((to.top - sizeWrap.top) + to.height)));
				} else {
					scroller.start(0,scrollWrap.getScrollSize().y);
				}
			});

			module.getElement('.nspTopInterface .nspNext').addEvent('click', function() {
				animation = true;
				if(current_art == 0) {
					current_art = start_item;
				} else {
					if(current_art < arts.length - 1) {
						current_art++;
					} else {
						current_art = 0;
					}
				}
								
				if(current_art > 0) {
					var to = arts_pos[current_art];
					scroller.start(0, Math.abs(sizeWrap.height - ((to.top - sizeWrap.top) + to.height)));
				} else {
					scroller.start(0,0);
				}
			});
		}
		if(auto_anim){
			(function(){
				if(!animation) module.getElement('.nspTopInterface .nspNext').fireEvent("click");
				else animation = false;
			}).periodical($G['animation_interval'] / 2);
		}
	});
});