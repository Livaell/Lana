<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.hidden');

class NextendElementRadio extends NextendElement {
    
    function fetchElement() {

        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/radio.css');
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/radio.js');
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $hidden = new NextendElementHidden($this->_form, $this->_tab, $this->_xml);
        $html = "<div class='nextend-radio clearfix' style='".NextendXmlGetAttribute($this->_xml, 'style')."'>";
        $html.= $this->generateOptions($this->_xml);
        $hiddenhtml = $hidden->render($this->control_name);
        $html.= $hiddenhtml[1];
        $html.= "</div>";
        $js->addLibraryJs('dojo', '
            new NextendElementRadio({
              hidden: "' . $this->_id . '",
              values: ' . json_encode($this->_values) . '
            });
        ');
        return $html;
    }
    
    function generateOptions(&$xml) {

        $this->_values = array();
        $html = '';
        foreach($xml->option AS $option) {
            $v = NextendXmlGetAttribute($option, 'value');
            $this->_values[] = $v;
            $html.= '<div class="nextend-radio-option' . $this->isSelected($v) . '">' . ((string)$option) . '</div>';
        }
        return $html;
    }
    
    function isSelected($value) {

        if ($value == $this->_value) {
            return ' selected';
        }
        return '';
    }
}
