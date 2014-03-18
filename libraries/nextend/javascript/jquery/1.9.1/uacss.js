  // {{{ win-safari hacks, scratch this,
  // let's just expose platform/browser to css
  (function($)
  {
    var uaMatch = '', prefix = '';

    if (navigator.userAgent.match(/Windows/))
    {
      $('html').addClass('x-win');
    }
    else if (navigator.userAgent.match(/Mac OS X/))
    {
      $('html').addClass('x-mac');
    }
    else if (navigator.userAgent.match(/X11/))
    {
      $('html').addClass('x-x11');
    }

    // browser
    if (navigator.userAgent.match(/Chrome/))
    {
      uaMatch = ' Chrome/';
      prefix = 'x-chrome';
    }
    else if (navigator.userAgent.match(/Safari/))
    {
      uaMatch = ' Version/';
      prefix = 'x-safari';
    }
    else if (navigator.userAgent.match(/Firefox/))
    {
      uaMatch = ' Firefox/';
      prefix = 'x-firefox';
    }
    else if (navigator.userAgent.match(/MSIE/))
    {
      uaMatch = ' MSIE ';
      prefix = 'x-msie';
    }
    // add result prefix as browser class
    if (prefix)
    {
      $('html').addClass(prefix);

      // get major and minor versions
      // reduce, reuse, recycle
      uaMatch = new RegExp(uaMatch+'(\\d+)\.(\\d+)');
      var uaMatch = navigator.userAgent.match(uaMatch);
      if (uaMatch && uaMatch[1])
      {
        // set major only version
        $('html').addClass(prefix+'-'+uaMatch[1]);
        // set major + minor versions
        $('html').addClass(prefix+'-'+uaMatch[1]+'-'+uaMatch[2]);
      }
    }
  })(jQuery);
  // }}}