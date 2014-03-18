<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
class NextendFontmanager{
    
    var $_form;
    
    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            $instance = new NextendFontmanager();
        }

        return $instance;
    }
    
    function render(){
        if($this->_form) return;
        $html = '';
        
        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('fontmanager.css');
        
        $js = NextendJavascript::getInstance();
        
        nextendimport('nextend.form.form');
        $form = new NextendForm();
        $form->loadXMLFile(dirname(__FILE__).DIRECTORY_SEPARATOR.'fontmanager'.DIRECTORY_SEPARATOR.'form.xml');
        
        $this->_form = &$form;
        
        $js->addLibraryJsLibraryFile('dojo', 'dojo/window.js');
        $js->addLibraryJsAssetsFile('dojo', 'window.js');
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'fontmanager.js');
        
        $id = 'nextend-fontmanager-lightbox-'.md5(time());
        
        $html.= '<div id="'.$id.'" class="gk_hack nextend-window">';
        $html.= '<div class="gk_hack nextend-window-container">';
        $html.= '<div class="gk_hack nextend-topbar"><div class="gk_hack nextend-topbar-logo"></div>';
        
        $manual = $this->_currentform->get('manual');
        if($manual != ""){
          $html.= '<a href="'.$manual.'" target="_blank" class="gk_hack nextend-topbar-button nextend-topbar-manual">Manual</a>';
        }
        
        $support = $this->_currentform->get('support');
        if($support != ""){
          $html.= '<a href="'.$support.'" target="_blank" class="gk_hack nextend-topbar-button nextend-topbar-support">Support</a>';
        }

        $html.= '<div id="nextend-fontmanager-save" class="nextend-window-save"><div class="NextendWindowSave">APPLY</div></div>';
        $html.= '</div>';
        
        $html.= '<div class="gk_hack nextend-window-container-inner">';
        
        $html.= '<fieldset id="nextend-fontmanager-panels" class="gk_hack panelform">';
        $html.= '<div id="nextend-fontmanager-tabs" class="gk_hack"></div>';
        
        $html.= '<div id="nextend-fontmanager-panel" class="gk_hack pane-sliders">';
        
        $html.= '<h3>Font manager</h3>';
        
        ob_start();
        //echo '<form method="post" name="nextend-fontmanager" id="nextend-fontmanager" action="" onsubmit="return false;">';
        $form->render('fontmanager');
        //echo '</form>';
        $html.= ob_get_clean();
        
        $html.= '</div>';
        
        $html.= '<p id="nextend-fontmanager-preview" style="background-color: #d0d0d0;">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>';
        $html.= '<div class="fontcolorpickerinput"><div><input id="nextend-fontmanager-backgroundcolor" value="#d0d0d0" name="nextend-fontmanager-backgroundcolor" /></div></div>';
        $html.= '<div id="nextend-fontmanager-cleartab" style="float:right;">Clear this tab</div>';
        $html.= '</fieldset>';
        $html.= '</div>';
        
        $html.= '</div>';
        $html.= '</div>';
        
        $js->addLibraryJs('dojo', '
            new NextendFontmanager({
              node: "'.$id.'"
            });
        ');
        return $html;
    }
}
?>