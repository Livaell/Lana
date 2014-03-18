<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.hidden');
nextendimport('nextend.form.element.onoff');
nextendimport('nextend.form.element.color');

class NextendElementGradient extends NextendElement {
    
    function fetchElement() {

        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/gradient.css');
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/gradient.js');
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $gradient = explode('-', $this->_value);
        $this->validate($gradient);
        $html = "<div class='nextend-gradient' style='".NextendXmlGetAttribute($this->_xml, 'style')."'>";
        $html.= "<table><tr>";
        $html.= "<td class='nextend-gradient-onoff'>";
        
        $attr = $this->_xml->attributes();
        
        $onoffXml = new SimpleXMLElement('<param type="onoff" default="'.$gradient[0].'" name="'.$attr->name . '_onoff'.'" />');
        $onoff = new NextendElementOnoff($this->_form, $this->_tab, $onoffXml);
        
        $onoffHtml = $onoff->render($this->control_name);
        $html.= $onoffHtml[1];
        $html.= "</td>";
        $html.= "<td class='nextend-gradient-start'>";
        
        $startColorXml = new SimpleXMLElement('<param type="color" default="'.$gradient[1].'" name="'.$attr->name . '_start'.'" />');
        $startColor = new NextendElementColor($this->_form, $this->_tab, $startColorXml);
        
        $startColorHtml = $startColor->render($this->control_name);
        $html.= $startColorHtml[1];
        $html.= "</td>";
        $html.= "<td class='nextend-grandient-bg'>";
        $html.= "</td>";
        $html.= "<td class='nextend-gradient-end'>";
        
        $endColorXml = new SimpleXMLElement('<param type="color" default="'.$gradient[2].'" name="'.$attr->name . '_end'.'" />');
        $endColor = new NextendElementColor($this->_form, $this->_tab, $endColorXml);
        
        $endColorHtml = $endColor->render($this->control_name);
        $html.= $endColorHtml[1];
        $html.= "</td>";
        $html.= "</tr></table>";
        $hidden = new NextendElementHidden($this->_form, $this->_tab, $this->_xml);
        $hiddenhtml = $hidden->render($this->control_name);
        $html.= $hiddenhtml[1];
        $html.= "</div>";
        $alpha = NextendXmlGetAttribute($this->_xml, 'alpha');
        if ($alpha != 1) $alpha = 0;
        $js->addLibraryJs('dojo', '
            new NextendElementGradient({
              hidden: "' . $this->_id . '",
              onoff: "' . $onoff->_id . '",
              start: "' . $startColor->_id . '",
              end: "' . $endColor->_id . '",
              value: "'.$this->_value.'"
            });
        ');
        return $html;
    }
    
    function validate(&$g){
        if(isset($g[0])){
            $g[0] = intval($g[0]); 
        }else{
            $g[0] = 1;
        }
        if(!isset($g[1])){
            $g[1] = '000000'; 
        }
        if(!isset($g[2])){
            $g[2] = '000000'; 
        }
    }
}
