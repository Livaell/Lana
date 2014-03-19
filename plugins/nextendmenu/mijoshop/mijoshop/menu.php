<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.accordionmenu.joomla.treebase');

require_once(JPATH_ROOT.'/components/com_mijoshop/mijoshop/mijoshop.php');

class NextendTreeMijoshop extends NextendTreebaseJoomla {

    function NextendTreeMijoshop(&$menu, &$module, &$data) {
        parent::NextendTreebase($menu, $module, $data);
        $this->initConfig();
    }

    function initConfig() {

        parent::initConfig();
        
        $config = MijoShop::get('opencart')->get('config');
        $this->_config['lang'] = 1;
        if (is_object($config)) {
            $this->_config['lang'] = intval($config->get('config_language_id'));
        }
        $this->_router = MijoShop::get('router');
        $this->_mijoshopmenu = $this->_router->getMenu();
        $component = JComponentHelper::getComponent('com_mijoshop');
        $this->mijoshopitems = $this->_mijoshopmenu->getItems('component_id', $component->id);
        $this->mijoshopstoreid = MijoShop::get('base')->getStoreId();
        $this->mijoshopitemid = $this->getHomeItemid();
        $this->_config['root'] = explode('||', $this->_data->get('root', '0'));
        if (count($this->_config['root']) == 0) {
            $this->_config['root'] = array(0);
        } $this->_config['showproducts'] = intval($this->_data->get('showproducts', 0));
        $this->_config['emptycategory'] = intval($this->_data->get('emptycategory', '1'));

        $this->_config['order'] = $this->_data->get('order', '0');

        $this->initMenuicon();
    }

    function getAllItems() {
        $db = JFactory::getDBO();
        $query = "SELECT DISTINCT c.category_id AS id, cd.name, '" . $this->mijoshopitemid . "' AS itemid, cd.description,";
        if ($this->_config['displaynum'] || !$this->_config['emptycategory']) {
            $query.= "( SELECT COUNT(*) FROM #__mijoshop_product_to_category AS ax LEFT JOIN #__mijoshop_product AS bp ON ax.product_id = bp.product_id WHERE ax.category_id = c.category_id AND bp.status=1";
            $query.= ") AS productnum, ";
        } else {
            $query.= "0 AS productnum, ";
        } if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
            $query.="IF(c.parent_id = " . $this->_config['root'][0] . ", 0 , IF(c.parent_id = 0, -1, c.parent_id)) AS parent, ";
        } else if (!in_array('0', $this->_config['root'])) {
            $query.="IF(c.category_id in (" . implode(',', $this->_config['root']) . "), 0 , IF(c.parent_id = 0, -1, c.parent_id)) AS parent, ";
        } else {
            $query.="c.parent_id AS parent, ";
        } $query.="'cat' AS typ ";
        $query.= " FROM #__mijoshop_category AS c LEFT JOIN #__mijoshop_category_description AS cd ON cd.category_id = c.category_id WHERE c.status = 1 AND cd.language_id = " . $this->_config['lang'] . " ";
        if ($this->_config['order'] == "asc") {
            $query.="ORDER BY cd.name ASC";
        } else if ($this->_config['order'] == "desc") {
            $query.="ORDER BY cd.name DESC";
        } else {
            $query.="ORDER BY sort_order ASC, cd.name ASC";
        } $db->setQuery($query);
        $allItems = $db->loadObjectList('id');

        if ($this->_config['showproducts']) {
            $query = " SELECT DISTINCT p.product_id, '" . $this->mijoshopitemid . "' AS itemid, pd.description AS description, concat( pc.category_id, '-', p.product_id ) AS id, pd.name, pc.category_id AS parent, 'prod' AS typ, 0 AS productnum FROM #__mijoshop_product AS p LEFT JOIN #__mijoshop_product_description AS pd ON p.product_id = pd.product_id LEFT JOIN #__mijoshop_product_to_category AS pc ON p.product_id = pc.product_id WHERE p.status = 1 AND pd.language_id = " . $this->_config['lang'] . " ";
            if ($this->_config['order'] == "desc") {
                $query.="ORDER BY pd.name DESC";
            } else {
                $query.="ORDER BY pd.name ASC";
            } $db->setQuery($query);
            $allItems += $db->loadObjectList('id');
        }
        
