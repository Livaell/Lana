<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.accordionmenu.menu');
nextendimport('nextend.data.data');
nextendimport('nextend.parse.parse');

class NextendMenuJoomla extends NextendMenu {

    var $_data;
    var $_module;

    function NextendMenuJoomla(&$module, &$params, $path) {
        parent::NextendMenu($path);
        $this->_data = new NextendData();
        $config = $params->toArray();
        $this->_data->loadArray(version_compare(JVERSION, '1.6.0', 'l') ? $config : $config['config']);
        $this->_module = $module;
        $this->setThemePath();
        $this->setInstance();
    }

    function setInstance() {
        $this->_instance = $this->_module->id;
    }

    function getTreeInstance() {
        $type = $this->_data->get('type', 'joomla');
        JPluginHelper::importPlugin('nextendmenu', $type);
        $class = 'plgNextendMenu' . $type;
        if (!class_exists($class)) {
            echo 'Error in menu type!';
            return false;
        }
        $dispatcher = JDispatcher::getInstance();
        $class = new $class($dispatcher);
        $this->_typepath = $class->getPath();
        require_once($this->_typepath . 'menu.php');
        $class = 'NextendTree' . $type;
        return new $class($this, $this->_module, $this->_data);
    }

    function setThemePath() {
        $theme = $this->_data->get('theme', 'default');
        JPluginHelper::importPlugin('nextendmenutheme', $theme);
        $class = 'plgNextendMenutheme' . $theme;
        if (!class_exists($class)) {
            echo 'Error in menu theme!';
            return false;
        }
        $dispatcher = JDispatcher::getInstance();
        $class = new $class($dispatcher);
        $this->_themePath = $class->getPath();
    }

    function getTitle() {
        return $this->_module->title;
    }

}