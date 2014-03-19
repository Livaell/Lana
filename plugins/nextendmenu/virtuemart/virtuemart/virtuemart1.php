<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

if (!isset($mosConfig_absolute_path)) {
    $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'] = JPATH_SITE;
}

require_once($mosConfig_absolute_path . '/components/com_virtuemart/virtuemart_parser.php');
require_once($mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/ps_product_category.php");

class NextendTreeVirtuemart extends NextendTreebaseJoomla {

    function NextendTreeVirtuemart(&$menu, &$module, &$data) {
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

        $this->_config['sess'] = intval($this->_data->get('sess', 1));

        $this->_config['itemid'] = intval($this->_data->get('itemid', '0'));


        $this->initMenuicon();
    }

    function getAllItems() {

        $db = new ps_DB;

        $query = "
          SELECT DISTINCT 
            a.category_id AS id, 
            a.category_name AS name,
            a.category_description as description, ";

        if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
            $query.="IF(f.category_parent_id = " . $this->_config['root'][0] . ", 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
        } else if (!in_array('0', $this->_config['root'])) {
            $query.="IF(a.category_id in (" . implode(',', $this->_config['root']) . "), 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
        } else {
            $query.="f.category_parent_id AS parent, ";
        }

        $query.="'cat' AS typ, 
            a.category_flypage,";
        if ($this->_config['displaynum'] || !$this->_config['emptycategory']) {
            $query.= "(SELECT COUNT(*) FROM #__{vm}_product_category_xref AS ax LEFT JOIN #__{vm}_product AS bp ON ax.product_id = bp.product_id WHERE ax.category_id = a.category_id AND bp.product_publish = 'Y' " . ((CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") ? ' AND bp.product_in_stock > 0 ' : '');
            if (CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
                $query.= " AND bp.product_in_stock > 0 ";
            }
            $query.= ") AS productnum";
        } else {
            $query.= "0 AS productnum";
        }
        $query.= " FROM #__{vm}_category AS a, #__{vm}_category_xref AS f  
          WHERE a.category_publish='Y' AND a.category_id = f.category_child_id ";

        if ($this->_config['order'] == "asc") {
            $query.="ORDER BY a.category_name ASC";
        } else if ($this->_config['order'] == "desc") {
            $query.="ORDER BY a.category_name DESC";
        } else {
            $query.="ORDER BY f.category_parent_id ASC, a.list_order ASC";
        }

        $db->setQuery($query);
        $allItems = $db->loadObjectList('id');
        /*
          Get products for the categories
         */
        if ($this->_config['showproducts']) {
            $query = "
              SELECT DISTINCT 
                b.product_id, 
                concat(a.category_id,'-',a.product_id) AS id, 
                b.product_name AS name, 
                b.product_desc AS description,
                a.category_id AS parent, 
                'prod' AS typ,
                0 AS productnum
                      FROM #__{vm}_product_category_xref AS a
                      LEFT JOIN #__{vm}_product AS b ON a.product_id = b.product_id 
                      WHERE b.product_parent_id = 0 AND b.product_publish = 'Y'";
            if (CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
                $query.= " AND b.product_in_stock > 0 ";
            }
            if ($this->_config['order'] == "desc") {
                $query.=" ORDER BY name DESC";
            } else {
                $query.=" ORDER BY name ASC";
            }
            $db->setQuery($query);
            $allItems += $db->loadObjectList('id');
        }
        return $allItems;
    }

    function getActiveItem() {
        $active = null;
        $product_id = JRequest::getInt('product_id');
        $category_id = JRequest::getInt('category_id');
        if ($product_id > 0 && $this->_config['showproducts']) {
            if ($category_id > 0) {
                $active = new stdClass();
                $active->id = $category_id . '-' . $product_id;
            } else {
                $ps_product_category = new ps_product_category();
                $active = new stdClass();
                $active->id = $ps_product_category->get_cid($product_id) . '-' . $product_id;
            }
        } else {
            if ($category_id > 0) {
                $active = new stdClass();
                $active->id = $category_id;
            } elseif ($product_id > 0) {
                $ps_product_category = new ps_product_category();
                $active = new stdClass();
                $active->id = $ps_product_category->get_cid($product_id);
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
        global $sess;
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
                $url = 'index.php?page=shop.browse&category_id=' . $item->id;
                if ($this->_config['sess']) {
                    $url = $sess->url($url);
                    $url = JRoute::_($url);
                } else {
                    $url.="&option=com_virtuemart&limitstart=0&Itemid=" . $this->_config['itemid'];
                }
                $item->nname = '<a href="' . $url . '">' . $item->nname . '</a>';
            }
        } elseif ($item->typ == 'prod') {
            $ids = explode('-', $item->id);
            $url = 'index.php?page=shop.product_details&category_id=' . $ids[0] . '&flypage=' . $item->parent->category_flypage . '&product_id=' . $ids[1];
            if ($this->_config['sess']) {
                $url = $sess->url($url);
                $url = JRoute::_($url);
            } else {
                $url.="&option=com_virtuemart&limitstart=0&Itemid=" . $this->_config['itemid'];
            }
            $item->nname = '<a href="' . $url . '">' . $item->nname . '</a>';
        }
    }

}

