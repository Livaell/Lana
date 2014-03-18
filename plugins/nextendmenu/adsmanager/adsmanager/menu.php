<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.accordionmenu.joomla.treebase');

class NextendTreeAdsmanager extends NextendTreebaseJoomla {

    function NextendTreeAdsmanager(&$menu, &$module, &$data) {
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
        c.id, 
        c.name, 
        c.description,";
        if ($this->_config['displaynum'] || !$this->_config['emptycategory']) {
            $query.= "( SELECT COUNT(*) 
                        FROM #__adsmanager_adcat AS ax 
                        LEFT JOIN #__adsmanager_ads AS bp ON ax.adid = bp.id 
                        WHERE ax.catid = c.id AND bp.published=1";
            $query.= ") AS productnum, ";
        } else {
            $query.= "0 AS productnum, ";
        }
        if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
            $query.="IF(c.parent = " . $this->_config['root'][0] . ", 0 , IF(c.parent = 0, -1, c.parent)) AS parent, ";
        } else if (!in_array('0', $this->_config['root'])) {
            $query.="IF(c.parent in (" . implode(',', $this->_config['root']) . "), 0 , IF(c.parent = 0, -1, c.parent)) AS parent, ";
        } else {
            $query.="c.parent AS parent, ";
        }
        $query.="'cat' AS typ ";
        $query.= " 
                FROM #__adsmanager_categories AS c
                WHERE c.published = 1 ";

        if ($this->_config['order'] == "asc") {
            $query.="ORDER BY c.name ASC";
        } else if ($this->_config['order'] == "desc") {
            $query.="ORDER BY c.name DESC";
        } else {
            $query.="ORDER BY c.ordering ASC, c.name ASC";
        }

        $db->setQuery($query);
        $allItems = $db->loadObjectList('id');

        if ($this->_config['showproducts']) {
            $query = "
                SELECT DISTINCT 
                  a.id,
                  '' AS description, 
                  concat( ac.catid, '-', a.id ) AS id, 
                  a.ad_headline AS name, 
                  ac.catid AS parent, 
                  'prod' AS typ, 
                  0 AS productnum
                FROM #__adsmanager_ads AS a
                LEFT JOIN #__adsmanager_adcat AS ac ON a.id = ac.adid
                WHERE a.published = 1 ";

            if ($this->_config['order'] == "desc") {
                $query.="ORDER BY a.ad_headline DESC";
            } else {
                $query.="ORDER BY a.ad_headline ASC";
            }

            $db->setQuery($query);
            $allItems += $db->loadObjectList('id');
        }
        return $allItems;
    }

    function getActiveItem() {
        $active = null;
        if (JRequest::getVar('option') == 'com_adsmanager') {
            $ad_id = 0;
            $category_id = 0;
            if (JRequest::getInt('catid') > 0) {
                $category_id = JRequest::getInt('catid');
            }
            if(JRequest::getVar('view') == 'details' && JRequest::getInt('id') > 0){
                $ad_id = JRequest::getInt('id');
                $db = JFactory::getDBO();
                $db->setQuery('SELECT catid FROM #__adsmanager_adcat WHERE adid = "'.$ad_id.'"');
                $categories = $db->loadRowList();
                if($this->_config['showproducts']){
                    foreach($categories AS $c){
                        if(isset($this->allItems[$c[0].'-'.$ad_id])){
                          $category_id = $c[0];
                          break;
                        }
                    }
                }else if($category_id == 0){
                    foreach($categories AS $c){
                        if(isset($this->allItems[$c[0]])){
                          $category_id = $c[0];
                          break;
                        }
                    }
                }
            }
            if ($ad_id > 0 && $this->_config['showproducts']) {
                $active = new StdClass();
                $active->id = $category_id . "-" . $ad_id;
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
                $item->nname = '<a href="' . JRoute::_('index.php?option=com_adsmanager&view=list&catid='.$item->id). '">' . $item->nname . '</a>';
            }
        } elseif ($item->typ == 'prod') {
            $id = explode("-", $item->id);
            $item->nname = '<a href="' .  JRoute::_('index.php?option=com_adsmanager&view=details&id='.$id[1].'&catid='.$id[0]) . '">' . $item->nname . '</a>';
        }
    }

}

