<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.text');

class NextendElementColor extends NextendElement {
    
    function fetchElement() {

        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('spectrum.css');
        $css->addCssLibraryFile('element/color.css');
        
        $js = NextendJavascript::getInstance();
        $js->loadLibrary('jquery');
        $js->addLibraryJsAssetsFile('jquery', 'spectrum.js');
        
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/color.js');
        
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $hidden = new NextendElementText($this->_form, $this->_tab, $this->_xml);
        $html = "<div class='nextend-color' style='".NextendXmlGetAttribute($this->_xml, 'style')."'>";
        $hiddenhtml = $hidden->render($this->control_name);
        $html.= $hiddenhtml[1];
        $html.= "</div>";
        
        $alpha = NextendXmlGetAttribute($this->_xml, 'alpha');
        if($alpha != 1) $alpha = 0;
        $js->addLibraryJs('dojo', '
            new NextendElementColor({
              hidden: "'.$this->_id.'",
              alpha: '.$alpha.'
            });
        ');
        return $html;
    }
}
