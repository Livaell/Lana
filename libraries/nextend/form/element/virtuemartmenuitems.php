<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.form.element.list');

class NextendElementVirtuemartmenuitems extends NextendElementList {

    function fetchElement() {
        $vmversion = 0;

        if (!class_exists('VmConfig') && file_exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php')) {
            $vmversion = 2;
        }else if(file_exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'compat.joomla1.5.php')){
            $vmversion = 1;
        }

        $db = JFactory::getDBO();
        $query = '';
        if ($vmversion == 1) {
            require_once (JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'virtuemart_parser.php');
            $query = 'SELECT 
                    a. category_id AS id, 
                    b.category_parent_id AS parent_id, 
                    b.category_parent_id AS parent,
                    a.category_name AS title,
                    a.category_name AS name
                FROM #__vm_category AS a
                LEFT JOIN #__vm_category_xref AS b ON a.category_id = b.category_child_id
                WHERE a.category_publish LIKE "Y"
                ORDER BY a.list_order';
            
        } else if ($vmversion == 2) {
            require(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
            VmConfig::loadConfig();
            $query = 'SELECT a.virtuemart_category_id AS id, b.category_parent_id AS parent_id, b.category_parent_id AS parent, c. category_name AS title '
                    . 'FROM #__virtuemart_categories AS a '
                    . 'LEFT JOIN #__virtuemart_category_categories AS b ON a.virtuemart_category_id = b.category_child_id '
                    . 'LEFT JOIN #__virtuemart_categories_' . VMLANG . ' AS c ON a.virtuemart_category_id = c.virtuemart_category_id '
                    . 'WHERE a.published = 1 '
                    . 'ORDER BY a.ordering';
        } else {
            return "Virtuemart not found!";
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
        $options = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
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
