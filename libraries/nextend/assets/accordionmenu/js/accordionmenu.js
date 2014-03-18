(function(dojo) {
    dojo.declare("AccordionMenu", null, {
        constructor: function(args) {
            this.interval = 1000;
            this.mode = 'onclick';
            this.classPattern = /nextend-nav-[0-9]+/;
            dojo.mixin(this, args);
            if (!this.node) return;
            if (this.css3animation && !Modernizr.cssanimations) {
                this.css3animation = 0;
            }
            
            if(this.css3animation) {
                var transEndEventNames = {
                    'WebkitTransition': 'webkitTransitionEnd',
                    'MozTransition': 'transitionend',
                    'OTransition': 'oTransitionEnd otransitionend',
                    'msTransition': 'MSTransitionEnd',
                    'transition': 'transitionend'
                };
                this.transitionEnd = transEndEventNames[Modernizr.prefixed('transition')];
            }
            if (typeof this.easing == 'string') this.easing = eval(this.easing);
            if (typeof this.closeeasing == 'string') this.closeeasing = eval(this.closeeasing);

            this.enabled = true;
            window.accordion = new Object;
            window.accordion.running = false;
            this.init();
        },

        init: function() {
            dojo.forEach(dojo.query('a', this.node), function(el) {
                var href = dojo.attr(el, 'href');
                if (href != undefined && href != '' && href != '#') {
                    dojo.connect(el, 'onclick', function(e) {
                        e.cancelBubble = true;
                    });
                }
            });

            this.opened = -1;
            this.dts = dojo.query('dt.parent.level' + this.level, this.node);
            this.dds = dojo.query('dd.parent.level' + this.level, this.node);
            this.forceopened = false;
            this.dts.forEach(function(el, i) {
                el.i = i;
                if (dojo.hasClass(this.dds[i], 'opened')) {
                    this.opened = i;
                }
                if (!dojo.hasClass(el, 'forceopened')) {
                    if (this.mode == 'both') {
                        dojo.connect(el, 'onclick', dojo.hitch(this, 'onOpenOrClose'));
                        dojo.connect(el, 'onmouseenter', dojo.hitch(this, 'onOpenOrClose'));
                    } else {
                        dojo.connect(el, this.mode, dojo.hitch(this, 'onOpenOrClose'));
                    }
                } else {
                    this.forceopened = true;
                }

                this.dds[i].dl = dojo.query('> dl', this.dds[i])[0];

                new AccordionMenu({
                    node: this.dds[i].dl,
                    level: this.level + 1,
                    mode: this.mode,
                    interval: this.interval,
                    easing: this.easing,
                    instance: this.instance,
                    classPattern: this.classPattern,
                    accordionmode: this.accordionmode,
                    css3animation: this.css3animation,
                    usecookies: this.usecookies
                });
            }, this);
            if (this.forceopened) {
                this.accordionmode = 0;
            }
            if (this.accordionmode == 2 && this.opened >= 0) {
                if (this.node.mmanim && this.node.mmanim.status() == "playing") {
                    this.node.mmanim.stop();
                }
                var pos = dojo.position(this.node).y - dojo.position(this.dts[this.opened]).y;
                dojo.style(this.node, 'marginTop', pos + 'px');
            }
        },

        onOpenOrClose: function(e) {
            var el = e.currentTarget;
            if ((this.mode == "onmouseenter" || (this.mode == "both" && e.type != 'click')) && dojo.hasClass(el, 'opened')) return;
            if (window.accordion.running) return;
            else window.accordion.running = true;
            var h = parseInt(dojo.position(this.dds[el.i]).h);
            if (dojo.hasClass(el, 'opening') || dojo.hasClass(el, 'opened')) {
                this.close(el.i);
            } else {
                if (this.accordionmode != 0 && this.opened >= 0 && this.opened != el.i) {
                    this.close(this.opened);
                }
                this.open(el.i);
            }
        },

        open: function(i) {
            var dt = this.dts[i];
            var dd = this.dds[i];
            if(dojo.hasClass(dd, 'opened')) return;
            if (dd.wwanim && dd.wwanim.status() == "playing") {
                dd.wwanim.stop();
                dojo.removeClass(dt, 'closing');
                dojo.removeClass(dd, 'closing');
            }
            dojo.removeClass(dt, 'closed');
            dojo.removeClass(dd, 'closed');
            dojo.addClass(dt, 'opening');
            dojo.addClass(dd, 'opening');

            if (this.accordionmode == 2) {
                if (this.css3animation) {
                    var pos = dojo.position(this.node).y - dojo.position(dt).y;
                    if (this.dds[this.opened]) {
                        pos += dojo.position(this.dds[this.opened]).h;
                    }
                    dojo.style(this.node, 'marginTop', pos+'px');
                }else{
                    if (this.node.mmanim && this.node.mmanim.status() == "playing") {
                        this.node.mmanim.stop();
                    }
    
                    var pos = dojo.position(this.node).y - dojo.position(dt).y;
                    if (this.dds[this.opened]) {
                        pos += dojo.position(this.dds[this.opened]).h;
                    }
                    this.node.mmanim = dojo.animateProperty({
                        node: this.node,
                        properties: {
                            marginTop: pos
                        },
                        duration: this.interval,
                        easing: this.easing
                    }).play();
                }
            }
            this.opened = this.i;
            var sub = dojo.query('dl.level' + (this.level + 1), dd);
            if (sub.length == 0) {
                sub = dojo.query('> div', dd);
            }
            var h = dojo.marginBox(sub[0]).h;
            if (this.css3animation) {
                dd.wwanim = {
                    i: i,
                    status: function() {
                        return 'playing'
                    },
                    stop: function() {}
                };
                dojo.removeClass(dd, 'notransition');
                //dd.transend = dojo.connect(dd, this.transitionEnd, this, 'onOpenEnd');
                dd.transend = dojo.hitch(this, 'onOpenEnd');
                dd.addEventListener(this.transitionEnd, dd.transend);
                setTimeout(function() {
                    dojo.style(dd, 'height', h + 'px');
                }, 30);
            } else {
                dd.wwanim = dojo.animateProperty({
                    i: i,
                    node: dd,
                    properties: {
                        height: h
                    },
                    duration: this.interval,
                    easing: this.easing,
                    onEnd: dojo.hitch(this, 'onOpenEnd')
                }).play();
            }
        },

        close: function(i) {
            var dt = this.dts[i];
            var dd = this.dds[i];
            if(dojo.hasClass(dd, 'closed')) return;
            if (dd.wwanim && dd.wwanim.status() == "playing") {
                dd.wwanim.stop();
                dojo.removeClass(dt, 'opening');
                dojo.removeClass(dd, 'opening');
            }
            dojo.addClass(dt, 'closing');
            dojo.addClass(dd, 'closing');

            if (this.accordionmode == 2) {
                if (this.css3animation) {
                    dojo.style(this.node, 'marginTop', 0);
                }else{
                    if (this.node.mmanim && this.node.mmanim.status() == "playing") {
                        this.node.mmanim.stop();
                    }
                    this.node.mmanim = dojo.animateProperty({
                        node: this.node,
                        properties: {
                            marginTop: 0
                        },
                        duration: this.interval,
                        easing: this.closeeasing
                    }).play();
                }
            }

            if (this.css3animation) {
                var sub = dojo.query('dl.level' + (this.level + 1), dd);
                if (sub.length == 0) {
                    sub = dojo.query('> div', dd);
                }
                var h = dojo.marginBox(sub[0]).h;
                dojo.style(dd, 'height', h+'px');
                dd.wwanim = {
                    i: i,
                    status: function() {
                        return 'playing'
                    },
                    stop: function() {}
                };
                
                dd.transend = dojo.hitch(this, 'onCloseEnd');
                dd.addEventListener(this.transitionEnd, dd.transend);
                
                dojo.style(dd, 'height', dojo.marginBox(dd).h + 'px');
                dojo.removeClass(dd, 'notransition');
                setTimeout(function() {
                    dojo.style(dd, 'height', 0);
                }, 30);
            } else {
                dd.wwanim = dojo.animateProperty({
                    i: i,
                    node: dd,
                    properties: {
                        height: {
                            start: dojo.marginBox(dd).h,
                            end: 0
                        }
                    },
                    duration: this.interval,
                    easing: this.closeeasing,
                    onEnd: dojo.hitch(this, 'onCloseEnd')
                }).play();
            }
            dojo.removeClass(dt, 'opened');
            dojo.removeClass(dd, 'opened');
        },

        onEnd: function() {
            window.accordion.running = false;
        },

        onOpenEnd: function(el) {
            if (this.css3animation) {
                dojo.stopEvent(el);
                el = el.currentTarget;
                el.removeEventListener(this.transitionEnd, el.transend);
                dojo.addClass(el, 'notransition');
            }
            this.opened = el.wwanim.i;
            var dt = this.dts[el.wwanim.i];
            var dd = this.dds[el.wwanim.i];
            dojo.addClass(dt, 'opened');
            dojo.addClass(dd, 'opened');
            this.removeProperty(dd, 'height');
            dojo.removeClass(dt, 'opening');
            dojo.removeClass(dd, 'opening');
            if(this.usecookies){
                dojo.cookie(this.instance + '-' + this.getNavClass(dt), 1, {
                    expires: 1
                });
            }
            this.onEnd();
        },

        onCloseEnd: function(el) {
            if (this.css3animation) {
                dojo.stopEvent(el);
                el = el.currentTarget;
                el.removeEventListener(this.transitionEnd, el.transend);
                dojo.style(el, this.transitionProperty, 'none');
            }
            var dt = this.dts[el.wwanim.i];
            var dd = this.dds[el.wwanim.i];
            dojo.addClass(dt, 'closed');
            dojo.addClass(dd, 'closed');
            dojo.removeClass(dt, 'closing');
            dojo.removeClass(dd, 'closing');
            if(this.usecookies){
                dojo.cookie(this.instance + '-' + this.getNavClass(dt), 1, {
                    expires: -1
                });
            }
            this.onEnd();
        },

        getNavClass: function(el) {
            return this.classPattern.exec(dojo.attr(el, 'class'))[0];
        },
        
        removeProperty: function(el, prop){
            if (el.style.removeAttribute) {
                el.style.removeAttribute(prop);
            } else {
                el.style.removeProperty(prop);
            }
        }

    });
})(ndojo);