        return $allItems;
    }

    function getActiveItem() {
        $active = null;
        if (JRequest::getVar('option') == 'com_mijoshop') {
            $product_id = 0;
            $category_id = 0;
            if (JRequest::getVar('route') == 'product/category' && JRequest::getVar('path') != '') {
                $cats = explode('_', JRequest::getVar('path'));
                $category_id = $cats[count($cats) - 1];
            } else if (JRequest::getVar('route') == 'product/product' && JRequest::getInt('product_id') > 0) {
                $product_id = JRequest::getInt('product_id');
                $db = JFactory::getDBO();
                $db->setQuery('SELECT category_id FROM #__mijoshop_product_to_category WHERE product_id = "' . $product_id . '"');
                $categories = $db->loadRowList();
                foreach ($categories AS $c) {
                    if (isset($this->allItems[$c[0] . '-' . $product_id])) {
                        $category_id = $c[0];
                        break;
                    }
                }
            } if ($product_id > 0 && $this->_config['showproducts']) {
                $active = new StdClass();
                $active->id = $category_id . "-" . $product_id;
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
        } if (!$this->_config['emptycategory']) {
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
                $item->itemid = $this->getCategoryItemid($item);
                $item->nname = '<a href="'.$this->route('index.php?option=com_mijoshop&route=product/category&path=' . $item->id . '&Itemid=' . $item->itemid) . '">' . $item->nname . '</a>';
//$item->nname = '<a href="' . $this->_router->route('index.php?option=com_mijoshop&route=product/category&path='.$item->id). '">' . $item->nname . '</a>';
            }
        } elseif ($item->typ == 'prod') {
            $id = explode("-", $item->id);
            $item->itemid = $this->getCategoryItemid($item);
            $item->nname = '<a href="'.$this->route('index.php?option=com_mijoshop&route=product/product&product_id=' . $id[1] . '&Itemid=' . $item->itemid) . '">' . $item->nname . '</a>';
//$item->nname = '<a href="' . $this->_router->route('index.php?option=com_mijoshop&route=product/product&product_id='.$id[1]) . '">' . $item->nname . '</a>';
        }
    }
    
    function route($url){
        $url = JRoute::_($url);
        $url = str_replace('&amp;', '&', $url);
        $url = str_replace('component/mijoshop/shop', 'component/mijoshop', $url);
        return $url;
    }

    function getCategoryItemid($it) {
        $menu_id = null;
        foreach ($this->mijoshopitems AS $item) {
            $params = $item->params instanceof JRegistry ? $item->params : $menu->getParams($item->id);

            if ($params->get('mijoshop_store_id', 0) != $this->mijoshopstoreid) {
                continue;
            }
            if ((@$item->query['view'] == 'category')) {
                if (@$item->query['path'] == $it->id) {
                    $menu_id = $item->id;
                    break;
                }
            }
        }
        if (empty($menu_id)) {
            if (empty($it->parent->itemid))
                $it->parent->itemid = $this->mijoshopitemid;
            return $it->parent->itemid;
        }
        return $menu_id;
    }

    function getProductItemid($it) {
        $menu_id = null;
        foreach ($this->mijoshopitems AS $item) {
            $params = $item->params instanceof JRegistry ? $item->params : $menu->getParams($item->id);

            if ($params->get('mijoshop_store_id', 0) != $this->mijoshopstoreid) {
                continue;
            }
            if ((@$item->query['view'] == 'product')) {
                if (@$item->query['path'] == $it->id) {
                    $menu_id = $item->id;
                    break;
                }
            }
        }
        if (empty($menu_id)) {
            if (empty($it->parent->itemid))
                $it->parent->itemid = $this->mijoshopitemid;
            return $it->parent->itemid;
        }
        return $menu_id;
    }

    function getHomeItemid() {
        $home_id = null;
        foreach ($this->mijoshopitems AS $item) {
            $params = $item->params instanceof JRegistry ? $item->params : $menu->getParams($item->id);

            if ($params->get('mijoshop_store_id', 0) != $this->mijoshopstoreid) {
                continue;
            }
            if (@$item->query['view'] == 'home') {
                $home_id = $item->id;
                break;
            }
        }
        return $home_id;
    }

}

