(function(dojo) {
    dojo.declare("NextendFontmanager", NextendElement, {
        
        defaults: {
            afont: 'Arial',
            color: '000000',
            size: '12|*|px',
            lineheight: 'normal',
            bold: 0,
            italic: 0,
            underline: 0,
            paddingleft: 0,
            align: 'left',
            tshadow: '0|*|0|*|0|*|000000FF'
        },
        
        constructor: function(args) {
            this.dataset = true;
            dojo.mixin(this, args);
            this.node = dojo.byId(this.node);
            if (typeof window.nextendfontmanager != 'undefined') {
                dojo.destroy(this.node);
                return;
            }
            window.nextendfontmanager = this;
            dojo.attr(this.node, 'id', 'nextend-fontmanager-lightbox');
            this.windowel = dojo.place(this.node, dojo.body());

            this.window = new NextendWindow({
                node: dojo.byId("nextend-fontmanager-lightbox"),
                save: dojo.byId("nextend-fontmanager-save"),
                onHide: dojo.hitch(this, 'save')
            });

            this.tabsnode = dojo.byId('nextend-fontmanager-tabs');
            
            this.preview = dojo.byId('nextend-fontmanager-preview');
            
            this.cleartabnode = dojo.byId('nextend-fontmanager-cleartab');
            dojo.connect(this.cleartabnode, 'click', this, 'clearTab');

            this.family = dojo.byId('fontmanagerfamily');
            dojo.connect(this.family, 'change', this, 'changeFamily');

            this.color = dojo.byId('fontmanagercolor');
            dojo.connect(this.color, 'change', this, 'changeColor');

            this.size = dojo.byId('fontmanagersize');
            dojo.connect(this.size, 'change', this, 'changeSize');

            this.lineheight = dojo.byId('fontmanagerlineheight');
            dojo.connect(this.lineheight, 'change', this, 'changeLineheight');
            
            this.decoration = dojo.byId('fontmanagerdecoration');
            dojo.connect(this.decoration, 'change', this, 'changeDecoration');
            
            this.paddingleft = dojo.byId('fontmanagerpaddingleft');
            dojo.connect(this.paddingleft, 'change', this, 'changePaddingleft');

            this.textalign = dojo.byId('fontmanagertextalign');
            dojo.connect(this.textalign, 'change', this, 'changeAlign');

            this.textshadow = dojo.byId('fontmanagertshadow');
            dojo.connect(this.textshadow, 'change', this, 'changeTextshadow');
            
            this.backgroundcolor = dojo.byId('nextend-fontmanager-backgroundcolor');
            jQuery(this.backgroundcolor).spectrum({
                showAlpha: this.alpha,
                preferredFormat: "hex6",
                showInput: true,
                showButtons: false,
                move: dojo.hitch(this, 'backgroundChange')
            });
            dojo.style(this.backgroundcolor, 'display', 'none');
        },
        
        show: function(tabs, value){
            this.tabsnode.innerHTML = '';
            this.tabs = [];
            try{
                this.data = dojo.fromJson(value);
            }catch(e){
                this.data = {};    
            }
            this.currentTab = 0;
            for(var i = 0; i < tabs.length; i++){
                var selected = (i == this.currentTab ? ' selected' : '');
                var tab = dojo.create('div', {'class': 'nextend-tab'+selected, 'innerHTML': tabs[i], 'value': tabs[i]}, this.tabsnode);
                dojo.connect(tab, 'click', dojo.hitch(this, 'changeTab', i));
                this.tabs.push(tab);
                if(typeof this.data[tabs[i]] == 'undefined') this.data[tabs[i]] = {};
            }
            this.loadTabSettings(0);
            var _this = this;
            setTimeout(function(){
              _this.window.showOverlay();
            }, 100);
        },
        
        save: function(){
            this.data.firsttab = this.firsttab;
            this.onSave(dojo.toJson(this.data));
            this.data = null;
        },
        
        onSave: function(){},
        
        changeTab: function(i){
            if(i == this.currentTab) return;
            dojo.removeClass(this.tabs[this.currentTab], 'selected');
            this.currentTab = i;
            dojo.addClass(this.tabs[i], 'selected');
            this.loadTabSettings(i);
        },
        
        loadTabSettings: function(i){
            if(this.tabs[i].value == this.firsttab){
                dojo.style(this.cleartabnode, 'display', 'none');
            }else{
                dojo.style(this.cleartabnode, 'display', 'block');
            }
            this.dataset = false;
            var tab = this.tabs[i].value;
            var family = this.getProperty(tab,'afont').split('||'); // split for a while for compatibility
            this.family.value = family[0];
            this.fireEvent(this.family, 'change');
            
            this.color.value = this.getProperty(tab,'color');
            this.fireEvent(this.color, 'change');
            
            var size = this.getProperty(tab,'size').split('||'); // split for a while for compatibility
            this.size.value = size.join('|*|');
            this.fireEvent(this.size, 'change');
            
            this.lineheight.value = this.getProperty(tab,'lineheight');
            this.fireEvent(this.lineheight, 'change');
            
            var decoration = [];
            if(this.getProperty(tab,'bold') == 1)
                decoration.push('bold');
            if(this.getProperty(tab,'italic') == 1)
                decoration.push('italic');
            if(this.getProperty(tab,'underline') == 1)
                decoration.push('underline');
            this.decoration.value = decoration.join('||');
            this.fireEvent(this.decoration, 'change');
            
            this.paddingleft.value = this.getProperty(tab,'paddingleft');
            this.fireEvent(this.paddingleft, 'change');
            
            this.textalign.value = this.getProperty(tab,'align');
            this.fireEvent(this.textalign, 'change');
            
            var shadow = this.getProperty(tab,'tshadow').replace(/\|\|px/g, '');
            this.textshadow.value = shadow;
            this.fireEvent(this.textshadow, 'change');
            this.dataset = true;
        },
        
        getProperty: function(tab, prop){
            var value = this.data[tab];
            if(value && typeof value[prop] != 'undefined'){
                return value[prop];
            }else if(tab != this.firsttab){
                return this.getProperty(this.firsttab, prop);
            }
            if(console) console.log('Undefined '+tab+' property '+prop);
            return this.defaults[prop];
            return "";
        },
        
        setProperty: function(tab, prop, value){
            if(this.dataset) this.data[tab][prop] = value;
        },
        
        getCurrentProperty: function(prop, value){
            this.getProperty(this.tabs[this.currentTab].value, prop, value);
        },
        
        setCurrentProperty: function(prop, value){
            this.setProperty(this.tabs[this.currentTab].value, prop, value);
        },
        
        clearTab: function(){
            if(this.tabs[this.currentTab].value != this.firsttab){
                this.data[this.tabs[this.currentTab].value] = {};
                this.loadTabSettings(this.currentTab);
            }
        },

        changePreview: function(prop, value) {
            try{
                dojo.style(this.preview, prop, value);
            }catch(err){}
        },

        changeFamily: function() {
            var family = this.family.value;
            var re = /google\(.*?family=(.*?)\);\)/g;
            var match = null;
            while(match = re.exec(this.family.value)){
                dojo.create('link', {
                    rel: 'stylesheet',
                    type: 'text/css',
                    href: 'http://fonts.googleapis.com/css?family=' + match[1]
                }, dojo.body());
                
                var f = match[1].replace('+', ' ').match(/[a-zA-Z ]*/);
                family = family.replace(match[0], f[0]);
            }//google(Skranji:700&subset=latin,latin-ext)
            this.setCurrentProperty('afont', this.family.value);
            this.changePreview('fontFamily', family);
        },
        
        changeColor: function(){
            this.setCurrentProperty('color', this.color.value);
            var c = this.hex2rgba(this.color.value);
            this.changePreview('color', '#'+this.color.value.substr(0,6));
            this.changePreview('color', 'RGBA('+c[0]+','+c[1]+','+c[2]+','+c[3]+')');
        },

        changeSize: function() {
            this.setCurrentProperty('size', this.size.value.replace('|*|', '||'));
            this.changePreview('fontSize', this.size.value.replace('|*|', ''));
        },

        changeLineheight: function() {
            this.setCurrentProperty('lineheight', this.lineheight.value);
            this.changePreview('lineHeight', this.lineheight.value);
        },
        
        changeDecoration: function() {
            if(this.decoration.value.indexOf('bold') != -1){
                this.setCurrentProperty('bold', 1);
                this.changePreview('fontWeight', 'bold');
            }else{
                this.setCurrentProperty('bold', 0);
                this.changePreview('fontWeight', 'normal');
            }
            
            if(this.decoration.value.indexOf('italic') != -1){
                this.setCurrentProperty('italic', 1);
                this.changePreview('fontStyle', 'italic');
            }else{
                this.setCurrentProperty('italic', 0);
                this.changePreview('fontStyle', 'normal');
            }
            
            if(this.decoration.value.indexOf('underline') != -1){
                this.setCurrentProperty('underline', 1);
                this.changePreview('textDecoration', 'underline');
            }else{
                this.setCurrentProperty('underline', 0);
                this.changePreview('textDecoration', 'none');
            }
        },
        
        changePaddingleft: function() {
            this.setCurrentProperty('paddingleft', this.paddingleft.value);
        },

        changeAlign: function() {
            this.setCurrentProperty('align', this.textalign.value);
            this.changePreview('textAlign', this.textalign.value);
        },

        changeTextshadow: function() {
            this.setCurrentProperty('tshadow', this.textshadow.value);
            var ts = this.textshadow.value.split('|*|');
            if (ts[0] == '0' && ts[1] == '0' && ts[2] == '0') {
                this.changePreview('textShadow', 'none');
            } else {
                var c = this.hex2rgba(ts[3]);
                this.changePreview('textShadow', ts[0] + 'px ' + ts[1] + 'px ' + ts[2] + 'px ' + 'RGBA('+c[0]+','+c[1]+','+c[2]+','+c[3]+')');
            }
        },
        
        backgroundChange: function(color){
            this.changePreview('backgroundColor', '#'+color.toHexString());
        },

        hex2rgba: function(str) {
            var num = parseInt(str, 16); // Convert to a number
            return [num >> 24 & 255, num >> 16 & 255, num >> 8 & 255, (num & 255)/255];
        }
    });
})(ndojo);