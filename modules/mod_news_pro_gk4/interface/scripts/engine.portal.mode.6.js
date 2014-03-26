window.addEvent("load", function(){   
    $$('.nspMainPortalMode6').each(function(module){
      new GKNSPSpeakers(module);
   });
});

var GKNSPSpeakers = new Class({
   id: '',
   $G: null,
   current_offset: 0,
   anim_interval: 0,
   current: 0,
   total: 0,
   items: [],
   availableItems: null,
   hover: false,
   
   initialize: function(module) {
      this.id = module.getProperty('id');
      this.$G = $Gavick[this.id];
      this.current_offset = 0;
      this.anim_interval = this.$G['animation_interval'];
      this.current = 4;
      this.total = module.getElements('.nspRestSpeakers .nspSpeaker').length;
      
      // if there is more than 5 slides
      if(this.total >= 5) {
         // prepare handlers
         this.items[0] = module.getElement('.nspSpeakers .nspSpeakersSmallLeft .nspSpeakerSmall');
         this.items[1] = module.getElement('.nspSpeakers .nspSpeakersSmallLeft').getElements('.nspSpeakerSmall')[1];
         this.items[2] = module.getElement('.nspSpeakers .nspSpeakerBig');
         this.items[3] = module.getElement('.nspSpeakers .nspSpeakersSmallRight .nspSpeakerSmall');
         this.items[4] = module.getElement('.nspSpeakers .nspSpeakersSmallRight').getElements('.nspSpeakerSmall')[1];
         // 
         this.availableItems = module.getElements('.nspRestSpeakers .nspSpeaker'); 
         //
         var $this = this;
         //
         this.items.each(function(el, i) {
            el.removeClass('speakerHide');
         });
         // run the animation
         setTimeout(function() {
            $this.gkChangeSpeakers();      
         }, this.anim_interval + 400);
         
         this.items.each(function(el, i) {
            el.addEvent('mouseenter', function() {
               $this.hover = true;
            });
            
            el.addEvent('mouseleave', function() {
               $this.hover = false;
            });
         });
      }
   },

   gkChangeSpeakers: function() {
      //
      var $this = this;
      //
      if(!this.hover) {
         // hide speakers
         this.items.each(function(el, i) {
            el.addClass('speakerHide');
         });
         
         if(this.current < this.total - 1) {
            this.current += 1;
         } else {
            this.current = 0;
         }
         
         setTimeout(function() {
            var IDs = [0, 0, 0, 0, 0]; 
            
            IDs[4] = $this.current;
            totalOffset = $this.total;
            
            IDs[3] = ($this.current - 1 < 0) ? --totalOffset : $this.current - 1;
            IDs[2] = ($this.current - 2 < 0) ? --totalOffset : $this.current - 2;
            IDs[1] = ($this.current - 3 < 0) ? --totalOffset : $this.current - 3;
            IDs[0] = ($this.current - 4 < 0) ? --totalOffset : $this.current - 4;
            
            $this.items[0].innerHTML = $this.availableItems[IDs[0]].innerHTML;
            $this.items[1].innerHTML = $this.availableItems[IDs[1]].innerHTML;
            $this.items[2].innerHTML = $this.availableItems[IDs[2]].innerHTML;
            $this.items[3].innerHTML = $this.availableItems[IDs[3]].innerHTML;
            $this.items[4].innerHTML = $this.availableItems[IDs[4]].innerHTML;
         }, 600);
         
         // show speakers
         setTimeout(function() {
            $this.items.each(function(el, i) {
               el.removeClass('speakerHide');
            });
         }, 750);
      }
      //
      setTimeout(function() {
         $this.gkChangeSpeakers();      
      }, this.anim_interval + 800);
   }
});