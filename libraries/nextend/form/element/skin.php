<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.list');

class NextendElementSkin extends NextendElementList {
    
    function fetchElement() {

        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/skin.js');
        $html = parent::fetchElement();
        
        $js->addLibraryJs('dojo', '
            new NextendElementSkin({
              hidden: "' . $this->_id . '",
              preid: "'.str_replace($this->parent->_name,'',$this->parent->_id).'",
              skins: '.json_encode($this->skins).'
            });
        ');
        return $html;
    }
    
    function generateOptions(&$xml){
        $html = '';
        $html.= '<option value="0" selected="selected">Choose</option>';
        $this->skins = array();
        foreach($this->_xml->children() as $skin) {
            $v = $skin->getName();
            $html.= '<option value="'.$v.'">'.NextendXmlGetAttribute($skin, 'label').'</option>';
            $this->skins[$v] = array();
            foreach($skin as $param) {
                $this->skins[$v][$param->getName()] = (string)$param;
            }
        }
        return $html;
    }
}
