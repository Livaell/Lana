(function(dojo) {
    dojo.declare("NextendElementSkin", NextendElement, {
        constructor: function(args) {
            dojo.mixin(this, args);
            
            this.hidden = dojo.byId(this.hidden);
            this.select = this.hidden.select;
            this.origText = this.select.options[0].text;
            dojo.connect(this.hidden, 'change', this, 'loadSkin');
        },
        
        loadSkin: function(){
            if(this.hidden.value != '0' && this.skins[this.hidden.value]){
                var skin = this.skins[this.hidden.value];
                for (var k in skin) {
                    if (skin.hasOwnProperty(k)) {
                        var el = dojo.byId(this.preid+k);
                        if(el){
                            if(el.value.substr(0, 2) == '{"'){ // font
                                var orig = dojo.fromJson(el.value);
                                var font = dojo.fromJson(skin[k]);
                                for (var tab in font) {
                                    if (font.hasOwnProperty(tab)) {
                                        if(typeof font[tab].reset != 'undefined'){
                                            orig[tab] = {};
                                        }
                                        if(typeof orig[tab] == 'undefined') orig[tab] = {};
                                        for (var prop in font[tab]) {
                                            if (font[tab].hasOwnProperty(prop)) {
                                                orig[tab][prop] = font[tab][prop];
                                            }
                                        }
                                    }
                                }
                                el.value = dojo.toJson(orig);
                            }else{
                                el.value = skin[k];
                            }
                            this.fireEvent(el, 'change');
                        }
                    }
                };
                
                
                this.changeText('Done');
                this.select.selectedIndex = 0;
                this.fireEvent(this.select, 'change');
                setTimeout(dojo.hitch(this, 'changeText', this.origText), 3000);
            }
        },
        
        changeText: function(text){
            this.select.options[0].text = text;
        }
    });
})(ndojo);