<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.accordionmenu.joomla.treebase');

class NextendTreeTienda extends NextendTreebaseJoomla {

    function NextendTreeTienda(&$menu, &$module, &$data) {
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

        $this->initMenuicon();
    }

    function getAllItems() {
        $db = JFactory::getDBO();

        $query = "SELECT DISTINCT 
        category_id AS id, 
        category_name AS name, 
         category_description AS description,";
        if ($this->_config['displaynum'] || !$this->_config['emptycategory']) {
            $query.= "(SELECT COUNT(*) FROM #__tienda_productcategoryxref AS ax LEFT JOIN #__tienda_products AS bp ON ax.product_id = bp.product_id WHERE ax.category_id = id AND bp.product_enabled=1";
            $query.= ") AS productnum, ";
        } else {
            $query.= "0 AS productnum, ";
        }
        if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
            $query.="IF(parent_id = " . $this->_config['root'][0] . ", 0 , IF(parent_id = 0, -1, parent_id)) AS parent, ";
        } else if (!in_array('0', $this->_config['root'])) {
            $query.="IF(id in (" . implode(',', $this->_config['root']) . "), 0 , IF(parent_id = 0, -1, parent_id)) AS parent, ";
        } else {
            $query.="parent_id AS parent, ";
        }
        $query.="'cat' AS typ ";
        $query.= " FROM #__tienda_categories
                WHERE category_enabled = 1 ";


        if ($this->_config['order'] == "asc") {
            $query.="ORDER BY name ASC";
        } else if ($this->_config['order'] == "desc") {
            $query.="ORDER BY name DESC";
        } else {
            $query.="ORDER BY ordering ASC, name ASC";
        }

        $db->setQuery($query);
        $allItems = $db->loadObjectList('id');

        if ($this->_config['showproducts']) {
            $query = "
                SELECT DISTINCT b.product_id,b.product_description AS description, concat( a.category_id, '-', a.product_id ) AS id, b.product_name AS name, a.category_id AS parent, 'prod' AS typ, 0 AS productnum
                FROM #__tienda_productcategoryxref AS a
                LEFT JOIN #__tienda_products AS b ON a.product_id = b.product_id
                WHERE product_enabled = 1 ";

            if ($this->_config['order'] == "desc") {
                $query.="ORDER BY name DESC";
            } else {
                $query.="ORDER BY name ASC";
            }

            $db->setQuery($query);
            $allItems += $db->loadObjectList('id');
        }
        return $allItems;
    }

    function getActiveItem() {
        $active = null;
        if (JRequest::getVar('option') == 'com_tienda') {
            $content_id = 0;
            $category_id = 0;
            if (JRequest::getInt('filter_category') > 0 && JRequest::getInt('id') == 0) {
                $category_id = JRequest::getInt('filter_category');
            } elseif (JRequest::getInt('id') > 0) {
                $content_id = JRequest::getInt('id');
                $category_id = JRequest::getInt('filter_category');
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

        if ($this->_config['menuiconshow'] && $item->description != '') {
            $out = array();
            preg_match('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)))["\'].*?>/i', $item->description, $out);
            if (count($out)) {
                $this->parseIcon($item, JURI::base(true) . '/' . $out[1], '');
            }
        }

        if ($item->typ == 'cat') {
            if (!$this->_config['parentlink'] && $item->p) {
                $item->nname = '<a>' . $item->nname . '</a>';
            } else {
                $item->nname = '<a href="' . JRoute::_('index.php?option=com_tienda&view=products&filter_category=' . $item->id) . '">' . $item->nname . '</a>';
            }
        } elseif ($item->typ == 'prod') {
            $id = explode("-", $item->id);
            $item->nname = '<a href="' . JRoute::_('index.php?option=com_tienda&view=products&task=view&id=' . $id[1] . '&filter_category=' . $id[0] . '&Itemid=') . '">' . $item->nname . '</a>';
        }
    }

}

