<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.checkbox');

class NextendElementDecoration extends NextendElementCheckbox {
    
    function fetchElement() {
        
        $bold = $this->_xml->addChild('option', 'Bold');
        $bold->addAttribute('value', 'bold');
        $italic = $this->_xml->addChild('option', 'Italic');
        $italic->addAttribute('value', 'italic');
        $underline = $this->_xml->addChild('option', 'Underline');
        $underline->addAttribute('value', 'underline');
        
        $html = "<div class='nextend-decoration' style='".NextendXmlGetAttribute($this->_xml, 'style')."'>";
        $html.= parent::fetchElement();
        $html.= '</div>';
        
        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/decoration.css');
        return $html;
    }
    
    function generateOptions(&$xml) {

        $this->_values = array();
        $html = '';
        foreach($xml->option AS $option) {
            $v = NextendXmlGetAttribute($option, 'value');
            $this->_values[] = $v;
            $html.= '<div class="nextend-checkbox-option nextend-decoration-'. $v . $this->isSelected($v) . ' gk_hack"><div class="gk_hack"></div></div>';
        }
        return $html;
    }
}