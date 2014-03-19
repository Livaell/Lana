(function(dojo) {
    dojo.declare("NextendElementOnoff", NextendElement, {
        constructor: function(args) {
            dojo.mixin(this, args);
            this.value = -1;
            this.hidden = dojo.byId(this.hidden);

            dojo.connect(this.hidden, 'change', this, 'reset');

            this.reset();
            dojo.connect(this.hidden.parentNode, 'click', this, 'switchSelected');
        },

        reset: function() {
            if (this.value != this.hidden.value) {
                this.value = this.hidden.value;
                this.setSelected(this.value);
            }
        },

        setSelected: function(x) {
            if (x == 1) {
                dojo.addClass(this.hidden.parentNode, 'nextend-onoff-on');
            } else {
                dojo.removeClass(this.hidden.parentNode, 'nextend-onoff-on');
            }
        },

        switchSelected: function() {
            var val = 0;
            if (this.value == 1) val = 0;
            else val = 1;
            this.hidden.value = val;
            this.fireEvent(this.hidden, 'change');
        }
    });
})(ndojo);