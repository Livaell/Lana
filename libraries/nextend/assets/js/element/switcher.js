(function(dojo) {
    dojo.declare("NextendElementSwitcher", NextendElement, {
        constructor: function(args) {
            dojo.mixin(this, args);
            this.value = '';
            this.hidden = dojo.byId(this.hidden);

            dojo.connect(this.hidden, 'change', this, 'reset');

            this.reset();
            dojo.connect(this.hidden.parentNode, 'click', this, 'switchSelected');
        },

        reset: function() {
            if (this.value != this.hidden.value) {
                this.value = this.hidden.value;
                var i = this.values.indexOf(this.value);
                if (i == -1) {
                    i = 0;
                    this.value = this.hidden.value = this.values[i];
                    this.fireEvent(this.hidden, 'change');
                }
                this.setSelected(i);
            }
        },

        setSelected: function(x) {
            this.hidden.value = this.values[x];
            if (x == 1) {
                dojo.addClass(this.hidden.parentNode, 'nextend-switcher-on');
            } else {
                dojo.removeClass(this.hidden.parentNode, 'nextend-switcher-on');
            }
        },

        switchSelected: function() {
            var i = this.values.indexOf(this.value);
            if (i == 1) i = 0;
            else i = 1;
            this.setSelected(i);
            this.fireEvent(this.hidden, 'change');
        }
    });
})(ndojo);