<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.form.element.list');

class NextendElementJoomshoppingmenuitems extends NextendElementList {

    function fetchElement() {

        $db = JFactory::getDBO();
        $lang = JFactory::getLanguage()->getTag();
        
        $query = "SELECT m.category_id AS id, `name_$lang` AS title, `name_$lang` AS name, m.category_parent_id AS parent_id, m.category_parent_id as parent
              FROM #__jshopping_categories AS m
              LEFT JOIN #__jshopping_products_to_categories AS f
              ON m.category_id = f.category_id
              WHERE m.category_publish = 1
              ORDER BY ordering";

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
                $optgroup->addChild('option', htmlspecialchars($option->treename))->addAttribute('value', $option->id);
            }
        }
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $html = parent::fetchElement();
        return $html;
    }

}
