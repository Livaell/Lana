<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.hidden');

class NextendElementSwitcher extends NextendElement {
    
    function fetchElement() {

        $this->_values = array();
        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/switcher.css');
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/switcher.js');
        $html = "";
        $html.= "<div class='nextend-switcher' style='".NextendXmlGetAttribute($this->_xml, 'style')."'>";
        $html.= "<div class='nextend-switcher-vertical'>";
        $html.= "<div class='nextend-switcher-vertical-dot'>";
        $html.= "</div>";
        $html.= "</div>";
        $html.= "<div class='nextend-switcher-units'>";
        foreach($this->_xml->unit AS $unit) {
            $this->_values[] = (string)$unit->attributes()->value;
            $html.= "<span class='nextend-switcher-units-unit'>";
            $html.= (string)$unit;
            $html.= "</span>";
        }
        $html.= "</div>";
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $hidden = new NextendElementHidden($this->_form, $this->_tab, $this->_xml);
        $hiddenhtml = $hidden->render($this->control_name);
        $html.= $hiddenhtml[1];
        $html.= "</div>";
        $js->addLibraryJs('dojo', '
            new NextendElementSwitcher({
              hidden: "' . $this->_id . '",
              values: ' . json_encode($this->_values) . '
            });
        ');
        return $html;
    }
}
