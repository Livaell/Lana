<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.accordionmenu.joomla.treebase');

require_once (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_k2'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php');
  
class NextendTreeK2 extends NextendTreebaseJoomla {

    function NextendTreeK2(&$menu, &$module, &$data) {
        parent::NextendTreebase($menu, $module, $data);
        $this->initConfig();
    }

    function initConfig() {

        parent::initConfig();

        $this->_config['root'] = explode('||', $this->_data->get('root', '0'));
        if (count($this->_config['root']) == 0) {
            $this->_config['root'] = array(0);
        }

        $this->_config['showproducts'] = intval($this->_data->get('showproducts', 0));

        $this->_config['emptycategory'] = intval($this->_data->get('emptycategory', '1'));

        $this->_config['order'] = $this->_data->get('order', '0');

        $this->_config['maxitemsincat'] = intval($this->_data->get('maxitemsincat', '20'));

        $this->initMenuicon();
    }

    function getAllItems() {
        $db = JFactory::getDBO();

        $query = "SELECT DISTINCT 
            a.id AS id, 
            a.name AS name,
            a.alias AS alias,
            a.image as image, ";
        if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
            $query.="IF(a.parent = " . $this->_config['root'][0] . ", 0 , IF(a.parent = 0, -1, a.parent)) AS parent, ";
        } else if (!in_array('0', $this->_config['root'])) {
            $query.="IF(a.id in (" . implode(',', $this->_config['root']) . "), 0 , IF(a.parent = 0, -1, a.parent)) AS parent, ";
        } else {
            $query.="a.parent AS parent, ";
        }
        $query.="'cat' AS typ, ";

        if ($this->_config['displaynum'] || !$this->_config['emptycategory']) {
            $query.= "(SELECT COUNT(*) FROM #__k2_items AS ax WHERE ax.catid=a.id AND ax.published = 1 AND ax.trash = 0 ";
            $query.= ") AS productnum";
        } else {
            $query.= "0 AS productnum";
        }

        $query.= " FROM #__k2_categories AS a
                WHERE a.published=1 ";


        if ($this->_config['order'] == "asc") {
            $query.="ORDER BY a.name ASC";
        } else if ($this->_config['order'] == "desc") {
            $query.="ORDER BY a.name DESC";
        } else {
            $query.="ORDER BY a.ordering ASC, a.name DESC";
        }

        $db->setQuery($query);
        $allItems = $db->loadObjectList('id');

        if ($this->_config['showproducts']) {

            $query = "
                SELECT 
                    concat(a.catid,'-',a.id) AS id, 
                    a.id AS id2,
                    a.title AS name, 
                    a.introtext AS description,
                    a.catid AS parent, 
                    a.access, 
                    a.alias, 
                    'prod' AS typ, 
                    0 AS productnum,
                    '' AS image
                FROM #__k2_items AS a
                WHERE a.published = 1 AND a.trash = 0 ";

            if ($this->_config['order'] == "asc") {
                $query.="ORDER BY a.title ASC";
            } else if ($this->_config['order'] == "desc") {
                $query.="ORDER BY a.title DESC";
            } else {
                $query.="ORDER BY a.ordering ASC, a.title DESC";
            }

            $db->setQuery($query);
            $rows = $db->loadObjectList('id');
            if ($this->_config['maxitemsincat'] > 0) {
                $cats = array();
                $keys = array_keys($rows);
                for ($x = 0; $x < count($keys); ++$x) {
                    $value = $rows[$keys[$x]];
                    if (!isset($cats[$value->parent])) {
                        $cats[$value->parent] = 0;
                    }
                    $cats[$value->parent]++;
                    if ($cats[$value->parent] > $this->_config['maxitemsincat']) {
                        unset($rows[$keys[$x]]);
                    }
                }
            }
            $allItems += $rows;
        }
        return $allItems;
    }

    function getActiveItem() {
        $db = JFactory::getDBO();
        $active = null;
        if (JRequest::getVar('option') == 'com_k2') {
            $content_id = 0;
            $category_id = 0;
            if (JRequest::getVar('task') == "category") {
                $category_id = JRequest::getInt('id');
            } elseif (JRequest::getVar('view') == "item") {
                $content_id = JRequest::getInt('id');
                $query = "SELECT catid FROM #__k2_items WHERE id=" . $content_id;
                $db->setQuery($query);
                $category_id = $db->loadResult();
            }
            if ($content_id > 0 && $this->_config['showproducts']) {
                $active = new StdClass();
                $active->id = $category_id . "-" . $content_id;
            } elseif ($category_id > 0) {
                $active = new StdClass();
                $active->id = $category_id;
            }
        }
        return $active;
    }

    function getItemsTree() {
        $items = $this->getItems();
        if ($this->_config['displaynum'] == 2 || !$this->_config['emptycategory']) {
            for ($i = count($items) - 1; $i >= 0; $i--) {
                $items[$i]->parent->productnum+= $items[$i]->productnum;
            }
        }
        if (!$this->_config['emptycategory']) {
            for ($i = count($items) - 1; $i >= 0; $i--) {
                if ($items[$i]->productnum == 0 && $items[$i]->typ == 'cat') {

                    $parent = &$this->helper[$items[$i]->parent->id];

                    if ($items[$i]->lib) {
                        array_splice($parent, count($parent) - 1, 1);
                        if (count($parent) != 0) {
                            $parent[count($parent) - 1]->lib = true;
                        }
                    } else if ($items[$i]->fib) {
                        array_splice($parent, 0, 1);
                        if (count($parent) != 0) {
                            $parent[0]->fib = true;
                        }
                    } else {
                        $key = array_search($items[$i], $parent);
                        if ($key !== false) {
                            array_splice($parent, $key, 1);
                        }
                    }
                    array_splice($items, $i, 1);
                }
            }
        }
        return $items;
    }

    function filterItem($item) {
        $item->nname = stripslashes($item->name);
        $item->nname = '<span>' . $item->nname . '</span>';
        if ($this->_config['displaynum'] && $item->productnum != 0) {
            $item->nname = $this->renderProductnum($item->productnum).$item->nname;
        }

        if ($this->_config['menuiconshow'] && $item->image != '') {
            $this->parseIcon($item, JUri::Root(false) . '/media/k2/categories/' . $item->image, '');
        }

        if ($item->typ == 'cat') {
            if (!$this->_config['parentlink'] && $item->p) {
                $item->nname = '<a>' . $item->nname . '</a>';
            } else {
                $item->nname = '<a href="' .
                        JRoute::_(K2HelperRoute::getCategoryRoute($item->id . ':' . urlencode($item->alias))) . '">' .
                        $item->nname .
                        '</a>';
            }
        } else if ($item->typ == 'prod') {
            $id = explode("-", $item->id);
            $item->nname = '<a href="' .
                    JRoute::_(K2HelperRoute::getItemRoute($id[1] . ':' . urlencode($item->alias), $id[0] . ':' . urlencode($this->helper[$id[0]]->alias))) . '">' .
                    $item->nname .
                    '</a>';
        }
    }

}

