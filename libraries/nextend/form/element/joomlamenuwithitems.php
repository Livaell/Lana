<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.mixed');
nextendimport('nextend.form.element.joomlamenu');
nextendimport('nextend.form.element.joomlamenuitems');

class NextendElementJoomlaMenuWithItems extends NextendElementMixed {
    function fetchElement() {
        
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/menuwithitems.js');
        
        $html = '';
        
        $html.= parent::fetchElement();
        
        $db = JFactory::getDBO();
        $where= ' WHERE published = 1 ';
        if (version_compare(JVERSION, '3.0.0', 'ge')) $query = 'SELECT id, parent_id, parent_id as parent, title, menutype, type' . ' FROM #__menu' . $where . ' ORDER BY menutype, lft, parent_id';
        elseif (version_compare(JVERSION, '1.6.0', 'ge')) $query = 'SELECT id, parent_id, parent_id as parent, title, menutype, type' . ' FROM #__menu' . $where . ' ORDER BY menutype, lft, parent_id, ordering';
        else $query = 'SELECT id, parent AS parent_id, parent, name, menutype, type' . ' FROM #__menu' . $where . ' ORDER BY menutype, parent, ordering';
        $db->setQuery($query);
        $menuItems = $db->loadObjectList();

        $children = array();
        if ($menuItems) {
            foreach($menuItems as $v) {
                $pt = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        jimport( 'joomla.html.html.menu' );
        $options = JHTML::_('menu.treerecurse', 0, '', array() , $children, 9999, 0, 0);
        
        $groupedList = array();
        foreach ($options as $k => $v) {
            $groupedList[$v->menutype][] = array($v->id, $v->treename);
        }
        $js->addLibraryJs('dojo', '
            new NextendElementMenuWithItems({
              hidden: "' . $this->_id . '",
              options: ' . json_encode($groupedList) . ',
              value: "'.$this->_value.'"
            });
        ');
        
        return $html;
    }
}