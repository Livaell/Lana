<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.radio');

class NextendElementTextalign extends NextendElementRadio {
    
    function fetchElement() {
        
        $left = $this->_xml->addChild('option', 'Left');
        $left->addAttribute('value', 'left');
        $center = $this->_xml->addChild('option', 'Center');
        $center->addAttribute('value', 'center');
        $right = $this->_xml->addChild('option', 'Right');
        $right->addAttribute('value', 'right');
        $justify = $this->_xml->addChild('option', 'Justify');
        $justify->addAttribute('value', 'justify');
        
        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/textalign.css');
        
        $html = "<div class='nextend-textalign' style='".NextendXmlGetAttribute($this->_xml, 'style')."'>";
        $html.= parent::fetchElement();
        $html.= '</div>';
        return $html;
    }
    
    function generateOptions(&$xml) {

        $this->_values = array();
        $html = '';
        foreach($xml->option AS $option) {
            $v = NextendXmlGetAttribute($option, 'value');
            $this->_values[] = $v;
            $html.= '<div class="nextend-radio-option nextend-text-align-'. $v . $this->isSelected($v) . ' gk_hack"><div class="gk_hack"></div></div>';
        }
        return $html;
    }
}