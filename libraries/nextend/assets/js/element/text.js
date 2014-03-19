(function(dojo) {
    dojo.declare("NextendElementText", NextendElement, {
        constructor: function(args) {
            dojo.mixin(this, args);
            this.hidden = dojo.byId(this.hidden);

            dojo.connect(this.hidden, 'focus', this, 'focus');
            dojo.connect(this.hidden, 'blur', this, 'blur');
        },

        focus: function() {
            dojo.addClass(this.hidden.parentNode, 'focus');
        },

        blur: function() {
            dojo.removeClass(this.hidden.parentNode, 'focus');
        }
    });
})(ndojo);