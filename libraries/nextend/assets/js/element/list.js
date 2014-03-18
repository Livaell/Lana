(function(dojo) {
    dojo.declare("NextendElementList", NextendElement, {
        constructor: function(args) {
            dojo.mixin(this, args);
            
            this.select = dojo.byId(this.hidden+'_select');
            this.hidden = dojo.byId(this.hidden);
            
            dojo.connect(this.select, 'change', this, 'onSelect');
            
            this.hidden.select = this.select;
            this.reset();
        },

        reset: function() {
            if(this.hidden.value != this.value){
                this.value = this.hidden.value;
                var value = this.value.split('||');
                var items = dojo.query('option', this.select);
                for(var i = 0; i < items.length; i++){
                    if(value.indexOf(items[i].value) != -1){
                        items[i].selected = true;
                    }else{
                        items[i].selected = false;
                    }
                }
                this.fireEvent(this.hidden, 'change');
            }
        },
        
        onSelect: function(){
            var selected = [];
            var items = dojo.query('option', this.select);
            for(var i = 0; i < items.length; i++){
                if(items[i].selected){
                    selected.push(items[i].value);
                }
            }
            this.hidden.value = selected.join('||');
            this.reset();
        }
    });
})(ndojo);