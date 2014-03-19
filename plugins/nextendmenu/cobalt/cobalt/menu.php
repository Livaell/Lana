<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.accordionmenu.joomla.treebase');

$helper = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';
$helper2 = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'helper.php';
if (is_file($helper)) {
    require_once $helper;
} else if (is_file($helper2)) {
    require_once $helper2;
}
require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'form.php';

class NextendTreeCobalt extends NextendTreebaseJoomla {

    function NextendTreeCobalt(&$menu, &$module, &$data) {
        parent::NextendTreebase($menu, $module, $data);
        $this->initConfig();
    }

    function initConfig() {

        parent::initConfig();

        $this->_config['root'] = $this->_data->get('root', '0');

        $this->_config['showproducts'] = intval($this->_data->get('showproducts', 0));

        $this->_config['emptycategory'] = intval($this->_data->get('emptycategory', '1'));

        $this->_config['order'] = $this->_data->get('order', '0');

        $this->_config['maxitemsincat'] = intval($this->_data->get('maxitemsincat', '20'));

        $this->initMenuicon();
    }

    function getAllItems() {
        $db = JFactory::getDBO();

        $allItems = array();

        if ($this->_config['root'] == 0) {
            $query = "SELECT DISTINCT 
              id, 
              name,
              title,
              alias,
              0 AS parent,
              params,
              'section' AS type
              FROM #__js_res_sections
              WHERE published = 1
              ORDER BY ordering";
            $db->setQuery($query);
            $allItems+= $db->loadObjectList('id');

            $query = "SELECT DISTINCT 
              CONCAT(id,'|',section_id) AS id, 
              path,
              title AS name,
              alias,
              IF(parent_id=1,section_id,CONCAT(parent_id,'|',section_id)) AS parent,
              section_id,
              params,
              'category' AS type
              FROM #__js_res_categories
              WHERE published = 1
              ORDER BY name ASC
              ";

            $db->setQuery($query);
            $allItems+= $db->loadObjectList('id');
        } else {
            $query = "SELECT DISTINCT 
              CONCAT(id,'|',section_id) AS id, 
              path,
              title AS name,
              alias,
              IF(parent_id=1,0,CONCAT(parent_id,'|',section_id)) AS parent,
              section_id,
              params,
              'category' AS type
              FROM #__js_res_categories
              WHERE section_id = '" . Nextendescape($db, $this->_config['root']) . "' AND published = 1
              ORDER BY name ASC
              ";

            $db->setQuery($query);
            $allItems+= $db->loadObjectList('id');
        }

        if ($this->_config['showproducts']) {
            $query = "SELECT DISTINCT 
            id, 
            title AS name,
            params,
            alias,
            user_id,
            section_id,
            type_id,
            categories,
            type_id,
            'record' AS type
            FROM #__js_res_record
            WHERE published = 1
            ORDER BY title ASC
            ";

            $db->setQuery($query);
            $records = $db->loadObjectList();
            foreach ($records AS $r) {
                $parent = '';
                $cat = json_decode($r->categories, true);
                if (is_array($cat) && count($cat) > 0) {
                    $cat = array_keys($cat);
                    $parent.= $cat[0] . '|';
                }
                $parent.= $r->section_id;
                $r->parent = $parent;
                $r->id.= '|' . $parent;
                $allItems[$r->id] = $r;
            }
        }

        return $allItems;
    }

    function getActiveItem() {
        $active = null;
        if (JRequest::getVar('option') == 'com_cobalt') {
            $record_id = 0;
            $category_id = 0;
            $section_id = 0;
            if (JRequest::getVar('view') == 'record' && JRequest::getInt('id')) {
                $record = ItemsStore::getRecord(JRequest::getInt('id'));
                $record_id = $record->id;
                $section_id = $record->section_id;
                $cat = json_decode($record->categories, true);
                if (is_array($cat) && count($cat) > 0) {
                    $cat = array_keys($cat);
                    $category_id = $cat[0];
                }
            } else {
                $section_id = JRequest::getInt('section_id');
                $category_id = JRequest::getInt('cat_id');
            }

            if ($record_id > 0) {
                $active = new stdClass();
                $active->id = $record_id . '|' . $category_id . '|' . $section_id;
            } else if ($category_id > 0) {
                $active = new stdClass();
                $active->id = $category_id . '|' . $section_id;
            } else if ($section_id > 0) {
                $active = new stdClass();
                $active->id = $section_id;
            }
        }
        return $active;
    }

    function getItemsTree() {
        return $this->getItems();
    }

    function filterItem($item) {
        $item->nname = stripslashes($item->name);
        $item->nname = '<span>' . $item->nname . '</span>';

        $params = new JRegistry();
        $params->loadString($item->params);
        $item->params = $params;
        if ($item->type == 'category') {
            $id = explode('|', $item->id);
            $item->id = $id[0];
            $url = Url::records($item->section_id, $item);
        } else if ($item->type == 'section') {
            $url = Url::records($item);
        } else if ($item->type == 'record') {
            $id = explode('|', $item->id);
            $item->id = $id[0];
            $url = Url::record($item);
        }
        $item->nname = '<a href="' . $url . '">' . $item->nname . '</a>';
    }

}

