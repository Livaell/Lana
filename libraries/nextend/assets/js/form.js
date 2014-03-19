(function(dojo) {
    dojo.declare("NextendForm", null, {
        constructor: function(args) {
            this.form = null;
            this.data = null;
            dojo.mixin(this, args);
            this.container = dojo.byId(this.container);
            this.form = dojo.query('input', this.container)[0].form;
            this.form.nextendform = this;
            // Special fix for Joomla 1.6, 1.7 & 2.5. Speedy save!
            if(typeof document.formvalidator != "undefined"){
                document.formvalidator.isValid = function() {return true;};
            }
        }
    });
})(ndojo);