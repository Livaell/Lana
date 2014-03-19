<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.subform');

class NextendElementMenutheme extends NextendElementSubform {
    var $_list = null;
    function getOptions(){
        $this->loadList();
        $list = array_keys($this->_list);
        sort($list);
        if(!in_array($this->_value, $list)) $this->_value = $list[0];
        return $list;
    }
    
    function getSubFormfolder($value){
        $this->loadList();
        if(!isset($this->_list[$value])){
            $keys = array_keys($this->_list);
            return $this->_list[$keys[0]];
        }
        return $this->_list[$value];
    }
    
    function loadList(){
        if($this->_list == null){
            if(nextendIsJoomla()){
                $this->loadJoomlaList();
            }else if(nextendIsMagento()){
                $this->loadMagentoList();
            }else if(nextendIsWordpress()){
                $this->loadWordpressList();
            }
        }
    }
    
    function loadJoomlaList(){
        JPluginHelper::importPlugin( 'nextendmenutheme' );
        $dispatcher = JDispatcher::getInstance();
        $this->_list = array();
        $results = $dispatcher->trigger( 'onNextendMenuThemeList', array(&$this->_list));
    }
    
    function loadMagentoList(){
        nextendimport('nextend.accordionmenu.themes.loadplugin');
        nextendimport('nextend.plugin.plugin');
        $this->_list = array();
        NextendPlugin::callPlugin('nextendmenutheme', 'onNextendMenuThemeList', $this->_list);
    }
    
    function loadWordpressList(){
        require_once(NEXTEND_ACCORDION_MENU . 'themes' . DIRECTORY_SEPARATOR . 'loadplugin.php');
        nextendimport('nextend.plugin.plugin');
        $this->_list = array();
        NextendPlugin::callPlugin('nextendmenutheme', 'onNextendMenuThemeList', $this->_list);
    }
}