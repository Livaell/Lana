<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.hidden');

class NextendElementOnoff extends NextendElement {
    
    function fetchElement() {

        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/onoff.css');
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/onoff.js');
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $hidden = new NextendElementHidden($this->_form, $this->_tab, $this->_xml);
        $html = "<div class='nextend-onoff clearfix gk_hack" . $this->isOn() . "' style='".NextendXmlGetAttribute($this->_xml, 'style')."'>";
        $hiddenhtml = $hidden->render($this->control_name);
        $html.= $hiddenhtml[1];
        $html.= "</div>";
        $js->addLibraryJs('dojo', '
            new NextendElementOnoff({
              hidden: "' . $this->_id . '"
            });
        ');
        return $html;
    }
    
    function isOn() {

        if ($this->_value) return 'nextend-onoff-on';
        return '';
    }
}
