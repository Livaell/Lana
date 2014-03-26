window.addEvent("load", function(){	
    $$('.nspMainPortalMode3').each(function(module){
		var id = module.getProperty('id');
		var $G = $Gavick[id];
		var current_art = 0;
		var arts = module.getElements('.nspTitleBlock');
		var anim_speed = $G['animation_speed'];
		var animation = false;
		var slides = [];
		var animation = false;
		module.getElements('.nspArtMore').removeClass('unvisible');
		
		module.getElements('.nspArtMore').each(function(el,i) {
			el.setProperty('id', id + '-tab-' + i);
			slides[el.getProperty('id')] = new Fx.Slide(el, {duration:anim_speed}).hide();
			el.setOpacity(0);
			el.setStyle('margin-left', (el.getParent().getParent().getElement('.nspTitleTab .nspDate').getSize().x - 1) + "px");
		});
		module.getElements('.nspTitleBlock').each(function(el,i){
			el.addEvent('click', function() {
				if(!animation) {
					animation = true;
					if(module.getElement('.nspTitles .opened')) {
						var elm = module.getElement('.nspTitles .opened');
						if(elm != el) {
							elm.removeClass('opened');
							slides[elm.getElement('.nspArtMore').getProperty('id')].slideOut();
							new Fx.Morph(elm.getElement('.nspArtMore'),  { duration: anim_speed }).start({'opacity':0});
						}
					}
					(function() {
						if(el.hasClass('opened')) {
							el.removeClass('opened');
							slides[el.getElement('.nspArtMore').getProperty('id')].slideOut();	
							new Fx.Morph(module.getElements('.nspArtMore')[i], { duration: anim_speed }).start({'opacity':0});
						} else {
							el.addClass('opened');
							new Fx.Morph(module.getElements('.nspArtMore')[i], { duration: anim_speed }).start({'opacity':1});
							slides[el.getElement('.nspArtMore').getProperty('id')].slideIn();
						}
					}).delay(anim_speed + 50);
					(function() {
						animation = false;
					}).delay((anim_speed * 2) + 50);
				}
			});
		});
		if($G['open_first'] == 1) {
			module.getElement('.nspTitleBlock').fireEvent('click');
		}
	});
});