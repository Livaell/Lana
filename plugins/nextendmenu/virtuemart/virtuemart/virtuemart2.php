<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

if (!class_exists('VmConfig')) {
    require(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
}
VmConfig::loadConfig();

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

        $this->_config['itemid'] = intval($this->_data->get('itemid', ''));


        $this->initMenuicon();
    }

    function getAllItems() {

        $db = JFactory::getDBO();

        $query = "SELECT DISTINCT 
        a.virtuemart_category_id AS id, 
        a.category_description  AS description, 
        a.category_name AS name, ";

        if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
            $query.="IF(f.category_parent_id = " . $this->_config['root'][0] . ", 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
        } else if (!in_array('0', $this->_config['root'])) {
            $query.="IF(a.virtuemart_category_id in (" . implode(',', $this->_config['root']) . "), 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
        } else {
            $query.="f.category_parent_id AS parent, ";
        }

        $query.="'cat' AS typ, ";
        if ($this->_config['displaynum'] || !$this->_config['emptycategory']) {
            $query.= "(SELECT COUNT(*) FROM #__virtuemart_product_categories AS ax LEFT JOIN #__virtuemart_products AS bp ON ax.virtuemart_product_id = bp.virtuemart_product_id WHERE ax.virtuemart_category_id = a.virtuemart_category_id AND bp.published = 1";
            if (VmConfig::get('check_stock') && Vmconfig::get('show_out_of_stock_products') != '1') {
                $query.= " AND bp.product_in_stock > 0 ";
            }
            $query.= ") AS productnum";
        } else {
            $query.= "0 AS productnum";
        }
        $query.= " FROM #__virtuemart_categories_" . VMLANG . " AS a
                LEFT JOIN #__virtuemart_category_categories AS f ON a.virtuemart_category_id = f.category_child_id
                LEFT JOIN #__virtuemart_categories AS b ON a.virtuemart_category_id = b.virtuemart_category_id
                WHERE b.published='1' AND a.virtuemart_category_id = f.category_child_id ";
        if ($this->_config['order'] == "asc") {
            $query.="ORDER BY a.category_name ASC";
        } else if ($this->_config['order'] == "desc") {
            $query.="ORDER BY a.category_name DESC";
        } else {
            $query.="ORDER BY b.ordering ASC";
        }
        $db->setQuery($query);
        $allItems = $db->loadObjectList('id');

        /*
          Get products for the categories
         */
        if ($this->_config['showproducts']) {
            $query = "
          SELECT DISTINCT 
            a.virtuemart_product_id, 
            concat(a.virtuemart_category_id,'-',a.virtuemart_product_id) AS id, 
            c.product_name AS name, 
            a.virtuemart_category_id AS parent, 
            'prod' AS typ,
            0 AS productnum
                  FROM #__virtuemart_product_categories AS a
                  LEFT JOIN #__virtuemart_products AS b ON a.virtuemart_product_id = b.virtuemart_product_id 
                  LEFT JOIN #__virtuemart_products_" . VMLANG . " AS c ON a.virtuemart_product_id = c.virtuemart_product_id
                   
                  WHERE b.product_parent_id = 0 AND b.published = '1'";
            if (VmConfig::get('check_stock') && Vmconfig::get('show_out_of_stock_products') != '1') {
                $query.= " AND b.product_in_stock > 0 ";
            }
            
            if ($this->_config['order'] == "asc") {
                $query.="ORDER BY name ASC";
            } else if ($this->_config['order'] == "desc") {
                $query.="ORDER BY name DESC";
            } else {
                $query.="ORDER BY a.ordering ASC";
            }
            
            $db->setQuery($query);
            $allItems += $db->loadObjectList('id');
        }
        return $allItems;
    }

    function getActiveItem() {
        $active = null;
        $product_id = JRequest::getInt('virtuemart_product_id');
        $category_id = JRequest::getInt('virtuemart_category_id');
        if ($product_id > 0 && $this->_config['showproducts']) {
            if ($category_id > 0) {
                $active = new stdClass();
                $active->id = $category_id . '-' . $product_id;
            } else {
                $active = new stdClass();
                $productModel = new VirtueMartModelProduct();
                $r = $productModel->getProductSingle($product_id)->categories;
                if (is_array($r)) {
                    $r = $r[0];
                }
                $active->id = $r . '-' . $product_id;
            }
        } else {
            if ($category_id > 0) {
                $active = new stdClass();
                $active->id = $category_id;
            } elseif ($product_id > 0) {
                $active = new stdClass();
                $productModel = new VirtueMartModelProduct();
                $r = $productModel->getProductSingle($product_id)->categories;
                if (is_array($r)) {
                    $r = $r[0];
                }
                $active->id = $r;
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
                $url = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $item->id);
                if($this->_config['itemid'] !== ''){
                    $url.=(strpos($url, '?')!==false ? '&' : '?')."Itemid=".$this->_config['itemid'];
                }
                $item->nname = '<a href="' . $url . '">' . $item->nname . '</a>';
            }
        } elseif ($item->typ == 'prod') {
            $ids = explode('-', $item->id);
            $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_category_id=' . $ids[0] . '&virtuemart_product_id=' . $ids[1]);
            if($this->_config['itemid'] !== ''){
                $url.=(strpos($url, '?')!==false ? '&' : '?')."Itemid=".$this->_config['itemid'];
            }
            $item->nname = '<a href="' . $url . '">' . $item->nname . '</a>';
        }
    }

}

