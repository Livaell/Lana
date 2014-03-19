(function(dojo) {
    dojo.declare("NextendElementCheckbox", NextendElement, {
        constructor: function(args) {
            dojo.mixin(this, args);
            this.value = '';
            this.hidden = dojo.byId(this.hidden);
            this.checkboxes = dojo.query('.nextend-checkbox-option', this.hidden.parentNode);

            dojo.connect(this.hidden, 'change', this, 'reset');

            for (var i = 0; i < this.checkboxes.length; i++) {
                dojo.connect(this.checkboxes[i], 'click', dojo.hitch(this, 'changeSelected', i));
            }

            this.reset();
        },

        reset: function() {
            if (this.value != this.hidden.value) {
                this.value = this.hidden.value;
                this.resetValue();
            }
        },
        
        resetValue: function(){
            var values = this.hidden.value.split('||');
            for (var i = 0; i < this.checkboxes.length; i++) {
                if(values.indexOf(this.values[i]) == -1){
                    dojo.removeClass(this.checkboxes[i], 'selected');
                }else{
                    dojo.addClass(this.checkboxes[i], 'selected');
                }
            }
            this.hidden.value = values.join('||');
        },
        
        refreshValue: function(){
            var values = [];
            for (var i = 0; i < this.checkboxes.length; i++) {
                if(dojo.hasClass(this.checkboxes[i], 'selected')){
                    values.push(this.values[i]);
                }
            }
            this.hidden.value = values.join('||');
        },

        setSelected: function(x) {
            for (var i = 0; i < this.checkboxes.length; i++) {
                this.checkboxes[i].removeClass('selected');
            }
            this.checkboxes[x].addClass('selected');
        },

        changeSelected: function(x) {
            this.switchOption(x);
            this.fireEvent(this.hidden, 'change');
        },
        
        switchOption: function(i){
            dojo.toggleClass(this.checkboxes[i], 'selected');
            this.refreshValue();
        }
    });
})(ndojo);