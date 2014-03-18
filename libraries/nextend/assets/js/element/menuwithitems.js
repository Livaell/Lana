(function(dojo) {
    dojo.declare("NextendElementMenuWithItems", NextendElement, {
        constructor: function(args) {
            this.value = '';
            dojo.mixin(this, args);
            this.value = this.value.split('|*|')[0];
            this.hidden = dojo.byId(this.hidden);
            this.mixed = this.hidden.nextendmixed;
            dojo.connect(this.hidden, 'change', this, 'refreshList');
        },
        
        refreshList: function(){
            var values = this.hidden.value.split('|*|');
            if(values[0] != this.value){
                this.value = values[0];
                this.removeOptions();
                this.createOptions(this.value);
            }
        },
        
        removeOptions: function(){
            var options = dojo.query('option', this.mixed.elements[1].select);
            for(var i = 1; i < options.length; i++){
                dojo.destroy(options[i]);
            }
            dojo.attr(options[0], 'selected', 'selected');
        },
        
        createOptions: function(value){
            var select = this.mixed.elements[1].select;
            var options = this.options[value];
            for(var i = 0; i < options.length; i++){
                dojo.create('option', {value: options[i][0], innerHTML: options[i][1]}, select);
            }
        }
        
        
    });
})(ndojo);