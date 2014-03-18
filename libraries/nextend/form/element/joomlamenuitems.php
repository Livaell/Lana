<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.form.element.list');

class NextendElementJoomlamenuitems extends NextendElementList {

    var $_menutype = 'mainmenu';

    function fetchElement() {
        $menu = explode('|*|', $this->parent->_value);
        $this->_menutype = $menu[0];
        $db = JFactory::getDBO();
        $where = ' WHERE menutype = ' . $db->Quote($this->_menutype);
        $where.= ' AND published = 1 ';
        if (version_compare(JVERSION, '3.0.0', 'ge')) {
            $query = 'SELECT id, parent_id, parent_id as parent, title, menutype, type' . ' FROM #__menu' . $where . ' ORDER BY menutype, lft, parent_id';
        } elseif (version_compare(JVERSION, '1.6.0', 'ge')) {
            $query = 'SELECT id, parent_id, parent_id as parent, title, menutype, type' . ' FROM #__menu' . $where . ' ORDER BY menutype, lft, parent_id, ordering';
        } else {
            $query = 'SELECT id, parent AS parent_id, parent, name, menutype, type' . ' FROM #__menu' . $where . ' ORDER BY menutype, parent, ordering';
        }
        $db->setQuery($query);
        $menuItems = $db->loadObjectList();

        $children = array();
        if ($menuItems) {
            foreach ($menuItems as $v) {
                $pt = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', version_compare(JVERSION, '1.6.0', 'ge') ? 1 : 0, '', array(), $children, 9999, 0, 0);
        $this->_xml->addChild('option', 'Root')->addAttribute('value', 0);
        if (count($options)) {
            foreach ($options AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->treename))->addAttribute('value', $option->id);
            }
        }
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $html = parent::fetchElement();
        return $html;
    }

}
