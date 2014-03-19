(function(dojo) {
    dojo.declare("NextendElementRadio", NextendElement, {
        constructor: function(args) {
            dojo.mixin(this, args);
            this.value = '';
            this.hidden = dojo.byId(this.hidden);
            this.radios = dojo.query('.nextend-radio-option', this.hidden.parentNode);

            dojo.connect(this.hidden, 'change', this, 'reset');

            for (var i = 0; i < this.radios.length; i++) {
                dojo.connect(this.radios[i], 'click', dojo.hitch(this, 'changeSelected', i));
            }

            this.reset();
        },

        reset: function() {
            if (this.value != this.hidden.value) {
                this.value = this.hidden.value;
                var i = this.values.indexOf(this.value);
                if (i == -1) {
                    i = this.partialSearch(this.hidden.value.replace(/^.*[\\\/]/, ''));
                    this.value = this.hidden.value = this.values[i];
                    this.fireEvent(this.hidden, 'change');
                }
                this.setSelected(i);
            }
        },

        setSelected: function(x) {
            for (var i = 0; i < this.radios.length; i++) {
                dojo.removeClass(this.radios[i], 'selected');
            }
            dojo.addClass(this.radios[x],'selected');
        },

        changeSelected: function(x) {
            this.hidden.value = this.values[x];
            this.fireEvent(this.hidden, 'change');
        },
        
        partialSearch: function(text){
            for (var i = 0; i < this.values.length; i++) {
                if(this.values[i].indexOf(text) != -1) return i;
            }
            return 0;
        }
    });
})(ndojo);