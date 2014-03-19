<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.language.language');

class NextendElement {
    
    var $_form;
    
    var $_tab;
    
    var $_xml;
    
    var $_default;
    
    var $_name;
    
    var $_label;
    
    var $_description;
    
    var $_id;
    
    var $_inputname;
    
    function NextendElement(&$form, &$tab, &$xml) {

        $this->_form = $form;
        $this->_tab = $tab;
        $this->_xml = $xml;
    }
    
    function render($control_name = 'params') {
        $this->control_name = $control_name;
        $this->_default = NextendXmlGetAttribute($this->_xml, 'default');
        $this->_name = NextendXmlGetAttribute($this->_xml, 'name');
        $this->_id = $this->generateId($control_name . $this->_name);
        $this->_inputname = $control_name . '[' . $this->_name . ']';
        $this->_label = NextendXmlGetAttribute($this->_xml, 'label');
        $this->_description = NextendXmlGetAttribute($this->_xml, 'description');
        if ($this->_label == '') $this->_label = $this->_name;
        return array(
            $this->fetchTooltip() ,
            $this->fetchElement()
        );
    }
    
    function fetchTooltip() {
        if($this->_label == '-') $this->_label = '';
        $output = '<label id="' . $this->_id . '-lbl" for="' . $this->_id . '"';
        if ($this->_description) {
            $output.= ' class="hasTip" title="' . NextendText::_($this->_label) . '::' . NextendText::_($this->_description) . '">';
        } else {
            $output.= '>';
        }
        $output.= NextendText::_($this->_label) . '</label>';
        return $output;
    }
    
    function fetchNoTooltip() {

        return "";
    }
    
    function fetchElement() {

    }
    
    function generateId($name) {

        return str_replace(array(
            '[x]',
            '[',
            ']',
            '-x-',
            ' '
        ) , array(
            '-x-',
            '',
            '',
            '[x]',
            ''
        ) , $name);
    }
}
