<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.form.element.subform');

class NextendElementMenutype extends NextendElementSubform {

    var $_list = null;

    function getOptions() {
        $this->loadList();
        $list = array_keys($this->_list);
        sort($list);
        if(!in_array($this->_value, $list)) $this->_value = $list[0];
        return $list;
    }

    function getSubFormfolder($value) {
        $this->loadList();
        return $this->_list[$value];
    }

    function loadList() {
        if ($this->_list == null) {
            if (nextendIsJoomla()) {
                $this->loadJoomlaList();
            } else if (nextendIsMagento()) {
                $this->loadOtherList();
            } else if(nextendIsWordpress()){
                $this->loadWordpressList();
            }
        }
    }

    function loadJoomlaList() {
        JPluginHelper::importPlugin('nextendmenu');
        $dispatcher = JDispatcher::getInstance();
        $this->_list = array();
        $results = $dispatcher->trigger('onNextendMenuList', array(&$this->_list));

        $limit = NextendXmlGetAttribute($this->_xml, 'limit');
        if (isset($this->_list[$limit])) {
            $tmp = $this->_list[$limit];
            $this->_list = array();
            $this->_list[$limit] = $tmp;
        }

        if (!isset($this->_list[$this->_value])) {
            $keys = array_keys($this->_list);
            $this->_value = $keys[0];
        }
    }

    function loadOtherList() {
        nextendimport('nextend.accordionmenu.types.loadplugin');
        nextendimport('nextend.plugin.plugin');
        $this->_list = array();
        NextendPlugin::callPlugin('nextendmenu', 'onNextendMenuList', $this->_list);
    }

    function loadWordpressList() {
        nextendimport('nextend.plugin.plugin');
        require_once(NEXTEND_ACCORDION_MENU . 'types' . DIRECTORY_SEPARATOR . 'loadplugin.php');
        $this->_list = array();
        NextendPlugin::callPlugin('nextendmenu', 'onNextendMenuList', $this->_list);
    }

}