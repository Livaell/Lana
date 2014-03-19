<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.form.element.list');

class NextendElementSobipromenuitems extends NextendElementList {

    function fetchElement() {

        $db = JFactory::getDBO();
        
        $query = 'SELECT 
            m.*, 
            m.name AS title, 
            m.parent AS parent, 
            m.parent AS parent_id  
            FROM #__sobipro_object m 
            WHERE 
              m.oType IN ("section", "category") AND 
              m.approved = 1 
              ORDER BY m.parent, m.name';

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
        $options = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        $this->_xml->addChild('option', 'Root')->addAttribute('value', 0);
        $optgroup = $this->_xml->addChild('optgroup', '');
        $optgroup->addAttribute('label', 'Categories');
        if (count($options)) {
            foreach ($options AS $option) {
                $optgroup->addChild('option', $option->treename)->addAttribute('value', $option->id);
            }
        }
        $this->_value = $this->_form->get($this->_name, $this->_default);
        return parent::fetchElement();
    }

}
