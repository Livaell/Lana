<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element');

class NextendTab {
    
    var $_form;
    
    var $_xml;
    
    var $_name;
    
    var $_attributes;
    
    var $_elements;
    
    function NextendTab(&$form, &$xml) {

        $this->_form = $form;
        $this->_xml = $xml;
        $this->_name = NextendXmlGetAttribute($xml, 'name');
        $this->initElements();
    }
    
    function initElements() {

        $this->_elements = array();
        foreach($this->_xml->param AS $element) {
            $type = NextendXmlGetAttribute($element, 'type');
            $name = NextendXmlGetAttribute($element, 'name');
            nextendimport('nextend.form.element.' . $type);
            $class = 'NextendElement' . ucfirst($type);
            $this->_elements[$name] = new $class($this->_form, $this, $element);
        }
    }
    
    function render($control_name) {

        $this->decorateTitle();
        $this->decorateGroupStart();
        $keys = array_keys($this->_elements);
        for ($i = 0;$i < count($keys);$i++) {
            $this->decorateElement($this->_elements[$keys[$i]], $this->_elements[$keys[$i]]->render($control_name) , $i);
        }
        $this->decorateGroupEnd();
    }
    
    function decorateTitle() {
        echo "<div class='nextend-tab'>";
        echo "<h3>" . NextendXmlGetAttribute($this->_xml, 'label') . "</h3>";
    }
    
    function decorateGroupStart() {

        echo "<table>";
    }
    
    function decorateGroupEnd() {

        echo "</table>";
        echo "</div>";
    }
    
    function decorateElement(&$el, $out, $i) {
        $class = 'odd';
        if ($i % 2) $class = 'even';
        echo "<tr class='" . $class . "'>";
        $title = NextendXmlGetAttribute($el->_xml, 'description');
        $class = '';
        if($title != ''){
            $class = ' nextend-hastip';
            $title= ' title="'.$title.'"';
        }
        echo "<td class='nextend-label".$class."' ".$title.">" . $out[0] . "</td>";
        echo "<td class='nextend-element'>" . $out[1] . "</td>";
        echo "</tr>";
    }
}
?>