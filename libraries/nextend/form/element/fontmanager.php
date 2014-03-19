<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.hidden');
nextendimport('nextend.fonts.fontmanager');

class NextendElementFontmanager extends NextendElement {
    
    function fetchElement() {

        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/fontmanager.css');
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/fontmanager.js');
        
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $hidden = new NextendElementHidden($this->_form, $this->_tab, $this->_xml);
        
        $html = '';
        
        $fontmanager = NextendFontmanager::getInstance();
        
        $fontmanager->_currentform = $this->_form;
        
        $html.= $fontmanager->render();
        
        $html.= '<a id="nextend-'.$this->_name.'-button" class="nextend-font-button" href="#">Font</a>';
        $html.= '<a id="nextend-'.$this->_name.'-button-export" class="nextend-button-css nextend-font-export nextend-element-hastip" title="Export" href="#"></a>';
        $html.= '<a id="nextend-'.$this->_name.'-button-import" class="nextend-button-css nextend-font-import nextend-element-hastip" title="Import" href="#"></a>';
        $html.= '<div id="nextend-'.$this->_name.'-message" class="nextend-message"></div>';
        
        $html.= "<div class='nextend-fontmanager clearfix'>";
        $hiddenhtml = $hidden->render($this->control_name);
        $html.= $hiddenhtml[1];
        $html.= "</div>";
        
        $tabs = explode('|', NextendXmlGetAttribute($this->_xml, 'tabs'));
        
        $js->addLibraryJs('dojo', '
            new NextendElementFontmanager({
                hidden: "'.$this->_id.'",
                button: "nextend-'.$this->_name.'-button",
                importbtn: "nextend-'.$this->_name.'-button-import",
                exportbtn: "nextend-'.$this->_name.'-button-export",
                message: "nextend-'.$this->_name.'-message",
                tabs: '.json_encode($tabs).',
                firsttab: "'.$tabs[0].'"
            });
        ');
        return $html;
    }
}
