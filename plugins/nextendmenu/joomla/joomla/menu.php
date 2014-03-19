<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

jimport('joomla.application.menu');
nextendimport('nextend.accordionmenu.joomla.treebase');

class NextendTreeJoomla extends NextendTreebaseJoomla {

    var $alias;
    var $parentName;
    var $name;

    function NextendTreeJoomla(&$menu, &$module, &$data) {

        parent::NextendTreebase($menu, $module, $data);
        $this->alias = array();
        if (version_compare(JVERSION, '1.6.0', 'ge')) {
            $this->rootId = 1;
            $this->parentName = 'parent_id';
            $this->name = 'title';
        } else {
            $this->rootId = 0;
            $this->parentName = 'parent';
            $this->name = 'name';
        }
        $this->initConfig();
    }

    function initConfig() {

        parent::initConfig();

        $expl = explode('|*|', $this->_data->get('joomlamenu', 'mainmenu|*|0'));
        $this->_config['menu'] = $expl[0];
        $this->_config['root'] = explode('||', $expl[1]);
        
        $expl = explode('|*|', $this->_data->get('hidebycssclass', '0|*|invisible'));
        $this->_config['hidebycssclassfilter'] = $this->rootId && intval($expl[0]);
        $this->_config['hidebycssclassfilterby'] = $expl[1];

        $this->initMenuicon();
    }

    function getAllItems() {

        $options = array();
        $menu = JMenu::getInstance('site', $options);
        $items = $menu->getMenu();
        $keys = array_keys($items);
        $allItems = array();
        if($this->_config['hidebycssclassfilter'] == 1){
            for ($x = 0; $x < count($keys); $x++) {
                if(strpos($items[$keys[$x]]->params->get('menu-anchor_css'), $this->_config['hidebycssclassfilterby']) !== false) continue;
                $allItems[$keys[$x]] = clone ($items[$keys[$x]]);
            }
        }else{
            for ($x = 0; $x < count($keys); $x++) {
                $allItems[$keys[$x]] = clone ($items[$keys[$x]]);
            }
        }
        return $allItems;
    }

    function getActiveItem() {

        $options = array();
        $menu = JMenu::getInstance('site', $options);
        $active = $menu->getActive();
        if(is_object($active)){
          if (isset($this->alias[$active->id]) && count($this->alias[$active->id]) > 0) {
              $itemid = JRequest::getInt('Itemid');
              foreach($this->alias[$active->id] AS $item){
                  if($item == $itemid || $active->menutype != $this->_config['menu']){
                      $active->id = $item;
                  }
              }
          }
        }
        return $active;
    }

    function getItemsTree() {

        $items = $this->getItems();
        if ($this->_config['displaynum']) {
            for ($i = count($items) - 1; $i >= 0; $i--) {
                if (!property_exists($items[$i]->parent, 'productnum')) {
                    $items[$i]->parent->productnum = 0;
                }
                if (!property_exists($items[$i], 'productnum')) {
                    $items[$i]->productnum = 0;
                    $items[$i]->parent->productnum++;
                } else {
                    $items[$i]->parent->productnum+= $items[$i]->productnum;
                }
            }
        }
        return $items;
    }

