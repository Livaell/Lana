if (typeof( window['gjToggler'] ) == "undefined") {

	window.addEvent('domready', function() {
		if (document.getElements('.gjtoggler').length) {
			gjToggler = new gjToggler();
		} else {
			// Try again 2 seconds later, because IE sometimes can't see object immediatly
			(function() {
				if (document.getElements('.gjtoggler').length) {
					gjToggler = new gjToggler();
				}
			}).delay(2000);
		}
	});

	var gjToggler = new Class({
		initialize: function() {
			var self = this;
			var ElementsBeToggled = document.getElements('.gjtoggler');
			Array.each(ElementsBeToggled, function(toBeToggled) {//iterate all toBeToggled blocks and find switches
				self.connectWithSwitch(toBeToggled);
			});
		},
		connectWithSwitch: function (toBeToggled) {

			var openValues = new Array;
			// Get switch name(s) and values for the switch(es) to the toggle object
			var valuestmp = toBeToggled.className.split(' ')[1].split('___');
			for (i = 1; i < valuestmp.length; i++) {//skip 0 element, it's just an uniqid'
				openValues.push(valuestmp[i].split('.')[1]);
			}
			//Now I need  to find the switch element
			toBeToggled.SwitchName = toBeToggled.className.split('___')[1].split('.')[0];//i.e. id="6345282___showit.1___showit.2" where the strange number is an uniqid, and showit.1 meand the name of the switcher and the value of the switcher
			var switcher;
			toBeToggled.groupname = '';//Prepare flag to store name of the group, if the toBeToggled is inside a group
			// Now we need to find the switch in the DOM.
			// If we are in a group, then we must look for the Switch only in the group
			toBeToggled.isGroup = toBeToggled.getParent('.repeating_group');
			if (toBeToggled.isGroup) {

				classes = toBeToggled.isGroup.className.split(' ');
				for (k = 0; k < classes.length; k++) {
					curclass = classes[k].split('__');
					if (curclass[0] == 'variablegroup') {
						toBeToggled.groupname = curclass[1];
						break;
					}
				}
				// We cannot rely on element id's as while duplicating groups the id's are nulled,
				// so we form the element name to find elements by name inside the group
				var switchElementName ='jform[params][{'+toBeToggled.groupname+']['+toBeToggled.SwitchName+'][]';
				var switchers = toBeToggled.isGroup.getElements('.sliderContainer select');//Radio and Checkboxes cannot be duplicated, only Select

				for (l = 0; l < switchers.length; l++) {
					if (switchers[l].name == switchElementName || switchers[l].name == switchElementName+'[]') {
						switcher = switchers[l];
						break;
					}
				}

			}
			else {
				var switchElementName ='jform[params]['+toBeToggled.SwitchName+']';
				var startFromElement = gjScripts.getParentByTagName(toBeToggled,'fieldset');
				var switchers = startFromElement.getElements('select');//Radio and Checkboxes cannot be duplicated, only Select
				for (l = 0; l < switchers.length; l++) {
					if (switchers[l].name == switchElementName || switchers[l].name == switchElementName+'[]') {//Normal field or a variable one
						switcher = switchers[l];
						break;
					}
				}
			}

			var myVerticalSlide = new Fx.Slide(toBeToggled,{
								resetHeight: true,
								onStart: function() {
									this.wrapper.setStyle('clear','both');
									this.wrapper.setStyle('width','100%');
								},
								onComplete: function() {
									this.wrapper.setStyle('width','100%');
								}
							});
			if (!openValues.contains(switcher.value)) {
				myVerticalSlide.hide();
			}
			else {
				myVerticalSlide.wrapper.style.overflow = 'visible';
				myVerticalSlide.element.style.overflow = 'visible';
			}
			var switchFunction = function(event){
				if (event && event.stop) event.stop();
				var open = openValues.contains(switcher.value);
				if (open) {
					myVerticalSlide.slideIn().chain(function(){
							this.wrapper.style.overflow = 'visible';
							this.element.style.overflow = 'visible';
						});
				}
				else {
					myVerticalSlide.wrapper.style.overflow = 'hidden';
					myVerticalSlide.element.style.overflow = 'hidden';
					myVerticalSlide.slideOut();
				}
				return;
			}

			if (JVERSION>='3.0') {//J3+
				jQuery(switcher).chosen().change(switchFunction);
			}
			else {//2.5
				switcher.addEvent('change', switchFunction);
			}

			if (!openValues.contains(switcher.value)) {
				myVerticalSlide.hide();
			}
			return true;
		}

	});
}
