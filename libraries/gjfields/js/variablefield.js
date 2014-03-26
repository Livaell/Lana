if (typeof( window['gjVariablefield'] ) == "undefined") {

	window.addEvent('domready', function() {
		if (document.getElements('.groupSlider').length) {
			gjVariablefield = new gjVariablefield();// Initialize all groups as sliders
		} else {
			// Try again 2 seconds later, because IE sometimes can't see object immediatly
			(function() {
				if (document.getElements('.groupSlider').length) {
					gjVariablefield = new gjVariablefield();
				}
			}).delay(2000);
		}
	});

	var gjVariablefield = new Class({
		initialize: function() {
			var self = this;
			Array.each(document.getElements('.groupSlider'), function(element) {//iterate all toBeToggled blocks and find switches
				self.initSlider(element);
			});
		},
		initSlider: function  (element) {

			if (JVERSION>='3.0') {
				var path = 'div.sliderContainer';
			}
			else {
				var path = 'div.sliderContainer';
			}
			//for each element.children create FX
			var slidePanelElement = element.parentNode.getNext(path);
			var myVerticalSlide = new Fx.Slide(slidePanelElement,{
								resetHeight: true,
								onStart: function() {this.wrapper.setStyle('width','100%');},
								onComplete: function () {
									if (this.element.style.overflow !== 'visible') {
										this.element.setStyle('overflow','visible');
										this.wrapper.setStyle('overflow','visible');
									}
									else {
										this.element.setStyle('overflow','hidden');
									}
									this.wrapper.setStyle('width','100%');
								}
							});
			//for each element create Click
			var input = element.parentNode.getChildren('span.hdr-wrppr input.groupnameEditField')[0];
			input.addEvent('click', function(event){
				if (input.readOnly != true) { return; }
				//I cannot use myVerticalSlide.toggle(); due to Opear bug with Fx.Slide().toggle() and to JQuery.chosen() overflow after Fx.slide operations - a dropdown list would be trimmed by a slider
				if (slidePanelElement.parentNode.style.overflow == 'visible') {
					slidePanelElement.parentNode.style.overflow = 'hidden';
					myVerticalSlide.slideOut();
				}
				else {
					myVerticalSlide.slideIn();
				}
				var label = this.parentNode.parentNode.childNodes[0];
				label.toggleClass('groupClosed');
				this.parentNode.parentNode.getNext('.groupState').value ^= true;
			});
			label = input.parentNode.parentNode.childNodes[0];

			if (element.parentNode.getNext('.groupState').value == '0') {
				myVerticalSlide.hide();
				label.classList.add("groupClosed");
			}
			else {
				label.className.replace(/\bgroupClosed\b/,'');
				slidePanelElement.style.overflow = 'visible';
				slidePanelElement.parentNode.style.overflow = 'visible';
			}
			return false;
		},
		// JavaScript Document
		delete_current_slide: function (me) {
			var currPanel = new Element(me.parentNode.parentNode.parentNode);
			var remove = false;
			if (currPanel.className=='variablefield_div repeating_element' && currPanel.parentNode.getChildren().length >1) {
				remove = true;
			}
			else {
				var classes = currPanel.className.split(' ');
				var search = '';
				for (i = 0; i <classes.length ; i++) {
					search = search+'.'+classes[i];
				}
				if (currPanel.parentNode.getChildren(search).length >1) {
					remove = true;
				}
			}
			if (remove == true) {
				currPanel.fade('out');
				remove = currPanel.parentNode.removeChild(currPanel);
			}
			else {
				if (currPanel.parentNode.hasClass('repeating_block')) {
					currPanel.parentNode.highlight('#fdd');
				}
				else {
					currPanel.highlight('#fdd');
				}
			}
			return(false);
		},
		add_new_slide: function (me, maxLength) {
			var currPanel = new Element(me.parentNode.parentNode.parentNode);
			if(maxLength > 0) {
				var children = currPanel.parentNode.getChildren('div');
				if(children.length >= maxLength) {
					if (currPanel.parentNode.hasClass('repeating_block')) {
						currPanel.parentNode.highlight('#dfd');
					}
					else {
						currPanel.highlight('#dfd');
					}
					return(false);
				}
			}

			var newPanel = currPanel.clone(true);

			if (newPanel.hasClass('repeating_group')) { //If we are copying  a group
				gjScripts.moveElementOneNodeUp(newPanel.getElements('.sliderContainer')[0]); // Prepare to init slider at the new panel
			}
			newPanel.inject(currPanel,'after');
			if (JVERSION>='3.0') {//J3+
				jQuery(newPanel).find('.chzn-container').remove();
				jQuery(newPanel).find('.chzn-done').removeClass("chzn-done").addClass("chzn-select");
				jQuery(".chzn-select").chosen();
			}

			if (newPanel.hasClass('repeating_group')) {
				this.initSlider(newPanel.getChildren('.buttons_container')[0].getChildren('label.groupSlider')[0]);

				var ElementsBeToggled = newPanel.getElements('.gjtoggler');
				Array.each(ElementsBeToggled, function(toBeToggled) {//iterate all toBeToggled blocks and find switches
					gjScripts.moveElementOneNodeUp(toBeToggled);
					gjToggler.connectWithSwitch(toBeToggled);
				});
			}

			return(false);
		},
		move_up_slide: function (me) {
			var currPanel = new Element(me.parentNode.parentNode.parentNode);
			var prevPanel = currPanel.getPrevious();

			if(prevPanel) {
			   removed = currPanel.parentNode.removeChild(currPanel);
			   removed.inject(prevPanel, 'before');
			}
			return(false);
		},
		move_down_slide: function (me) {
			var currPanel = new Element(me.parentNode.parentNode.parentNode);
			var nextPanel = currPanel.getNext();

			if(nextPanel)
			{
				removed = currPanel.parentNode.removeChild(currPanel);
				removed.inject(nextPanel, 'after');
			}
			return(false);
		},
		editGroupName: function (me) {
			//me.parentNode.getChildren('label.groupSlider').toggleClass('hide');
			var input = me.parentNode.getChildren('span.hdr-wrppr input.groupnameEditField')[0];
			me.parentNode.getChildren('a.cancelGroupNameEdit').toggleClass('hide');
			if (me.innerHTML == '✍') {

				var self = this;
				self.removeClass(input,'readonlyGroupName');
				me.innerHTML = '✓';
				input.focus();
				input.readOnly = false;
			}
			else {
				//me.parentNode.getChildren('label.groupSlider')[0].innerHTML = me.parentNode.getChildren('input.groupnameEditField')[0].value;
				me.innerHTML = '✍';
				input.readOnly = true;
				input.classList.add("readonlyGroupName");
			}

		},
		cancelGroupNameEdit: function (me) {
			//me.parentNode.getChildren('label.groupSlider').toggleClass('hide');
			me.parentNode.getChildren('a.cancelGroupNameEdit').toggleClass('hide');
			//me.parentNode.getChildren('input.groupnameEditField')[0].value = me.parentNode.getChildren('label.groupSlider')[0].innerHTML;
			var input = me.parentNode.getChildren('span.hdr-wrppr input.groupnameEditField')[0];
			input.readOnly = true;
			me.parentNode.getChildren('a.editGroupNameButton')[0].innerHTML = '✍';
			input.classList.add("readonlyGroupName");
		},
		removeClass: function (ele,cls) {
			var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
			ele.className=ele.className.replace(reg,' ');
		}
	});


}
