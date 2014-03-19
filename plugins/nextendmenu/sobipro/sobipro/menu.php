<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.accordionmenu.joomla.treebase');

if(!defined("SOBIPRO")){
    defined( 'DS' ) || define( 'DS', DIRECTORY_SEPARATOR );
    defined( 'SOBI_CMS' ) || define( 'SOBI_CMS', version_compare( JVERSION, '3.0.0', 'ge' ) ? 'joomla3' : ( version_compare( JVERSION, '1.6.0', 'ge' ) ? 'joomla16' : 'joomla15'  ) );
    defined( 'SOBIPRO' ) || define( 'SOBIPRO', true );
    defined( 'SOBI_TASK' ) || define( 'SOBI_TASK', 'task' );
    if( SOBI_CMS == 'joomla15') {
    	defined( 'SOBI_DEFLANG' ) || define( 'SOBI_DEFLANG', JFactory::getConfig()->getValue( 'config.language' ) );
    }
    else {
    	defined( 'SOBI_DEFLANG' ) || define( 'SOBI_DEFLANG', JFactory::getConfig()->get( 'language', JFactory::getConfig()->get( 'config.language' ) ) );
    }
    defined( 'SOBI_ACL' ) || define( 'SOBI_ACL', 'front' );
    defined( 'SOBI_ROOT' ) || define( 'SOBI_ROOT', JPATH_ROOT );
    defined( 'SOBI_MEDIA' ) || define( 'SOBI_MEDIA', implode( DS, array( JPATH_ROOT, 'media', 'sobipro' ) ) );
    defined( 'SOBI_MEDIA_LIVE' ) || define( 'SOBI_MEDIA_LIVE', JURI::root().'/media/sobipro' );
    defined( 'SOBI_PATH' ) || define( 'SOBI_PATH', SOBI_ROOT.'/components/com_sobipro' );
    defined( 'SOBI_LIVE_PATH' ) || define( 'SOBI_LIVE_PATH', 'components/com_sobipro' );
    require_once ( SOBI_PATH.'/lib/base/fs/loader.php' );
    
    SPLoader::loadClass( 'base.const' );
    SPLoader::loadClass( 'base.factory' );
    SPLoader::loadClass( 'base.object' );
    SPLoader::loadClass( 'base.filter' );
    SPLoader::loadClass( 'base.request' );
    SPLoader::loadClass( 'sobi' );
    SPLoader::loadClass( 'base.config' );
    SPLoader::loadClass( 'cms.base.lang' );
}

class NextendTreeSobipro extends NextendTreebaseJoomla {

    function NextendTreeSobipro(&$menu, &$module, &$data) {
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

        $this->_config['emptycategory'] = intval($this->_data->get('emptycategory', 1));

        $this->_config['order'] = $this->_data->get('order', '0');
    }

    function getAllItems() {
        $db = JFactory::getDBO();

        $query = "SELECT DISTINCT 
            a.id AS id, 
            a.name AS name,
            ";
        if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
            $query.="IF(a.parent = " . $this->_config['root'][0] . ", 0 , IF(a.parent = 0, -1, a.parent)) AS parent, ";
        } else if (!in_array('0', $this->_config['root'])) {
            $query.="IF(a.id in (" . implode(',', $this->_config['root']) . "), 0 , IF(a.parent = 0, -1, a.parent)) AS parent, ";
        } else {
            $query.="a.parent AS parent, ";
        }
        $query.="a.oType AS typ, ";

        if ($this->_config['displaynum'] || !$this->_config['emptycategory']) {
            $query.= "(SELECT COUNT(*) FROM #__sobipro_object AS ax LEFT JOIN #__sobipro_relations AS axn ON ax.id = axn.id WHERE axn.pid = a.id AND ax.approved = 1 AND ax.oType = 'entry' ";
            $query.= ") AS productnum";
        } else {
            $query.= "0 AS productnum";
        }

        $query.= " FROM #__sobipro_object AS a
                WHERE a.oType IN ('section', 'category') AND a.approved = 1 ";
                
        if ($this->_config['order'] == "asc") {
            $query.="ORDER BY a.name ASC";
        } else if ($this->_config['order'] == "desc") {
            $query.="ORDER BY a.name DESC";
        } else {
            $query.="ORDER BY a.name ASC";
        }

        $db->setQuery($query);
        $allItems = $db->loadObjectList('id');
        if ($this->_config['showproducts']) {

            $query = "
                SELECT 
                    concat(b.pid,'-',a.id) AS id, 
                    a.id AS id2,
                    c.baseData AS name,
                    b.pid AS parent, 
                    'entry' AS typ, 
                    0 AS productnum
                FROM #__sobipro_object AS a
                LEFT JOIN #__sobipro_relations AS b ON a.id = b.id
                LEFT JOIN #__sobipro_field_data AS c ON a.id = c.sid 
                LEFT JOIN #__sobipro_field AS d ON c.fid = d.fid 
                WHERE a.approved = 1 AND a.oType = 'entry' AND d.nid = 'field_name' ";

            if ($this->_config['order'] == "asc") {
                $query.="ORDER BY a.name ASC";
            } else if ($this->_config['order'] == "desc") {
                $query.="ORDER BY a.name DESC";
            } else {
                $query.="ORDER BY a.name ASC";
            }

            $db->setQuery($query);
            $rows = $db->loadObjectList('id');
            $allItems += $rows;
        }
        return $allItems;
    }

    function getActiveItem() {
        $db = JFactory::getDBO();
        $active = null;
        if (JRequest::getVar('option') == 'com_sobipro') {
            $content_id = 0;
            $category_id = JRequest::getInt('sid');
            $pid = JRequest::getInt('pid', -1);
            if($pid != -1){
                $content_id = $category_id;
                $category_id = $pid;
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
                if ($items[$i]->productnum == 0 && $items[$i]->typ == 'category') {

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


        if ($item->typ == 'section') {
            if (!$this->_config['parentlink'] && $item->p) {
                $item->nname = '<a>' . $item->nname . '</a>';
            } else {
                $item->nname = '<a href="' .
                        Sobi::Url( array( 'sid' => $item->id ) ) . '">' .
                        $item->nname .
                        '</a>';
            }
        }else if ($item->typ == 'category') {
            if (!$this->_config['parentlink'] && $item->p) {
                $item->nname = '<a>' . $item->nname . '</a>';
            } else {
                $item->nname = '<a href="' .
                        Sobi::Url( array( 'sid' => $item->id, 'title' => Sobi::Cfg( 'sef.alias', true ) ? strtolower(SPLang::nid( $item->name, true )) : $category->get( 'name' ) ) ) . '">' .
                        $item->nname .
                        '</a>';
            }
        } else if ($item->typ == 'entry') {
            $id = explode("-", $item->id);
            $item->nname = '<a href="' .
                    Sobi::Url( array( 'pid' => $item->parent->id, 'sid' => $item->id2, 'title' => Sobi::Cfg( 'sef.alias', true ) ? strtolower(SPLang::nid( $item->name, true )) : $item->name ), false, true, true ) . '">' .
                    $item->nname .
                    '</a>';
        }
    }

}

