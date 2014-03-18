<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.hidden');

class NextendElementMixed extends NextendElement {
    
    var $_separator = '|*|';
    
    function fetchElement() {

        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/mixed.css');
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/mixed.js');
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $value = explode($this->_separator, $this->_value);
        $html = "<div class='nextend-mixed' style='".NextendXmlGetAttribute($this->_xml, 'style')."'>";
        $this->_elements = array();
        $i = 0;
        foreach($this->_xml->param AS $element) {
            $html.= "<div class='nextend-mixed-group' style='".NextendXmlGetAttribute($element, 'mixedstyle')."'>";
            $type = NextendXmlGetAttribute($element, 'type');
            nextendimport('nextend.form.element.' . $type);
            $class = 'NextendElement' . strtoupper($type);
            $element->addAttribute('name', $this->_name . '_' . $i);
            if (isset($value[$i])) $element->addAttribute('default', $value[$i]);
            $el = new $class($this->_form, $this, $element);
            $el->parent = &$this;
            $elementHtml = $el->render($this->_name . $this->control_name);
            $html.= "<div class='nextend-mixed-label'>";
            $html.= $elementHtml[0];
            $html.= "</div>";
            $html.= "<div class='nextend-mixed-element'>";
            $html.= $elementHtml[1];
            $html.= "</div>";
            $this->_elements[$i] = $el->_id;
            $i++;
            $html.= "</div>";
        }
        $this->_form->set($this->_name, $this->_value);
        $hidden = new NextendElementHidden($this->_form, $this->_tab, $this->_xml);
        $hiddenhtml = $hidden->render($this->control_name);
        $html.= $hiddenhtml[1];
        $html.= "</div>";
        $js->addLibraryJs('dojo', '
            new NextendElementMixed({
              hidden: "' . $this->_id . '",
              elements: ' . json_encode($this->_elements) . ',
              separator: "' . $this->_separator . '"
            });
        ');
        return $html;
    }
}
