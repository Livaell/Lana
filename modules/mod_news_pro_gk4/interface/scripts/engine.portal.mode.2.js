window.addEvent("load", function(){	
    $$('.nspMainPortalMode2').each(function(module){
		var id = module.getProperty('id');
		var $G = $Gavick[id];
		var current_art = 0;
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
		// reset
		scroller.start(0,0);
		//
		if(module.getElement('.nspBotInterface .nspPrev')) {
			module.getElement('.nspBotInterface .nspPrev').addEvent('click', function() {
				animation = true;
				new Fx.Morph(headline_titles[current_art], {duration:anim_speed / 2}).start({'opacity':0});
				if(current_art == 0) {
					current_art = arts.length - 1;
				} else {
					current_art--;
				}
				scroller.toElement(arts[current_art]);
				headlines.start({'margin-top':-1 * headline_size * current_art});
				new Fx.Morph(headline_titles[current_art],{duration:anim_speed * 2}).start({'opacity':1});
				arts.each(function(art,i){
					if(i !== current_art && arts[i].hasClass('active')) {
						arts[i].removeClass('active');
					} else if(i == current_art) {
						if(!arts[i].hasClass('active')) arts[i].addClass('active');
					}
				});
			});
		}
		if(module.getElement('.nspBotInterface .nspNext')) {
			module.getElement('.nspBotInterface .nspNext').addEvent('click', function() {
				animation = true;
				new Fx.Morph(headline_titles[current_art], {duration:anim_speed / 2}).start({'opacity':0});
				if(current_art < arts.length - 1) {
					current_art++;
				} else {
					current_art = 0;
				}
				scroller.toElement(arts[current_art]);				headlines.start({'margin-top': -1 * headline_size * current_art});
				new Fx.Morph(headline_titles[current_art],  {duration:anim_speed * 2}).start({'opacity':1});
				arts.each(function(art,i){
					if(i !== current_art && arts[i].hasClass('active')) {
						arts[i].removeClass('active');
					} else if(i == current_art) {
						if(!arts[i].hasClass('active')) arts[i].addClass('active');
					}
				});
			});
		}
		if(auto_anim){
			(function(){
				if(!animation) module.getElement('.nspBotInterface .nspNext').fireEvent("click");
				else animation = false;
			}).periodical($G['animation_interval'] / 2);
		}
	});
});