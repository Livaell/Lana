(function(dojo) {
    dojo.declare("NextendElementGradient", NextendElement, {
        constructor: function(args) {
            this.supress = true;
            this.supressChange = false;
            this.value = '1-ffffff-ffffff';
            dojo.mixin(this, args);
            this.hidden = dojo.byId(this.hidden);
            this.onoff = dojo.byId(this.onoff);
            dojo.connect(this.onoff, 'change', this, 'changeGradientByInput');
            this.start = dojo.byId(this.start);
            dojo.connect(this.start, 'change', this, 'changeGradientByInput');
            this.end = dojo.byId(this.end);
            dojo.connect(this.end, 'change', this, 'changeGradientByInput');

            this.bg = dojo.query('.nextend-grandient-bg', this.hidden.parentNode)[0];

            this.reset();
            this.changeGradient();
            dojo.connect(this.hidden, 'change', this, 'reset');
        },

        reset: function() {
            if (this.value != this.hidden.value) {
                this.value = this.hidden.value;
                if(this.supress){
                    this.supressChange = true;
                    var split = this.value.split('-');
                    if(this.onoff.value != split[0]){
                        this.onoff.value = split[0];
                        this.fireEvent(this.onoff, 'change');
                    }
                    if(this.start.value != split[1]){
                        this.start.value = split[1];
                        this.fireEvent(this.start, 'change');
                    }
                    if(this.end.value != split[2]){
                        this.end.value = split[2];
                        this.fireEvent(this.end, 'change');
                    }
                    this.supressChange = false;
                }
                this.changeGradient();
            }
            this.supress = true;
        },
        
        changeGradientByInput: function(){
            if(this.supressChange) return;
            this.supress = false;
            var text = this.getCurrentElementValue();

            if (this.hidden.value != text) {
                this.hidden.value = text;
                this.fireEvent(this.hidden, 'change');
            }
        },

        changeGradient: function() {

            if (this.onoff.value == '1') {
                dojo.addClass(this.hidden.parentNode, 'nextend-enabled');
            } else {
                dojo.removeClass(this.hidden.parentNode, 'nextend-enabled');
            }
            if (dojo.isIE < 10) {
                dojo.style(this.bg, 'filter', 'progid:DXImageTransform.Microsoft.Gradient(GradientType=1,StartColorStr=#' + this.start.value + ',EndColorStr=#' + this.end.value + ')');
            } else if (dojo.isIE >= 10) {
                dojo.style(this.bg, 'background', 'linear-gradient( to right, #' + this.start.value + ' 0%, #' + this.end.value + ' 100%)');
            } else if (dojo.isFF) {
                dojo.style(this.bg, 'background', '-moz-linear-gradient( left, #' + this.start.value + ', #' + this.end.value + ')');
            } else if (dojo.isMozilla) {
                dojo.style(this.bg, 'background', '-moz-linear-gradient( left, #' + this.start.value + ', #' + this.end.value + ')');
            } else if (dojo.isOpera) {
                dojo.style(this.bg, 'background-image', '-o-linear-gradient(right, #' + this.start.value + ', #' + this.end.value + ')');
            } else {
                dojo.style(this.bg, 'background', '-webkit-gradient( linear, left top, right top, from(#' + this.start.value + '), to(#' + this.end.value + '))');
            }
        },
        
        getCurrentElementValue: function(){
            var text = '';
            text += this.onoff.value + '-';
            text += this.start.value + '-';
            text += this.end.value;
            return text;
        }
    });
})(ndojo);