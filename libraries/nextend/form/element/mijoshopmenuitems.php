<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.form.element.list');

require_once(JPATH_ROOT.'/components/com_mijoshop/mijoshop/mijoshop.php');

class NextendElementMijoshopmenuitems extends NextendElementList {

    function fetchElement() {

        $db = JFactory::getDBO();
        $lang = '';
        $config = MijoShop::get('opencart')->get('config');
        if (is_object($config)) {
            $lang = ' AND cd.language_id = '.$config->get('config_language_id');
        }
        
        $query = 'SELECT 
                    m.category_id AS id, 
                    cd.name AS name, 
                    cd.name AS title, 
                    m.parent_id AS parent, 
                    m.parent_id as parent_id
                FROM #__mijoshop_category m
                LEFT JOIN #__mijoshop_category_description AS cd ON cd.category_id = m.category_id
                WHERE m.status = 1 '.$lang.'
                ORDER BY m.sort_order';
        
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
        $html = parent::fetchElement();
        return $html;
    }

}
