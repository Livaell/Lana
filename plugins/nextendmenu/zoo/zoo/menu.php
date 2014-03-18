<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.accordionmenu.joomla.treebase');

require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_zoo'.DIRECTORY_SEPARATOR.'config.php');

class NextendTreeZoo extends NextendTreebaseJoomla {

    function NextendTreeZoo(&$menu, &$module, &$data) {
        parent::NextendTreebase($menu, $module, $data);
        $this->initConfig();
    }

    function initConfig() {
        
        $this->zoo = $zoo = App::getInstance('zoo');
        $zoo->loader->register('CategoryModuleHelper', 'helpers:helper.php');
        
        parent::initConfig();
        
        $expl = explode('|*|', $this->_data->get('zoomenu', '1|*|0'));
        $this->_config['app'] = intval($expl[0]);
        $this->app = $zoo->table->application->get($this->_config['app'])->app;
        $this->_config['root'] = explode('||', $expl[1]);

        $this->_config['showproducts'] = intval($this->_data->get('showproducts', 0));

        $this->_config['emptycategory'] = intval($this->_data->get('emptycategory', '1'));

        $this->_config['order'] = $this->_data->get('order', '0');

        $this->initMenuicon();
    }

    function getAllItems() {
        $allItems = array();
        $user = JFactory::getUser();
        $this->_catobj = $this->app->table->category->getAll($this->_config['app'], true, $this->_config['displaynum'] || !$this->_config['emptycategory'], $user);
        foreach ($this->_catobj as $k => $v) {
            $allItems[$k] = clone $v;
        }
        
        $keys = array_keys($allItems);
        if(!in_array('0', $this->_config['root'])){
          if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
            for($i = 0; $i < count($keys); $i++){
              if($allItems[$keys[$i]]->parent == 0){
                $allItems[$keys[$i]]->parent = -1;
              }
              if($allItems[$keys[$i]]->parent == $this->_config['root'][0]){
                $allItems[$keys[$i]]->parent = 0;
              }
            }
          } else {
            for($i = 0; $i < count($keys); $i++){
              if($allItems[$keys[$i]]->parent == 0){
                $allItems[$keys[$i]]->parent = -1;
              }
            }
            foreach($this->_config['root'] AS $r){
              $allItems[$r]->parent = 0;
            }
          }
        }
        
        if($this->_config['displaynum'] || !$this->_config['emptycategory']){
            for($i = 0; $i < count($keys); $i++){
                $allItems[$keys[$i]]->productnum = count($allItems[$keys[$i]]->item_ids);
            }
        }
        
        return $allItems;
    }

    function getActiveItem() {
        $active = null;
    		if (!$category_id = (int) $this->app->request->getInt('category_id', $this->app->system->application->getParams()->get('category'))) {
    			if ($item = $this->app->table->item->get((int) $this->app->request->getInt('item_id', $this->app->system->application->getParams()->get('item_id', 0)))) {
    				$category_id = $item->getPrimaryCategoryId();
    			}
    		}
        if($category_id != 0){
            $active = new StdClass();
            $active->id = $category_id;
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

        if (!$this->_config['parentlink'] && $item->p) {
            $item->nname = '<a>' . $item->nname . '</a>';
        } else {
            $url = $this->app->route->category($this->_catobj[$item->id], true, '');
            $item->nname = '<a href="' . $url . '">' . $item->nname . '</a>';
        }
    }

}