    function filterItems() {

        $app = JApplication::getInstance('site');
        $this->helper = array();
        $user = JFactory::getUser();
        $language = null;
        
        if (version_compare(JVERSION, '1.6.0', 'ge')) {
            $aid = $user->getAuthorisedViewLevels();
            if ($app->getLanguageFilter()){
                $language = array(JFactory::getLanguage()->getTag(), '*');
            }
        } else {
            $aid = $user->get('aid');
        }

        $ids = $this->_config['root'];
        if (!in_array(0, $ids) && count($ids) > 0) {

            if (count($ids) == 1 && !$this->_config['rootasitem']) {
                $keys = array_keys($this->allItems);
                $newParent = $ids[0];
                for ($x = 0; $x < count($keys); $x++) {
                    $el = & $this->allItems[$keys[$x]];
                    if ($el->{$this->parentName} == $newParent)
                        $el->{$this->parentName} = $this->rootId;
                    elseif ($el->{$this->parentName} == $this->rootId)
                        $el->{$this->parentName} = - 1;
                }
            } else {
                $keys = array_keys($this->allItems);
                for ($x = 0; $x < count($keys); $x++) {
                    $el = & $this->allItems[$keys[$x]];
                    if (in_array($el->id, $ids))
                        $el->{$this->parentName} = $this->rootId;
                    elseif ($el->{$this->parentName} == $this->rootId)
                        $el->{$this->parentName} = - 1;
                }
            }
        }
        $keys = array_keys($this->allItems);
        for ($x = 0; $x < count($keys); $x++) {
            $item = & $this->allItems[$keys[$x]];
            if (!is_object($item))
                continue;
            $item->parent = $this->rootId && $item->{$this->parentName} == 1 ? 0 : $item->{$this->parentName};
            $this->rootId ? $item->ordering = $x : 0;
            if ($item->menutype == $this->_config['menu'] && (is_array($aid) ? in_array($item->access, $aid) : $item->access <= $aid) && ($language === null || in_array($item->language, $language))){
                $item->p = false; // parent

                $item->fib = false; // First in Branch

                $item->lib = false; // Last in Branch

                if (!property_exists($item, 'opened')) {
                    if ($this->opened == - 1) {
                        $item->opened = true; // Opened
                    } else {
                        $item->opened = false; // Opened
                    }
                }
                $item->active = false; // Active

                $this->helper[$item->parent][] = $item;
                if(!$this->rootId){
                    $item->cparams = new JParameter($item->params);
                }else{
                    $item->cparams = new JRegistry($item->params);
                    $data = $item->cparams->get('data', 0);
                    if($data){
                        $item->cparams = new JRegistry($data);
                    }
                }
                if ($item->type == 'menulink' || $item->type == 'alias') {
                    $itemid = '';
                    if($this->rootId){
                        $itemid = $item->cparams->get('aliasoptions');
                        if($itemid == ''){
                            $data = (array)$item->cparams->get('data');
                            if(isset($data['aliasoptions'])){
                                $itemid = $data['aliasoptions'];
                            }
                        }
                    }else{
                        $itemid = $item->cparams->get('menu_item');
                    }
                    if ($itemid != '' && !isset($this->alias[$itemid]) && isset($this->allItems[$itemid])){
                        if(!isset($this->alias[$itemid])) $this->alias[$itemid] = array();
                        $this->alias[$itemid][] = $item->id;
                        $newItem = $this->allItems[$itemid];
                        $item->link = $newItem->link;
                        $item->type = 'alias';
                        $item->ttype = $newItem->type;
                        $item->itemid = $newItem->id;
                    }else{
                        $item->type = 'separator';
                    }
                }
            }
        }
    }

    function filterItem($item) {
        if(!is_object($item->cparams)){
            if(!$this->rootId){
                $item->cparams = new JParameter($item->params);
            }else{
                $item->cparams = new JRegistry($item->params);
                $data = $item->cparams->get('data', 0);
                if($data){
                    $item->cparams = new JRegistry($data);
                }
            }
        }
        if($item->type == 'alias'){
            $item->type = $item->ttype;
            $item->id = $item->itemid;
        }
        
        $item->nname = '<span>' . $item->{$this->name} . '</span>';

        if ($this->_config['displaynum'] && $item->productnum != 0) {
            $item->nname = $this->renderProductnum($item->productnum).$item->nname;
        }


        $image = $item->cparams->get('menu_image', '-1');
        if ($this->_config['menuiconshow'] && $image != -1) {
            $imageurl = '';
            if ($this->rootId) {
                $imageurl = JURI::base(true) . "/" . $image;
            } else {
                $imageurl = JURI::base(true) . "/images/stories/" . $image;
            }

            $this->parseIcon($item, $imageurl, $item->alias);
        }

        if (!$this->_config['parentlink'] && $item->p) {
            $item->type = 'separator';
        }
        switch ($item->type) {
            case 'separator':
                $item->url = '#';
                return true;
            case 'url':
                if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
                    $item->url = $item->link . '&amp;Itemid=' . $item->id;
                } else {
                    $item->url = $item->link;
                }
                break;
            default:
                $router = JSite::getRouter();
                $item->url = $router->getMode() == JROUTER_MODE_SEF ? 'index.php?Itemid=' . $item->id : $item->link . '&Itemid=' . $item->id;
                break;
        }
        if ($item->url != '') {

            // Handle SSL links
            $iSecure = $item->cparams->def('secure', 0);
            if ($item->home == 1) {
                $item->url = JURI::base();
            } elseif (strcasecmp(substr($item->url, 0, 4), 'http') && (strpos($item->link, 'index.php?') !== false)) {
                $item->url = JRoute::_($item->url, true, $iSecure);
            } else {
                $item->url = str_replace('&', '&amp;', $item->url);
            }
            switch ($item->browserNav) {
                default:
                case 0:

                    // _top
                    $item->nname = '<a href="' . $item->url . '">' . $item->nname . '</a>';
                    break;
                case 1:

                    // _blank
                    $item->nname = '<a href="' . $item->url . '" target="_blank">' . $item->nname . '</a>';
                    break;
                case 2:

                    // window.open
                    $attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes';
                    $link = str_replace('index.php', 'index2.php', $item->url);
                    $item->nname = '<a href="' . $link . '" onclick="window.open(this.href,\'targetWindow\',\'' . $attribs . '\');return false;">' . $item->nname . '</a>';
                    break;
            }
        } else {
            $item->nname = '<a href="#">' . $item->nname . '</a>';
        }
    }

    function menuOrdering(&$a, &$b) {

        if ($a->ordering == $b->ordering) {
            return 0;
        }
        return ($a->ordering < $b->ordering) ? -1 : 1;
    }

}

?>